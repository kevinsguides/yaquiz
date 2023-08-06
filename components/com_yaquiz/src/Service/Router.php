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

        return $segments;
        
    }


    public function parse(&$segments){


        Log::add('parse with segments ' . print_r($segments, true), Log::DEBUG, 'yaquiz');
        
        $view = $segments[0];

        //the MENU item id
        $Itemid = $this->menu->getActive()->id;


        Log::add('Itemid: ' . $Itemid, Log::DEBUG, 'yaquiz');
        //if we have an Itemid, we can use it to get the view and id
        if($Itemid){
            $item = $this->menu->getItem($Itemid);
            Log::add('item: ' . print_r($item, true), Log::DEBUG, 'yaquiz');
            if($item->component == 'com_yaquiz'){
                Log::add('item component is com_yaquiz', Log::DEBUG, 'yaquiz');
                $vars = [];
                if($view === 'quiz'){
                    
                    $active = $this->menu->getActive();
                    $quiz_id = $active->getParams()->get('quiz_id');
                    Log::add('quiz_id: ' . $quiz_id, Log::DEBUG, 'yaquiz');
                    $vars['view'] = $view;
                    $vars['id'] = $quiz_id;

                }
                
                $segments = [];
                return $vars;
            }
        }

        $vars = [];



		if ($view == 'quiz')
		{
			$vars['view'] = $view;
			$vars['id'] = $segments[1];
			// Reset segments to make J4 Router happy.
			$segments = [];
		}


		return $vars;
		
       
    }





}