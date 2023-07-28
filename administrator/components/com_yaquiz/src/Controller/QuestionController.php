<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\Controller;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;
class QuestionController extends BaseController
{
    //constructor
    public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
    {
        Log::add('QuestionController::__construct() called', Log::INFO, 'com_yaquiz');
        parent::__construct($config, $factory, $app, $input);
        //register task to save
        $this->registerTask('edit', 'edit');
    }
    

    /**
     * Task asks model to save or update the question
     */
    public function edit($cachable = false, $urlparams = array())
    {
        $app = Factory::getApplication();
        if($app->getIdentity()->authorise('core.edit', 'com_yaquiz') != true){
            $app->enqueueMessage('You do not have permission to edit questions', 'error');
            $app->redirect('index.php?option=com_yaquiz&view=yaquizzes');
        }


        //get the model
        $model = $this->getModel('Question');
        //get the data from form POST
        $data = $this->input->post->get('jform', array(), 'array');
        $data_array_to_string = '';
        foreach ($data as $key => $value) {
            $data_array_to_string .= $key . ' => ' . $value . ', ';
        }
        Log::add('QuestionController::save() called with data' . $data_array_to_string, Log::INFO, 'com_yaquiz');
        //log the model name
        //save the data
        $model->save($data);
        if ($data['id'] == 0) {
            $newid = $model->getLastInsertedId();
        } else {
            $newid = $data['id'];
        }
        //cue saved message
        $this->setMessage('Question saved');
        //send user back to the question they were editing
        $this->setRedirect('index.php?option=com_yaquiz&view=Question&layout=edit&qnid=' . $newid);
    }
    public function cancel()
    {

        
        $app = Factory::getApplication();
        $redirect = $app->getUserState('com_yaquiz.redirectbackto');
        $this->setRedirect($redirect);

        
    }
    public function saveclose()
    {
        $this->edit();
        $app = Factory::getApplication();
        $redirect = $app->getUserState('com_yaquiz.redirectbackto');
        $this->setRedirect($redirect);
        
    }
}