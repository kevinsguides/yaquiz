<?php
namespace KevinsGuides\Component\SimpleQuiz\Site\Controller;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;
use JSession;
defined ('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;
use KevinsGuides\Component\SimpleQuiz\Site\Model\QuizModel;
use KevinsGuides\Component\SimpleQuiz\Site\Helper\QuestionBuilderHelper;

/**
 * Summary of QuizController
 */
class QuizController extends BaseController{

    /**
     * Summary of display
     * @param mixed $cachable
     * @param mixed $urlparams
     * @return void
     */
    public function display($cachable = false, $urlparams = array()){
        parent::display($cachable, $urlparams);
        //register tasks
        $this->registerTask('submitquiz', 'submitquiz');
        $this->registerTask('loadnextpage', 'loadnextpage');

    }

    public function submitquiz()
    {
        //check for token
        if(!JSession::checkToken()){
            Log::add('SimpleQuizController::save() token failed', Log::INFO, 'com_simplequiz');
            //cue error message
            $this->setMessage('Token failed');
            //redirect to the view
            $this->setRedirect('index.php?option=com_simplequiz');
            return;
        }

        //grade the quiz by comparing the answers to the correct answers
        //get submitted answers
        $app = Factory::getApplication();
        $input = $app->getInput();
        $quiz_id = $input->get('quiz_id', 0, 'int');
        $quiz = $this->getModel('Quiz')->getItem($quiz_id);
        $model = new QuizModel();
        $quizParams = $model->getQuizParams($quiz_id);
        $globalParams = $app->getParams('com_simplequiz');
        if ($globalParams->get('record_submissions') === '1') {
            $model->countAsSubmission($quiz_id);
        }

        //if this is a single page quiz
        if($quizParams->quiz_displaymode === 'default'){
            //then answers come from form input
            $answers = $input->get('answers', array(), 'array');
        }
        else if($quizParams->quiz_displaymode === 'individual'){
            //then answers come from the session variable
            $session = $app->getSession();
            $answers = $session->get('sq_answers', array());
            $answers = $answers[$quiz_id];
        }

        

        //make sure they answered all the questions...
        if(count($answers) < $model->getTotalQuestions($quiz_id)){
            $this->setMessage('You did not answer all the questions', 'warning');

            //foreach question, save its answer and id to an array
            $answered = array();
            foreach($answers as $question_id=>$answer){
                $answered[] = array(
                    'question_id' => $question_id,
                    'answer' => $answer
                );
            }


            //save all answered info to their session
            $session = $app->getSession();
            $session->set('sq_retryanswers', $answered);
            $this->setRedirect('index.php?option=com_simplequiz&view=quiz&id=' . $quiz_id.'&status=retry');
            return;
        }
        


        //for each answer, check if its correct
        $points = 0;
        $total = 0;
        $model = new QuizModel();
        $all_feedback = array();
        foreach($answers as $question_id=>$answer){
            $question = $model->getQuestion($question_id);
            if($quizParams->quiz_use_points === '1'){
                $point_multiplier = $question->params->points;
            }
            else{
                $point_multiplier = 1;
            }
            
            $total += $point_multiplier;
            $points = $points + ($model->checkAnswer($question_id, $answer) * $point_multiplier);

            //key value pair of question_id, their answer, and if its correct
            $question_feedback = array(
                'question' => $question,
                'iscorrect' => $model->checkAnswer($question_id, $answer),
                'useranswer' => $model->getSelectedAnswerText($question_id, $answer),
            );
            $all_feedback[] = $question_feedback;
        }

        $passfail = 'pass';
        $scorepercentage = $points/$total * 100;
        if(($points/$total * 100) < $quizParams->passing_score){
            $passfail = 'fail';
        }

        $qbhelper = new QuestionBuilderHelper();
        
        //create a blank results object
        $results = new \stdClass();
        $results->correct = $points;
        $results->total = $total;
        $results->quiz_id = $quiz_id;
        $results->questions = $all_feedback;
        $results->passfail = $passfail;

        //set quiz title state var
        $quiz = $model->getItem($quiz_id);
        $title = $quiz->title;
        $buildResults = $qbhelper->buildResultsArea($title, $quiz_id, $results);
        //echo $buildResults;
        $this->setRedirect('index.php?option=com_simplequiz&view=quiz&layout=results&id='.$quiz_id);

        //set the results state var
        $app->setUserState('com_simplequiz.results', $buildResults);
    }


    public function loadnextpage(){
        Log::add('SimpleQuizController::loadnextpage()', Log::INFO, 'com_simplequiz');
        $app = Factory::getApplication();
        $input = $app->getInput();
        $quiz_id = $_POST['quiz_id'];
        $page = $_POST['page'];
        $nextpage = $_POST['nextpage'];
        if($nextpage == '-1' || $nextpage == 1){
            $page+= $nextpage;
        }

        
        if(isset($_POST['answers'])){
            $answers = $_POST['answers'];
            $this->savePageAnswers($quiz_id, $answers);
        }


        //redirect to view quiz with id and page
        if($nextpage === 'results'){
            //redirect to results page
            $this->submitquiz();
            //$this->setRedirect('index.php?option=com_simplequiz&view=quiz&layout=results&id='.$quiz_id);

        }
        else{
            $this->setRedirect('index.php?option=com_simplequiz&view=quiz&id='.$quiz_id.'&page='.$page);
        }
        

    }

    /**
     * Saves the user's answers for the current page to their user session
     * The user cannot be working on more than one quiz at once
     * TODO restrict the user to only one quiz at a time
     */
    protected function savePageAnswers($quiz_id, $answers){
        $session = Factory::getApplication()->getSession();
        //answers is in the format [quizid][question_id] = answer
        //get the answers from the session index sq_answers where the quiz_id is the key
        $current_answers = $session->get('sq_answers', array());
        //if the quiz_id is not in the array, add it
        if(!array_key_exists($quiz_id, $current_answers)){
            $current_answers[$quiz_id] = array();
        }
        //answer is in the format [question_id] = answer
        //add the answer to the current_answers array
        foreach($answers as $question_id=>$answer){
            $current_answers[$quiz_id][$question_id] = $answer;
        }

        //save answer to session
        $session->set('sq_answers', $current_answers);
        $session->set('sq_quiz_id', $quiz_id);

        
    }

    public function resetSession(){
        //delete sq_answers and sq_quiz_id from session
        $session = Factory::getApplication()->getSession();

        if(isset($_GET['quiz_id'])){
            $quiz_id = $_GET['quiz_id'];
            $current_answers = $session->get('sq_answers', array());
            if(array_key_exists($quiz_id, $current_answers)){
                unset($current_answers[$quiz_id]);
                $session->set('sq_answers', $current_answers);
                $this->setMessage('Quiz data has been reset', 'warning');
                $this->setRedirect('index.php?option=com_simplequiz&view=quiz&id='.$quiz_id.'&page=0');
            }
            else{
                $this->setMessage('Quiz data has already been reset', 'warning');
                $this->setRedirect('index.php');
            }
        }
        else{
            $session->clear('sq_answers');
            $session->clear('sq_quiz_id');
            $this->setMessage('All quiz data has been reset', 'warning');
            $this->setRedirect('index.php');
        }


    }


}