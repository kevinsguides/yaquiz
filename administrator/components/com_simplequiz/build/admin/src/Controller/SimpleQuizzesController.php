<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\Controller;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Menus\Administrator\Controller\ItemController;
use Joomla\Input\Input;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class SimpleQuizzesController extends BaseController{

    //the add task
    public function add(){
        Log::add('SimpleQuizzesController::add() called', Log::INFO, 'com_simplequiz');
        //redirect to the view
        $this->setRedirect('index.php?option=com_simplequiz&view=simplequiz&layout=edit');
    }

}