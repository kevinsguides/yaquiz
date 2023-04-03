<?php
namespace KevinsGuides\Component\Yaquiz\Administrator\Controller;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Menus\Administrator\Controller\ItemController;
use Joomla\Input\Input;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class YaquizzesController extends BaseController{

    public function display($cachable = false, $urlparams = array())
    {
        Log::add('YaquizzesController::display() called', Log::INFO, 'com_yaquiz');
        //redirect to the view
        $this->setRedirect('index.php?option=com_yaquiz&view=yaquizzes');

    }
    //the add task
    public function add(){
        Log::add('YaquizzesController::add() called', Log::INFO, 'com_yaquiz');
        //redirect to the view
        $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&layout=edit');
    }

}