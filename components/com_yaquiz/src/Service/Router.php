<?php

/**
 *
 * @copyright   (C) kevin olson kevinsguides.com
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KevinsGuides\Component\Yaquiz\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Log\Log;


/**
 * Router for Quizzes
 * To keep things simple, let's just do this...
 * site.com/menuitem/[id-or-alias]/[view]/[layout]//[page]
 * The "categories" view is parent of everything else
 */

class Router extends RouterView
{

    public function __construct($app = null, $menu = null)
    {
        Log::add('router construct called');
        parent::__construct($app, $menu);
    }

    public function preprocess($query)
    {
        //find first segment from url
        $segments = $this->app->input->get('segments', [], 'array');

        //if no itemid, we need to guess one
        if (!isset($query['Itemid']) && isset($query['view']) && $query['view'] == 'quiz') {
            Log::add('got to here');
            $id = null;
            if (isset($query['id'])) {
                $id = $query['id'];
            }
            Log::add("quiz id: " . $id, Log::DEBUG, "yaquiz");
            $itemid = $this->getMenuItemIdByQuizId($id);
            if ($itemid) {
                $query['Itemid'] = $itemid;
            }
        }
        return $query;
    }


    public function build(&$query)
    {


 
        $segments = [];
        $app = Factory::getApplication();

        $id = null; //quiz id
        $itemid = null; //menu item id


        if (isset($query['id'])) {
            $id = $query['id'];
        }

        if (isset($query['Itemid'])) {
            $itemid = $query['Itemid'];
        }

        //alias or quiz id
        //will be added if we are not on a menu item matching that id already
        if (isset($query['id'])) {
            //if there is no itemid, we always need to add it
            if (!$itemid) {
                Log::add('id is ' . $id . ' and itemid is ' . $itemid, Log::DEBUG, 'yaquiz');
                //see if we can find an alias
                $alias = $this->getQuizAlias($id);
                if ($alias) {
                    $segments[] = $alias;
                } else {
                    $segments[] = $id;
                }
            }
            //otherwise we need to get the id from the menu item and see if it matches
            else {
                //we want the client menu object, not the admin
                $sitemenu = Factory::getApplication()->getMenu('site');
                $checkitem = $sitemenu->getItem($itemid);
                $checkquizid = isset($checkitem->query['id']) ? $checkitem->query['id'] : 0;

                //if the menu item id doesn't match the quiz id, we need to add the quiz id
                if ($checkquizid != $id) {
                    $alias = $this->getQuizAlias($id);
                    if ($alias) {
                        $segments[] = $alias;
                    } else {
                        $segments[] = $id;
                    }
                }

            }

            unset($query['id']);
        }


        if (isset($query['view'])) {
            //if view is quiz, we can omit it since it's the default
            if ($query['view'] != 'quiz' && $query['view'] != 'categories') {
                $segments[] = $query['view'];
            }
            unset($query['view']);
        }
        if (isset($query['layout'])) {
            $segments[] = $query['layout'];
            unset($query['layout']);
        }


        if (isset($query['page'])) {
            $segments[] = 'p-' . $query['page'];
            unset($query['page']);
        }

        if (isset($query['resultid'])) {
            $segments[] = 'resultid-' . $query['resultid'];
            unset($query['resultid']);
        }

        Log::add('segments: ' . print_r($segments, true), Log::DEBUG, 'yaquiz');

        return $segments;
    }



    public function parse(&$segments)
    {
        Log::add('segments: ' . print_r($segments, true), Log::DEBUG, 'yaquiz');
        $active = $this->menu->getActive();
        $vars = [];
        $app = Factory::getApplication();


            //if the last item is p-# on any view or layout, it's the page number
            if (strpos($segments[count($segments) - 1], 'p-') === 0) {
                $vars['page'] = str_replace('p-', '', $segments[count($segments) - 1]);
            }

            //if first segment is not categories, category, certverify, or user, we can assume it's a quiz
            $nonDefaultViews = ['categories', 'category', 'certverify', 'user'];
            if (!in_array($segments[0], $nonDefaultViews)) {
                $vars['view'] = 'quiz';

                //continue with quiz logic

                //if any segments are numeric, it's the quiz id
                foreach ($segments as $segment) {
                    if (is_numeric($segment)) {
                        $vars['id'] = $segment;
                    }
                }

                //if we haven't found an id yet, see if we can find an alias
                if (!isset($vars['id'])) {
                    $alias = $segments[0];
                    $id = $this->getQuizIdByAlias($alias);
                    if ($id) {
                        $vars['id'] = $id;
                        Log::add('im supposed to be showing you quiz id ' . $id, Log::DEBUG, 'yaquiz');
                    }
                }

                //look at $segment[1] and check against possible layouts (segment 0 is id or alias, 1 is omitted if view is quiz, so 1 is actually 2 - the layout)
                $layouts = ['results', 'quiztype_singlepage', 'quiztype_oneperpage', 'quiztype_jsquiz', 'quiztype_individual', 'max_attempt_reached', 'default'];

                if (isset($segments[1]) && in_array($segments[1], $layouts)) {
                    $vars['layout'] = $segments[1];
                } //if a page is set, layout is automatically 'quiztype_oneperpage'
                elseif (isset($vars['page'])) {
                    $vars['layout'] = 'quiztype_oneperpage';
                } //if segments[0] is results
                elseif ($segments[0] == 'results') {
                    $vars['layout'] = 'results';
                }

                //see if we can find an itemid for menu
                if (isset($vars['id'])) {
                    $vars['Itemid'] = $this->getMenuItemIdByQuizId($vars['id']);
                    Log::add('found menu item id: ' . $vars['Itemid'], Log::DEBUG, 'yaquiz');
                    $app->input->set('Itemid', $vars['Itemid']);
                    
                }
            } else {
                //the first segment is the view
                $vars['view'] = $segments[0];
                //if second segment is set, it's the layout
                if (isset($segments[1])) {
                    $vars['layout'] = $segments[1];
                }

                //if it ends with resultid-#, it's a result
                if (strpos($segments[count($segments) - 1], 'resultid-') === 0) {
                    $vars['resultid'] = str_replace('resultid-', '', $segments[count($segments) - 1]);
                }

                //if view is certverify, check for menu item
                if ($vars['view'] == 'certverify') {
                    $vars['Itemid'] = $this->getCertVerifyMenuItemId();
                }

                //if view is user, getUserResultsMenuItemId
                if ($vars['view'] == 'user') {
                    $vars['Itemid'] = $this->getUserResultsMenuItemId();
                    
                }
            }





            $segments = [];
            Log::add('vars: ' . print_r($vars, true), Log::DEBUG, 'yaquiz');
            return $vars;
        

        $segments = [];
        return $vars;
    }

