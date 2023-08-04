<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\Controller;




defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;
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




    public function allowAdd()
    {

        $user = Factory::getApplication()->getIdentity();

        // Check edit
        if ($user->authorise('core.create', 'com_yaquiz')) {
            Log::add('QuestionController::allowAdd() called and returning true from core create', Log::INFO, 'com_yaquiz');
            return true;
        }

        return false;
    }


    /**
     * Check if user is allowed to edit, or is editing their own item and has edit.own perms
     * @return bool true if allowed
     */
    public function allowEdit($id = null)
    {

        $user = Factory::getApplication()->getIdentity();
        $app = Factory::getApplication();

        if($id == null){
            $id = $app->input->get('id', 0, 'int');
        }

        // Check edit
        if ($user->authorise('core.edit', 'com_yaquiz')) {
            Log::add('QuestionController::allowEdit() called and returning true from core edit', Log::INFO, 'com_yaquiz');
            return true;
        }


        // Check edit own
        if ($user->authorise('core.edit.own', 'com_yaquiz')) {
           
            $userId = $user->id;

            // Check for existing quiz
            if ($id) {
                // Get the user who created the article
                $createdBy = (int) $this->getModel()->getItem($id)->created_by;
                // If the article is yours to edit, allow it.
                if ($createdBy === $userId) {
                    Log::add('QuestionController::allowEdit() called and returning true from core edit own', Log::INFO, 'com_yaquiz');
                    return true;
                }
            }

            if ($id == 0) {
                Log::add('id is 0, so returning allowAdd', Log::INFO, 'com_yaquiz');
                return $this->allowAdd();
            }
        }

        Log::add('QuestionController::allowEdit() called and returning FALSE', Log::INFO, 'com_yaquiz');
        return  false;
    }


    public function allowDelete()
    {

        $user = Factory::getApplication()->getIdentity();

        // Check edit
        if ($user->authorise('core.delete', 'com_yaquiz')) {
            return true;
        }

        return false;
    }


    /**
     * Task asks model to save or update the question
     */
    public function edit($cachable = false, $urlparams = array())
    {
        $app = Factory::getApplication();



        //get the model
        $model = $this->getModel('Question');
        //get the data from form POST
        $data = $this->input->post->get('jform', array(), 'array');

        if(!$this->allowEdit($data['id'])){
            $app->enqueueMessage(Text::_('COM_YAQUIZ_PERM_REQUIRED_EDITOWN'), 'error');
            $this->setRedirect('index.php?option=com_yaquiz&view=Questions');
            return;
        }

        $data_array_to_string = '';
        foreach ($data as $key => $value) {
            $data_array_to_string .= $key . ' => ' . $value . ', ';
        }
        Log::add('QuestionController::edit() called with data' . $data_array_to_string, Log::INFO, 'com_yaquiz');
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
        $this->setRedirect('index.php?option=com_yaquiz&view=Question&layout=edit&id=' . $newid);
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


    public function new()
    {
        $app = Factory::getApplication();

        $this->setRedirect('index.php?option=com_yaquiz&view=Question&layout=edit');
    }
}
