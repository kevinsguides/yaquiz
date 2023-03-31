<?php
namespace KevinsGuides\Component\Yaquiz\Site\Controller;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\BaseController;

defined ('_JEXEC') or die;


/**
 * Summary of DisplayController
 */
class DisplayController extends BaseController{

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

        //if the layout is not results, and there is no page number, then we are on the first page of a quiz and it's cachable
        if($layout != 'results' && $pagenum == null){
            $cachable = true;
            Log::add('i think this should be cachable', Log::INFO, 'com_yaquiz');
        }

        
        //register tasks
        $app = Factory::getApplication();
        $wam = $app->getDocument()->getWebAssetManager();
        //ask for fontawesome
        $wam->useStyle('fontawesome');



        parent::display($cachable, $urlparams);

        
    }


  
}
