<?php
/**
 *
 * @copyright   (C) kevin olson kevinsguides.com
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KevinsGuides\Component\Yaquiz\Site\Service;

defined ('_JEXEC') or die;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
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
 * site.com/menuitem/[view]/[layout]/[id]/[page]
 * The "categories" view is parent of everything else
 */

class Router extends RouterView{


    public function build(&$query)
    {

        Log::add('build called with query: ' . print_r($query, true), Log::DEBUG, 'yaquiz');


        $segments = [];
        $app = Factory::getApplication();

        if(isset($query['view'])){
            //if view is quiz, we can omit it since it's the default
            if($query['view'] != 'quiz'){
                $segments[] = $query['view'];
            }
            unset($query['view']);
        }
        if(isset($query['layout'])){
            $segments[] = $query['layout'];
            unset($query['layout']);
        }
        if(isset($query['Itemid'])){
            unset($query['Itemid']);
        }

        if(isset($query['id'])){
            $segments[] = $query['id'];
            unset($query['id']);
        }
        else{
            //check for id in app input
            $input = $app->input;
            $id = $input->getInt('id');
            if($id){
                $segments[] = $id;
                unset($query['id']);
            }
        }


        if(isset($query['page'])){
            $segments[] = 'p-'.$query['page'];
            unset($query['page']);
        }

        if(isset($query['resultid'])){
            $segments[] = 'resultid-'.$query['resultid'];
            unset($query['resultid']);
        }

        return $segments;
    }



    public function parse(&$segments)
    {


        $vars = [];

        //if the last item is p-# on any view or layout, it's the page number
        if(strpos($segments[count($segments)-1], 'p-') === 0){
            $vars['page'] = str_replace('p-', '', $segments[count($segments)-1]);
        
        }

        //if first segment is not categories, category, certverify, or user, we can assume it's a quiz
        $nonDefaultViews = ['categories', 'category', 'certverify', 'user'];
        if(!in_array($segments[0], $nonDefaultViews)){
            $vars['view'] = 'quiz';
        
            //continue with quiz logic

            //if any segments are numeric, it's the quiz id
            foreach($segments as $segment){
                if(is_numeric($segment)){
                    $vars['id'] = $segment;
                }
            }

            //look at $segment[1] and check against possible layouts
            $layouts = ['results', 'quiztype_singlepage', 'quiztype_oneperpage', 'quiztype_jsquiz', 'quiztype_individual', 'max_attempt_reached', 'default'];

            if(isset($segments[1]) && in_array($segments[1], $layouts)){
                $vars['layout'] = $segments[1];
            }//if a page is set, layout is automatically 'quiztype_oneperpage'
            elseif(isset($vars['page'])){
                $vars['layout'] = 'quiztype_oneperpage';
            }//if segments[0] is results
            elseif($segments[0] == 'results'){
                $vars['layout'] = 'results';
            }

            //see if we can find an itemid for menu
            if(isset($vars['id'])){
                $vars['Itemid'] = $this->getMenuItemIdByQuizId($vars['id']);
                Log::add('found menu item id: ' . $vars['Itemid'], Log::DEBUG, 'yaquiz');
            }

        }
        else{
            //the first segment is the view
            $vars['view'] = $segments[0];
            //if second segment is set, it's the layout
            if(isset($segments[1])){
                $vars['layout'] = $segments[1];
            }

            //if it ends with resultid-#, it's a result
            if(strpos($segments[count($segments)-1], 'resultid-') === 0){
                $vars['resultid'] = str_replace('resultid-', '', $segments[count($segments)-1]);
            }

            //if view is certverify, check for menu item
            if($vars['view'] == 'certverify'){
                $vars['Itemid'] = $this->getCertVerifyMenuItemId();
            }

            //if view is user, getUserResultsMenuItemId
            if($vars['view'] == 'user'){
                $vars['Itemid'] = $this->getUserResultsMenuItemId();
            }

        }

        



        $segments = [];

        Log::add('parsed vars look like this: ' . print_r($vars, true), Log::DEBUG, 'yaquiz');

        return $vars;

    }


    public function getMenuItemIdByQuizId($quiz_id)
    {
        Log::add('trying to find menu item id for quiz id: ' . $quiz_id, Log::DEBUG, 'yaquiz');
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__menu');
        //where link has com_yaquiz in it
        $query->where('link LIKE "%com_yaquiz%"');
        $query->where("params LIKE '%\"id\":\"" . $quiz_id . "\"%'");
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

        Log::add('result look like this: ' . $result, Log::DEBUG, 'yaquiz');
        //if there is a result
        //return the first id
        if ($result) {
            return (int) $result;
        } else {
            return null;
        }

    }


    public function getUserResultsMenuItemId(){

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


    public function getCertVerifyMenuItemId(){
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


    public function getMenuAliasFromItemid($itemid){
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
        $query->where("params LIKE '%\"quiz_id\":\"" . $quiz_id . "\"%'");
        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result) {
            return $result;
        } else {
            return null;
        }
    }


    public function getCategoryAlias($quiz_id){
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


    public function getQuizAlias($quiz_id){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('alias');
        $query->from('#__com_yaquiz_quizzes');
        $query->where('id = ' . $quiz_id);
        $db->setQuery($query);
        $result = $db->loadResult();

        if ($result) {
            return $result;
        } else {
            return null;
        }
    }




}


