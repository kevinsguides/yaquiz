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
use Joomla\CMS\Router\Route;


use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;


defined('_JEXEC') or die;


/**
 * Summary of DisplayController
 */
class DisplayController extends BaseController
{

    protected $default_view = 'quiz';
    protected $app;


    public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
    {

        parent::__construct($config, $factory, $app, $input);

        //see if we are using SEF urls in global config
        $config = Factory::getConfig();
        $sef = $config->get('sef');

        $this->app = Factory::getApplication();
        $menu = $this->app->getMenu();
        $active = $menu->getActive();
        //if active component not com_yaquiz
        if ($active && $active->component != 'com_yaquiz') {
            Log::add('joomla thinks active component is ' . $active->component, Log::INFO, 'com_yaquiz');
            $router = new YaquizRouter();

            $view = $app->input->get('view');

            if ($view == 'quiz') {
                $newId = $router->getMenuItemIdByQuizId($app->input->get('id'));
            } elseif ($view == 'user') {
                $newId = $router->getUserResultsMenuItemId();
            } elseif ($view == 'certverify') {
                $newId = $router->getCertVerifyMenuItemId();
            }

            $new_url = 'index.php?option=com_yaquiz';

            $new_url .= '&view=' . $view;

            $layout = $app->input->get('layout');
            if ($layout != null) {
                $new_url .= '&layout=' . $layout;
            }

            $page = $app->input->get('page');
            if ($page != null) {
                $new_url .= '&page=' . $page;
            }
            $quiz_id = $app->input->get('id');
            if ($quiz_id != null) {
                $new_url .= '&id=' . $quiz_id;
            }

            $resultid = $app->input->get('resultid');
            if ($resultid != null) {
                $new_url .= '&resultid=' . $resultid;
            }

            $new_url .= '&Itemid=' . $newId;

            //reroute to fixed url...
            if ($newId != null) {
                $app->redirect(Route::_($new_url, false));
            }
        }
    }


    //on display...
    /**
     * Summary of display
     * @param mixed $cachable
     * @param mixed $urlparams
     * @return void
     */
    public function display($cachable = false, $urlparams = array())
    {
        
        $cachable = false;
        $layout = $this->input->get('layout');
        $pagenum = $this->input->get('page');
        $view = $this->input->get('view');
        $app = Factory::getApplication();

        //check for Itemid
        $itemid = $app->input->get('Itemid', null);

        //get config from component
        $gConfig = $app->getParams('com_yaquiz');
        if ($gConfig->get('respect_jcache', "1") == "1") {
            //if the layout is not results, and there is no page number, then we are on the first page of a quiz and it's cachable
            if ($layout != 'results' && $pagenum == null) {
                $cachable = true;
            }
        }



        $wam = $app->getDocument()->getWebAssetManager();
        $wam->useStyle('fontawesome');
        parent::display($cachable, $urlparams);
    }
}
