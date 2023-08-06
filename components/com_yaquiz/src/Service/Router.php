<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_yaquiz
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KevinsGuides\Component\Yaquiz\Site\Service;

 defined ('_JEXEC') or die;

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


class Router extends RouterBase{

	public function build(&$query)
	{

        $segments = [];

        if (!isset($query['view']))
		{
			return $segments;
		}

        //if we already have a menu Itemid...
        if(isset($query['Itemid'])){
            $item = $this->menu->getItem($query['Itemid']);
            if($item->component == 'com_yaquiz'){
                $segments[] = $item->query['view'];
                if(isset($item->query['id'])){
                    $segments[] = $item->query['id'];
                }
                unset($query['view']);
                unset($query['id']);
                return $segments;
            }
        }

        $view = $query['view'];
		unset($query['view']);

        if($view == 'quiz'){
            $segments[] = $view;
            if(isset($query['id'])){
                $segments[] = $query['id'];
                unset($query['id']);
            }

        }

        if(isset($query['layout'])){
            $segments[] = $query['layout'];
            unset($query['layout']);
        }

        if(isset($query['resultid'])){
            $segments[] = $query['resultid'];
            unset($query['resultid']);
        }



        return $segments;
        
    }



    public function parse(&$segments){

        $app = Factory::getApplication();
        $layout = $app->input->get('layout', 'default');
        Log::add('layout: ' . $layout, Log::DEBUG, 'yaquiz');


        Log::add('parse with segments ' . print_r($segments, true), Log::DEBUG, 'yaquiz');
        
        $view = $segments[0];
        
        $Itemid = null;

        $vars = [];

        if($this->menu->getActive()){
            $Itemid = $this->menu->getActive()->id;
        }


        //if we have an Itemid, we can use it to get the view and id
        if($Itemid){
            $item = $this->menu->getItem($Itemid);
            Log::add('item: ' . print_r($item, true), Log::DEBUG, 'yaquiz');
            if($item->component == 'com_yaquiz'){
                Log::add('item component is com_yaquiz', Log::DEBUG, 'yaquiz');
                
                if($view === 'quiz'){
                    
                    $active = $this->menu->getActive();
                    $quiz_id = $active->getParams()->get('quiz_id');
                    Log::add('quiz_id: ' . $quiz_id, Log::DEBUG, 'yaquiz');
                    $vars['view'] = $view;
                    $vars['id'] = $quiz_id;
                }

                // Reset segments to make J4 Router happy.
                $segments = [];
                //force menu item id #166 to be used
                //$vars['Itemid'] = 166;
                
                return $vars;
            }

        }

        if ($view == 'quiz' && isset($segments[2])){
            $layout = $segments[2];
            if($layout == 'results'){
                $vars['view'] = $view;
                $vars['layout'] = $layout;
                $vars['id'] = $segments[1];

                //see if there is a menu item with this quiz id
                $quiz_id = $segments[1];
                $menu_id = $this->findMenuItemIdByQuizId($quiz_id);
                if($menu_id){
                    Log::add('found menu item id: ' . $menu_id, Log::DEBUG, 'yaquiz');
                    $vars['Itemid'] = $menu_id;
                }
                else{
                    $vars['Itemid'] = 0;
                }



                // Reset segments to make J4 Router happy.
                Log::add('i think we on result view', Log::DEBUG, 'yaquiz');
                $segments = [];
                return $vars;
            }
        }


		if ($view == 'quiz')
		{
			$vars['view'] = $view;
			$vars['id'] = $segments[1];
			// Reset segments to make J4 Router happy.
			$segments = [];
            return $vars;
		}
       
    }


    public function findMenuItemIdByQuizId($quiz_id){
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
        if($result){
            return $result;
        }
        else{
            return null;
        }
    }





}