<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\Controller;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\BaseController;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
use KevinsGuides\Component\Yaquiz\Site\Service\Router as YaquizRouter;


use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;


defined ('_JEXEC') or die;


/**
 * Summary of DisplayController
 */
class DisplayController extends BaseController{

    protected $default_view = 'quiz';
    protected $app;


    public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null){
        Log::add('DisplayController::__construct', Log::INFO, 'com_yaquiz');
        parent::__construct($config, $factory, $app, $input);
        $this->app = Factory::getApplication();
        $menu = $this->app->getMenu();
        $active = $menu->getActive();
        //if active component not com_yaquiz
        if($active && $active->component != 'com_yaquiz'){
            Log::add('manually setting active menu item', Log::DEBUG, 'com_yaquiz');
            $router = new YaquizRouter();
            $newId = $router->findMenuItemIdByQuizId($app->input->get('id'));
            $menu->setActive($newId);
        }
        
    }


    //on display...
    /**
     * Summary of display
     * @param mixed $cachable
     * @param mixed $urlparams
     * @return void
     */
    public function display($cachable = false, $urlparams = array()){
        Log::add('DisplayController::display', Log::INFO, 'com_yaquiz');
        $cachable = false;
        $layout = $this->input->get('layout');
        $pagenum = $this->input->get('page');
        $app = Factory::getApplication();

        //check for Itemid
        $itemid = $app->input->get('Itemid', null);
        
        //get config from component
        $gConfig = $app->getParams('com_yaquiz');
        if ($gConfig->get('respect_jcache',"1") == "1") {
            //if the layout is not results, and there is no page number, then we are on the first page of a quiz and it's cachable
            if($layout != 'results' && $pagenum == null){
                $cachable = true;
            }
        }   

        $wam = $app->getDocument()->getWebAssetManager();
        $wam->useStyle('fontawesome');
        parent::display($cachable, $urlparams);

        
    }


  
}
