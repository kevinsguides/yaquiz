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
        //register tasks

        $app = Factory::getApplication();
        $wam = $app->getDocument()->getWebAssetManager();
        //ask for fontawesome
        $wam->useStyle('fontawesome');
        parent::display($cachable, $urlparams);

        
    }


  
}