    public function getQuizId($segment, $query)
    {
        $id = null;
        if (isset($query['id'])) {
            $id = $query['id'];
        } else {
            $input = Factory::getApplication()->input;
            $id = $input->getInt('id', null);
        }
        return $id;
    }


    public function getMenuItemIdByQuizId($quiz_id)
    {

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__menu');
        //where link has com_yaquiz in it
        $query->where('link LIKE "%com_yaquiz%"');
        //where link like id=#
        $query->where('link LIKE "%id=' . $quiz_id . '%"');
        $query->where('published = 1');
        $db->setQuery($query);
        $result = $db->loadResult();
        //if there is a result
        if ($result) {
            return $result;
        } else {
            return $this->getTopmostQuizMenuItemId();
        }
    }



    public function getTopmostQuizMenuItemId()
    {


        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, published, level');
        $query->from('#__menu');
        //get all results where path like com-yaquiz-categories
        $query->where('link LIKE "%com_yaquiz&view=categories%"');
        //where published 1
        $query->where('published = 1');
        //order by level, 1 to highest
        $query->order('level ASC');
        $db->setQuery($query);
        $result = $db->loadResult();

        Log::add('using topmost categories itemid ' . $result, Log::DEBUG, 'yaquiz');
        //if there is a result
        //return the first id
        if ($result) {
            return (int) $result;
        } else {
            return null;
        }
    }


    public function getUserResultsMenuItemId()
    {

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, published, level');
        $query->from('#__menu');
        //get all results where path like com-yaquiz-categories
        $query->where('link LIKE "%com_yaquiz&view=user%"');
        //where published 1
        $query->where('published = 1');
        //order by level, 1 to highest
        $query->order('level ASC');
        $db->setQuery($query);
        $result = $db->loadResult();

        return (int) $result;
    }


    public function getCertVerifyMenuItemId()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id, published, level');
        $query->from('#__menu');
        //get all results
        $query->where('link LIKE "%com_yaquiz&view=certverify%"');
        //where published 1
        $query->where('published = 1');
        //order by level, 1 to highest
        $query->order('level ASC');
        $db->setQuery($query);
        $result = $db->loadResult();

        return (int) $result;
    }


    public function getMenuAliasFromItemid($itemid)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('alias');
        $query->from('#__menu');
        $query->where('id = ' . $itemid);
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }



    public function getMenuAlias($quiz_id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('alias');
        $query->from('#__menu');
        $query->where('link LIKE "%com_yaquiz%"');
        $query->where("params LIKE '%\"id\":\"" . $quiz_id . "\"%'");
        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result) {
            return $result;
        } else {
            return null;
        }

    }


    public function getCategoryAlias($quiz_id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('c.alias');
        $query->from('#__categories c');
        $query->join('INNER', '#__com_yaquiz_quizzes q ON q.catid = c.id');
        $query->where('extension = "com_yaquiz"');
        $query->where('q.id = ' . $quiz_id);
        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result) {
            return $result;
        } else {
            return null;
        }
    }


    public function getQuizAlias($quiz_id)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('alias');
        $query->from('#__com_yaquiz_quizzes');
        $query->where('id = ' . (int) $quiz_id);
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {
            if($this->getQuizAliasCount($result) > 1){
                return $quiz_id;
            }
            return $result;
        }

        return null;
    }


    /**
     * Checks if there are multiple identical quiz alias
     * @param $quiz_alias
     */
    public function getQuizAliasCount($quiz_alias){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from('#__com_yaquiz_quizzes');
        $query->where('alias = "' . $quiz_alias . '"');
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {
            return $result;

        } else {
            return null;
        }
    }

    public function getQuizIdByAlias($alias)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__com_yaquiz_quizzes');
        $query->where('alias = "' . $alias . '"');
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result) {
            return $result;
        } else {
            return null;
        }
    }


    //if a menu item exists directly for this quiz, that is the alias
    //otherwise return categories view menu item alias
    public function getQuizRootAlias($quiz_id)
    {

        if ($this->getMenuAlias($quiz_id)) {
            return $this->getMenuAlias($quiz_id);
        }

        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('alias');
        $query->from('#__menu');
        $query->where('link LIKE "%com_yaquiz&view=categories%"');
        $query->wherE("published = 1");
        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result) {
            return $result;
        } else {
            return null;
        }
    }


    public function getComponentRoute(){
        return Route::_('index.php?option=com_yaquiz', false, -1, 'myalias');
    }
}
