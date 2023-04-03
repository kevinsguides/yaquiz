<?php
namespace KevinsGuides\Component\Yaquiz\Site\Controller;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\BaseController;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;

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
        $app = Factory::getApplication();
        
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
