<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_yaquiz
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KevinsGuides\Component\Yaquiz\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;


use Joomla\Database\DatabaseInterface;

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterBase;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;


use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

// phpcs:disable PSR1.Files.SideEffects



// possible views are "quiz" and "user"
// quiz has layouts "default" and "results"
// user has layouts "default" and "singleresult"

//raw route is like: index.php?option=com_yaquiz&view=quiz&id=13

//route should look like this: site.com/component/yaquiz/quiz/quizid


class Router extends RouterBase
{

    public function build(&$query)
    {

        $segments = [];

        if (!isset($query['view'])) {
            return $segments;
        }



        $app = Factory::getApplication();
        $view_from_app_input = $app->input->get('view', null);

        Log::add('build query ' . print_r($query, true), Log::DEBUG, 'yaquiz');
        Log::add('view from app input: ' . $view_from_app_input, Log::DEBUG, 'yaquiz');

        // //if we already have a menu Itemid...
        // if (isset($query['Itemid'])) {
            
        //     $item = $this->menu->getItem($query['Itemid']);

        //     if ($item && $item->component == 'com_yaquiz') {
        //         $segments[] = $item->query['view'];
        //         if (isset($item->query['id'])) {
        //             $segments[] = $item->query['id'];
        //         }
        //         unset($query['view']);
        //         unset($query['id']);
        //         return $segments;
        //     }

        // }


        $segments[] = $query['view'];
        
        unset($query['view']);

        if (isset($query['id'])) {
            $segments[] = $query['id'];
            unset($query['id']);
        }

        if (isset($query['layout'])) {
            $segments[] = $query['layout'];
            unset($query['layout']);
        }

        if (isset($query['resultid'])) {
            $segments[] = $query['resultid'];
            unset($query['resultid']);
        }

        //Log::add('app input look like this: ' . print_r($app->input, true), Log::DEBUG, 'yaquiz');
        Log::add('i built segments: ' . print_r($segments, true), Log::DEBUG, 'yaquiz');
        return $segments;
    }



    public function parse(&$segments)
    {

        Log::add('parse called with segments: ' . print_r($segments, true), Log::DEBUG, 'yaquiz');

        $app = Factory::getApplication();
        $layout = $app->input->get('layout', null);
        $view = $segments[0];

        $Itemid = null;

        $vars = [];

        if ($this->menu->getActive()) {
            $Itemid = $this->menu->getActive()->id;
        }

        $view_from_app_input = $app->input->get('view', 'quiz');
        //if they dont match, use the one from the INPUT
        if ($view_from_app_input != $view) {
            $view = $view_from_app_input;
        }


        //if we have an Itemid, we can use it to get the view and id
        if ($Itemid) {
            Log::add('getting info from menu item id: ' . $Itemid, Log::DEBUG, 'yaquiz');
            $item = $this->menu->getItem($Itemid);
            //Log::add('item: ' . print_r($item, true), Log::DEBUG, 'yaquiz');
            if ($item->component == 'com_yaquiz') {
                Log::add('item component is com_yaquiz', Log::DEBUG, 'yaquiz');

                if ($view === 'quiz') {

                    $active = $this->menu->getActive();

                    $quiz_id = $active->getParams()->get('quiz_id');
                    //if quiz_id is null, we need to get from segments
                    if (!$quiz_id) {
                        $quiz_id = $segments[1];
                    }

                    Log::add('quiz_id: ' . $quiz_id, Log::DEBUG, 'yaquiz');
                    $vars['view'] = $view;
                    $vars['id'] = $quiz_id;
                    if($layout){
                        $vars['layout'] = $layout;
                    }
                    elseif (isset($segments[2])){
                        $vars['layout'] = $segments[2];
                    }
                    else{
                        $vars['layout'] = 'default';
                    }
                    
                    $vars['Itemid'] = $Itemid;

                }

                // Reset segments to make J4 Router happy.
                Log::add('returning vars: ' . print_r($vars, true), Log::DEBUG, 'yaquiz');
                $segments = [];
                return $vars;
            }
        }

        if ($view == 'quiz' && isset($segments[2])) {
            Log::add('getting info from segments', Log::DEBUG, 'yaquiz');
            $layout = $segments[2];
            if ($layout == 'results') {
                $vars['view'] = $view;
                $vars['layout'] = $layout;
                $vars['id'] = $segments[1];

                //see if there is a menu item with this quiz id
                $quiz_id = $segments[1];
                $menu_id = $this->findMenuItemIdByQuizId($quiz_id);
                if ($menu_id) {
                    Log::add('found menu item id: ' . $menu_id, Log::DEBUG, 'yaquiz');
                    $vars['Itemid'] = $menu_id;;
                } else {
                    Log::add('no menu item id found', Log::DEBUG, 'yaquiz');
                    $vars['Itemid'] = 0;
                }

                // Reset segments to make J4 Router happy.
                $segments = [];
                return $vars;
            }
        }


        if ($view == 'quiz') {

            Log::add('view is quiz', Log::DEBUG, 'yaquiz');

            $quiz_id = $segments[1];                        

            $vars['view'] = $view;
            $vars['id'] = $quiz_id;

            $menu_id = $this->findMenuItemIdByQuizId($quiz_id);
            $vars['Itemid'] = (int) $menu_id;
            
            Log::add('found menu item id: ' . $vars['Itemid'] , Log::DEBUG, 'yaquiz');

            // Reset segments to make J4 Router happy.
            $segments = [];
            return $vars;
        }
    }


    public function findMenuItemIdByQuizId($quiz_id)
    {
        Log::add('trying to find menu item id for quiz id: ' . $quiz_id, Log::DEBUG, 'yaquiz');
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__menu');
        //where link has com_yaquiz in it
        $query->where('link LIKE "%com_yaquiz%"');
        $query->where("params LIKE '%\"quiz_id\":\"" . $quiz_id . "\"%'");
        $db->setQuery($query);
        $result = $db->loadResult();
        //if there is a result
        if ($result) {
            return $result;
        } else {
            return $this->findTopmostQuizMenuItemId();
        }
    }



    public function findTopmostQuizMenuItemId()
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

        Log::add('result look like this: ' . $result, Log::DEBUG, 'yaquiz');
        //if there is a result
        //return the first id
        if ($result) {
            return (int) $result;
        } else {
            return null;
        }

    }
}
