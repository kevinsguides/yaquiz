<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\Controller;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Menus\Administrator\Controller\ItemController;
use Joomla\Input\Input;
use JSession;
use JUri;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class SimpleQuizController extends BaseController

{
    public function __construct($config = [])
    {
        Log::add('SimpleQuizController::__construct() called', Log::INFO, 'com_simplequiz');

        //register delete task
        //$this->registerTask('remove', 'remove');
        parent::__construct($config);

    }

    public function save(){
            
            Log::add('SimpleQuizController::save() called', Log::INFO, 'com_simplequiz');
    
            //get the model
            $model = $this->getModel('SimpleQuiz');
    
            //get the data from form POST
            $data = $this->input->post->get('jform', array(), 'array');
            //check for token
            if(!JSession::checkToken()){
                Log::add('SimpleQuizController::save() token failed', Log::INFO, 'com_simplequiz');
                //cue error message
                $this->setMessage('Token failed');
                //redirect to the view
                $this->setRedirect('index.php?option=com_simplequiz');
                return;
            }
            //save the data
            $newid = $model->save($data);
            if($newid > 0){
                Log::add('SimpleQuizController::save() saved successfully', Log::INFO, 'com_simplequiz');
                //cue success message
                $this->setMessage('Quiz saved successfully');
                //return to edit form
                $this->setRedirect('index.php?option=com_simplequiz&view=simplequiz&layout=edit&id=' . $newid);
            }
    }

    public function cancel($key = null){

        $this->setRedirect('index.php?option=com_simplequiz&view=simplequiz&id=' . $_GET['id']);
    }

    public function saveclose(){
        //call save
        $this->save();
        //redirect to the view
        $this->setRedirect('index.php?option=com_simplequiz&view=simplequizzes');

    }

    public function add(){
        Log::add('SimpleQuizController::new() called', Log::INFO, 'com_simplequiz');
        //redirect to the view
        $this->setRedirect('index.php?option=com_simplequiz&view=simplequiz&layout=edit');
    }

    public function addQuestionsToQuiz(){
        Log::add('attempt add questions to quiz', Log::INFO, 'com_simplequiz');
        //get the model
        $model = $this->getModel('SimpleQuiz');
        //get the data from form POST
        $quizid = $this->input->post->get('quiz_id', '', 'raw');
        $questionids = $this->input->post->get('question_ids', array(), 'array');
        //check for token
        if(!JSession::checkToken()){
            Log::add('SimpleQuizController::save() token failed', Log::INFO, 'com_simplequiz');
            //cue error message
            $this->setMessage('Token failed');
            //redirect to the view
            $this->setRedirect('index.php?option=com_simplequiz');
            return;
        }

        $model->addQuestionsToQuiz($quizid, $questionids);
        //redirect to the view
        $this->setRedirect('index.php?option=com_simplequiz&view=simplequiz&id=' . $quizid);

    }

    public function removeQuestionFromQuiz(){
        //get quiz_id and question_id from GET
        $quizid = $this->input->get('quiz_id', '', 'raw');
        $questionid = $this->input->get('question_id', '', 'raw');
        //get the model
        $model = $this->getModel('SimpleQuiz');
        //remove the question from the quiz
        $model->removeQuestionFromQuiz($quizid, $questionid);
        //redirect to the view
        $this->setRedirect('index.php?option=com_simplequiz&view=simplequiz&id=' . $quizid);
    }

    public function redirectEdit(){
        //redirect to edit view
        $this->setRedirect('index.php?option=com_simplequiz&view=simplequiz&layout=edit&id=' . $this->input->get('id', '', 'raw'));
    }

    public function preview(){
        //open a page in a new tab
        //redirect to preview view
        $this->setRedirect(JUri::root().'index.php?option=com_simplequiz&view=quiz&id=' . $this->input->get('id', '', 'raw'));
    }

    public function remove($pk = null){
        //get the model
        $model = $this->getModel('SimpleQuiz');

        //get the data from form GET
        $quizid = $this->input->get('quizid', '', 'raw');
        Log::add('SimpleQuizController::delete() called for quizid: ' . $quizid, Log::INFO, 'com_simplequiz');

        if($model->delete($quizid)){
            //message
            $this->setMessage('Quiz deleted successfully');
        }
        //redirect to the view
        $this->setRedirect('index.php?option=com_simplequiz&view=simplequizzes');
    }

    public function orderUp(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('SimpleQuiz');
        $neworder = $model->moveQuestionOrderUp($quiz_id, $this->input->get('qnid', '', 'raw'));
        //redirect to quiz
        $this->setRedirect('index.php?option=com_simplequiz&view=SimpleQuiz&id=' . $this->input->get('quiz_id', '', 'raw') . '#qn' . $neworder );
    }

    public function orderDown(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('SimpleQuiz');
        $neworder = $model->moveQuestionOrderDown($quiz_id, $this->input->get('qnid', '', 'raw'));
        //redirect to quiz
        $this->setRedirect('index.php?option=com_simplequiz&view=SimpleQuiz&id=' . $this->input->get('quiz_id', '', 'raw') . '#qn' . $neworder );
    }




}