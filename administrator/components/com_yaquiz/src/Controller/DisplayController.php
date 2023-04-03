<?php
namespace KevinsGuides\Component\Yaquiz\Administrator\Controller;
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Log\Log;

class DisplayController extends BaseController
{
    protected $default_view = 'Yaquizzes';


    //display the view
    public function display($cachable = false, $urlparams = array())
    {

        Log::add('DisplayController::display() called', Log::INFO, 'com_yaquiz');
        return parent::display($cachable, $urlparams);

    }

}