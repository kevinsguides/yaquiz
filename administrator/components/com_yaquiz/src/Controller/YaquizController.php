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
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Menus\Administrator\Controller\ItemController;
use Joomla\Input\Input;
use JSession;
use JUri;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class YaquizController extends BaseController

{
    public function __construct($config = [])
    {
        Log::add('YaquizController::__construct() called', Log::INFO, 'com_yaquiz');

        //register delete task
        //$this->registerTask('remove', 'remove');
        parent::__construct($config);

    }

    public function save(){
            
            Log::add('YaquizController::save() called', Log::INFO, 'com_yaquiz');
    
            //get the model
            $model = $this->getModel('Yaquiz');
    
            //get the data from form POST
            $data = $this->input->post->get('jform', array(), 'array');
            //check for token
            if(!JSession::checkToken()){
                Log::add('YaquizController::save() token failed', Log::INFO, 'com_yaquiz');
                //cue error message
                $this->setMessage('Token failed');
                //redirect to the view
                $this->setRedirect('index.php?option=com_yaquiz');
                return;
            }




            //save the data
            $newid = $model->save($data);
            if($newid > 0){
                Log::add('YaquizController::save() saved successfully', Log::INFO, 'com_yaquiz');
                //cue success message
                $this->setMessage('Quiz saved successfully');
                //return to edit form
                $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&layout=edit&id=' . $newid);
            }
    }

    public function cancel($key = null){

        //if no id, redirect to quizzes
        if(!isset($_GET['id']) || $_GET['id'] == 0){
            $this->setRedirect('index.php?option=com_yaquiz&view=yaquizzes');
            return;
        }
        
        //check in
        $model = $this->getModel('Yaquiz');
        $model->checkin();

        $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&id=' . $_GET['id']);
    }

    public function saveclose(){
        //call save
        $this->save();
        //redirect to the view
        $this->setRedirect('index.php?option=com_yaquiz&view=yaquizzes');

    }

    public function add(){
        Log::add('YaquizController::new() called', Log::INFO, 'com_yaquiz');
        //redirect to the view
        $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&layout=edit');
    }

    public function addQuestionsToQuiz(){
        Log::add('attempt add questions to quiz', Log::INFO, 'com_yaquiz');
        //get the model
        $model = $this->getModel('Yaquiz');
        //get the data from form POST
        $quizid = $this->input->post->get('quiz_id', '', 'raw');
        $questionids = $this->input->post->get('question_ids', array(), 'array');
        //check for token
        if(!JSession::checkToken()){
            Log::add('YaquizController::save() token failed', Log::INFO, 'com_yaquiz');
            //cue error message
            $this->setMessage('Token failed');
            //redirect to the view
            $this->setRedirect('index.php?option=com_yaquiz');
            return;
        }

        $model->addQuestionsToQuiz($quizid, $questionids);
        //redirect to the view
        $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&id=' . $quizid);
        //refresh
        //$this->redirectEdit();

    }

    public function removeQuestionFromQuiz(){
        //get quiz_id and question_id from GET
        $quizid = $this->input->get('quiz_id', '', 'raw');
        $questionid = $this->input->get('question_id', '', 'raw');
        //get the model
        $model = $this->getModel('Yaquiz');
        //remove the question from the quiz
        $model->removeQuestionFromQuiz($quizid, $questionid);
        //reorder
        $model->reorderQns($quizid);
        //redirect to the view
        $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&id=' . $quizid);
    }

    public function redirectEdit(){
        //redirect to edit view
        $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&layout=edit&id=' . $this->input->get('id', '', 'raw'));
    }

    public function preview(){
        //open a page in a new tab
        //redirect to preview view
        $this->setRedirect(JUri::root().'index.php?option=com_yaquiz&view=quiz&id=' . $this->input->get('id', '', 'raw'));
    }

    public function remove($pk = null){
        //get the model
        $model = $this->getModel('Yaquiz');

        //get the data from form GET
        $quizid = $this->input->get('quizid', '', 'raw');
        Log::add('YaquizController::delete() called for quizid: ' . $quizid, Log::INFO, 'com_yaquiz');

        if($model->delete($quizid)){
            //message
            $this->setMessage('Quiz deleted successfully');
        }
        //redirect to the view
        $this->setRedirect('index.php?option=com_yaquiz&view=yaquizzes');
    }

    public function orderUp(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('Yaquiz');
        $neworder = $model->moveQuestionOrderUp($quiz_id, $this->input->get('qnid', '', 'raw'));
        //redirect to quiz
        $this->setRedirect('index.php?option=com_yaquiz&view=Yaquiz&id=' . $this->input->get('quiz_id', '', 'raw') . '#qn' . $neworder );
    }

    public function orderDown(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('Yaquiz');
        $neworder = $model->moveQuestionOrderDown($quiz_id, $this->input->get('qnid', '', 'raw'));
        //redirect to quiz
        $this->setRedirect('index.php?option=com_yaquiz&view=Yaquiz&id=' . $this->input->get('quiz_id', '', 'raw') . '#qn' . $neworder );
    }


    public function reorderQns(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('Yaquiz');
        $model->reorder($quiz_id);
        //redirect to quiz
        //$this->setRedirect('index.php?option=com_yaquiz&view=Yaquiz&id=' . $this->input->get('quiz_id', '', 'raw'));
    }

    public function removeAllQuestionsFromQuiz($pk = null){
        //get the model
        $model = $this->getModel('Yaquiz');

        //get the data from form GET
        $quizid = $_GET['quiz_id'];
        Log::add('YaquizController::delete() called for quizid: ' . $quizid, Log::INFO, 'com_yaquiz');

        if($model->removeAllQuestionsFromQuiz($quizid)){
            //message
            $this->setMessage('Questions removed from quiz');
        }
        //redirect to the view
        $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&id=' . $quizid);
    }


    //do something with multiple questions
    public function executeBatchOps(){

        if(isset($_POST['batch_op']) && $_POST['batch_op'] == 'remove'){

            $questionIds = $_POST['selectedQuestions'];
            $quiz_id = $_POST['quiz_id'];
            $model = $this->getModel('Yaquiz');
            $model->removeQuestionFromQuiz($quiz_id, $questionIds);
            $model->reorderQns($quiz_id);
            $this->setMessage('Questions removed from quiz');
            $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&id=' . $quiz_id);
        }else{
            $this->setMessage('No batch operation selected','error');
            $this->setRedirect('index.php?option=com_yaquiz&view=yaquiz&id=' . $_POST['quiz_id']);
        }
    }


    //This will remove all general stats, user scores saved, and user attempt counts
    public function resetAllStatsAndRecords(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('Yaquiz');
        $model->resetAllStatsAndRecords($quiz_id);
        //redirect to quiz
        $this->setRedirect('index.php?option=com_yaquiz&view=Yaquiz&id=' . $this->input->get('quiz_id', '', 'raw'));
        $this->setMessage('All stats and records reset');
    }

    public function resetGeneralQuizStats(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('Yaquiz');
        $model->resetGeneralQuizStats($quiz_id);
        //redirect to quiz
        $this->setRedirect('index.php?option=com_yaquiz&view=Yaquiz&id=' . $this->input->get('quiz_id', '', 'raw'));
        $this->setMessage('General stats reset');
    }

    public function resetIndividualQuizStats(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('Yaquiz');
        $model->resetIndividualQuizStats($quiz_id);
        //redirect to quiz
        $this->setRedirect('index.php?option=com_yaquiz&view=Yaquiz&id=' . $this->input->get('quiz_id', '', 'raw'));
        $this->setMessage('Individual stats reset');
    }

    public function resetAllQuizAttemptCounts(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('Yaquiz');
        $model->resetAllQuizAttemptCounts($quiz_id);
        //redirect to quiz
        $this->setRedirect('index.php?option=com_yaquiz&view=Yaquiz&id=' . $this->input->get('quiz_id', '', 'raw'));
        $this->setMessage('All attempt counts reset');
    }

    public function recalculateGeneralStatsFromSaved(){
        $quiz_id = $this->input->get('quiz_id', '', 'raw');
        $model = $this->getModel('Yaquiz');
        $model->recalculateGeneralStatsFromSaved($quiz_id);
        //redirect to quiz
        $this->setRedirect('index.php?option=com_yaquiz&view=Yaquiz&id=' . $this->input->get('quiz_id', '', 'raw'));
        $this->setMessage('General stats recalculated');
    }

}