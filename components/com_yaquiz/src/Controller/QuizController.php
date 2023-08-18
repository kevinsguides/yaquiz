<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\Controller;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Session\Session;
use Joomla\Input\Input;
use JSession;
use Joomla\CMS\Router\Route;
//use language
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
use KevinsGuides\Component\Yaquiz\Site\Helper\QuestionBuilderHelper;

/**
 * Summary of QuizController
 */
class QuizController extends BaseController
{

    /**
     * Summary of display
     * @param mixed $cachable
     * @param mixed $urlparams
     */
    public function display($cachable = false, $urlparams = array())
    {
        parent::display($cachable, $urlparams);
        //register tasks
        $this->registerTask('submitquiz', 'submitquiz');
        $this->registerTask('loadnextpage', 'loadnextpage');
        $this->registerTask('startTimedQuiz', 'startTimedQuiz');
    }

    /**
     * Submit a quiz, grade it, store results if needed, and redirect to the results page
     * TODO: refactor this into smaller methods
     * 
     */
    public function submitquiz()
    {
        //check for token
        if (!Session::checkToken()) {
            //cue error message
            $this->setMessage('Token failed');
            //redirect to the view
            $this->setRedirect(Route::_('index.php?option=com_yaquiz'));
            return;
        }

        //grade the quiz by comparing the answers to the correct answers
        //get submitted answers
        $app = Factory::getApplication();
        $input = $app->getInput();
        $user = $app->getIdentity();
        $quiz_id = $input->get('id', 0, 'int');
        $quiz = $this->getModel('Quiz')->getItem($quiz_id);
        $model = new QuizModel();
        $quizParams = $model->getQuizParams($quiz_id);
        $globalParams = $app->getParams('com_yaquiz');

        if ($globalParams->get('record_submissions') === '1') {
            $model->countAsSubmission($quiz_id);
        }

        //if this is a single page quiz
        if ($quizParams->quiz_displaymode === 'default') {
            //then answers come from form input
            $answers = $input->get('answers', array(), 'array');
        } else if ($quizParams->quiz_displaymode === 'individual') {
            //then answers come from the session variable
            $session = $app->getSession();
            $answers = $session->get('sq_answers', array());
            $answers = $answers[$quiz_id];
        }

        //check for existing set Itemid
        $itemid = $input->get('Itemid', 0, 'int');

        //See if this is a timed quiz and if so, check if they ran out of time
        if ($quizParams->quiz_use_timer === '1') {
            $timeleft = $model->getTimeRemainingAsSeconds($user->id, $quiz_id);
            //if they ran out of time with 15 seconds or less, submit the quiz and grade as is
            if ($timeleft <= 15) {
                $app->enqueueMessage('Time up', 'warning');
            }
            //if they ran out of time and quiz is more than 15 seconds expired, the whole quiz is wrong
            elseif ($timeleft < 0) {
            }
            else{
                //make sure answered all questions
                if(!$this->checkQuestionsAnswered($answers, $quiz_id)){
                    return;
                }
            }
        }
        else{
            //make sure answered all questions
            if(!$this->checkQuestionsAnswered($answers, $quiz_id)){
                return;
            }
        }


        $results = $this->gradeQuiz($answers, $quizParams, $quiz_id);
        

        //save general results
        $quiz_record_results = (int) $quizParams->quiz_record_results;
        $quiz_record_guest_results = (int) $quizParams->quiz_record_guest_results;

        if ($quiz_record_results == -1) {
            $quiz_record_results = (int) $globalParams->get('quiz_record_results', 0);
        }
        if ($quiz_record_guest_results == -1) {
            $quiz_record_guest_results = (int) $globalParams->get('quiz_record_guest_results', 0);
        }
        if ($quiz_record_results >= 1) {
            if ($quiz_record_guest_results == 1 || $app->getIdentity()->guest == 0) {
                $model->saveGeneralResults($quiz_id, $results->scorepercentage, $results->passfail);
            }
        }

        $qbhelper = new QuestionBuilderHelper();



        $new_result_id = 0;

        //save individual results (level 2 or 3)
        if ($quiz_record_results >= 2) {
            //user must be logged in
            if (!$app->getIdentity()->guest) {
                $new_result_id = $model->saveIndividualResults($results, $quiz_record_results);
                if ($quizParams->quiz_use_timer === '1') {
                    $model->updateTimerOnSubitted($user->id, $quiz_id, $new_result_id);
                }
            }

        }

        Log::add('value of $quiz_record_results: ' . $quiz_record_results, Log::INFO, 'com_yaquiz');

        //set quiz title state var
        $quiz = $model->getItem($quiz_id);
        $title = $quiz->title;
        $buildResults = $qbhelper->buildResultsArea($quiz_id, $results, $new_result_id);


        //check if the user already started this quiz
        $session = Factory::getApplication()->getSession();
        $answers = $session->get('sq_answers', array());
        //check if an entry exists for this quiz
        if (isset($answers[$quiz_id])) {
            $session->clear('sq_answers');
            $session->clear('sq_quiz_id');
        }

        //set the results state var
        $app->setUserState('com_yaquiz.results', $buildResults);

        if($new_result_id != 0){
            Log::add('redirecting to: ' . Route::_('index.php?option=com_yaquiz&view=quiz&layout=results&id=' . $quiz_id . '&resultid=' . $new_result_id . '&Itemid=' . $itemid), Log::INFO, 'com_yaquiz');

            $this->setRedirect(Route::_('index.php?option=com_yaquiz&view=quiz&layout=results&id=' . $quiz_id . '&resultid=' . $new_result_id . '&Itemid=' . $itemid));
        }
        else{
            Log::add('redirecting to: ' . Route::_('index.php?option=com_yaquiz&view=quiz&layout=results&id=' . $quiz_id), Log::INFO, 'com_yaquiz');
            $this->setRedirect(Route::_('index.php?option=com_yaquiz&view=quiz&layout=results&id=' . $quiz_id));
        }

        
        


    }

    public function gradeQuiz($answers, $quizParams, $quiz_id){

        $points = 0;
        $total = 0;
        $model = new QuizModel();

        $all_questions = $model->getQuestions($quiz_id);

        $all_feedback = array();

        $modified_array = false;

        //loop through each question in $all_questions
        foreach ($all_questions as $question){
            //if the question is a section, skip it
            if($question->params->question_type === 'html_section'){
                continue;
            }

            //if array is null, user didn't answer any questions
            if($answers == null){
                $answers = array();
            }

            //if the question is not in the answers array, add it with a blank answer
            if(!array_key_exists($question->id, $answers)){
                $answers[$question->id] = '';
                $modified_array = true;
            }
        }

        if($modified_array){
            //sort array by $question->ordering
            uksort($answers, function($a, $b) use ($all_questions) {
                $a_ordering = 0;
                $b_ordering = 0;
                foreach($all_questions as $question){
                    if($question->id == $a){
                        $a_ordering = $question->ordering;
                    }
                    if($question->id == $b){
                        $b_ordering = $question->ordering;
                    }
                }
                return $a_ordering - $b_ordering;
            });
        }

        foreach ($answers as $question_id => $answer) {
            $question = $model->getQuestion($question_id);

            if($question->params->question_type === 'html_section'){
                continue;
            }

            if ($quizParams->quiz_use_points === '1') {
                $point_multiplier = $question->params->points;
            } else {
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
        $scorepercentage = $points / $total * 100;
        if (($points / $total * 100) < $quizParams->passing_score) {
            $passfail = 'fail';
        }

        //create a blank results object
        $results = new \stdClass();
        $results->correct = $points;
        $results->total = $total;
        $results->quiz_id = $quiz_id;
        $results->questions = $all_feedback;
        $results->scorepercentage = $scorepercentage;
        $results->passfail = $passfail;

        return $results;
    }



    /**
     * Checks if the user has answered all the questions. If not, it saves the answers to the session and redirects them to the quiz page.
     * @param array $answers
     * @param int $quiz_id
     */
    public function checkQuestionsAnswered($answers, $quiz_id){

        $app = Factory::getApplication();
        $model = new QuizModel();
            //make sure they answered all the questions...
            if (count($answers) < $model->getTotalQuestions($quiz_id)) {
                Log::add('user not answered all questions');
                $this->setMessage(Text::_('COM_YAQ_HAS_UNANSWERED_QUESTIONS'), 'warning');
    
                //foreach question, save its answer and id to an array
                $answered = array();
                foreach ($answers as $question_id => $answer) {
                    $answered[] = array(
                        'question_id' => $question_id,
                        'answer' => $answer
                    );
                }
    
    
                //save all answered info to their session
                $session = $app->getSession();
                $session->set('sq_retryanswers', $answered);
                $this->setRedirect(Route::_('index.php?option=com_yaquiz&view=quiz&id=' . $quiz_id . '&status=retry'));
                return false;
            }
            else{
                return true;
            }
    }


    /**
     * Loads the next page of the quiz in a multi page quiz and saves the answers to the session
     * TODO: If quiz is timed, and they run out of time, submit quiz instead of loading next page
     * //Also check if we need to start a timer after page 0
     */
    public function loadnextpage()
    {

        $app = Factory::getApplication();
        $input = $app->getInput();
        $quiz_id = $input->get('id', 0, 'int');
        $page = $input->get('page', 0, 'int');
        $nextpage = $input->get('nextpage', 0, 'int, string');
        if ($nextpage == '-1' || $nextpage == 1) {
            $page += $nextpage;
        }

        if (isset($_POST['answers'])) {
            //$answers = $_POST['answers'];
            $answers = $input->get('answers', array(), 'array');
            $this->savePageAnswers($quiz_id, $answers);
        }


        //timer stuff
        $model = $this->getModel('Quiz');
        $quizParams = $model->getQuizParams($quiz_id);
        $use_timer = ($quizParams->quiz_use_timer==1)?true:false;
        $user = $app->getIdentity();
        if($use_timer){

            $model->cleanupQuizTimer($user->id, $quiz_id);

            $existing_timerid = $model->getTimerId($user->id, $quiz_id);
            //see if we need to start a timer
            if($page == 1 && $existing_timerid == 0){
                $model->createNewTimer($user->id, $quiz_id);
            }

            //see if a timer has expired (15 seconds or less)
            $timeleft = $model->getTimeRemainingAsSeconds($user->id, $quiz_id);
            if($timeleft <= 15){
                $this->submitquiz();
                return;
            }

        }
        


        //redirect to view quiz with id and page
        if ($nextpage === 'results') {
            //redirect to results page
            $this->submitquiz();
        } else {
            $this->setRedirect(Route::_('index.php?option=com_yaquiz&view=quiz&id=' . $quiz_id . '&page=' . $page));
        }


    }

    /**
     * Saves the user's answers for the current page to their user session
     * The user cannot be working on more than one quiz at once
     * TODO restrict the user to only one quiz at a time
     */
    protected function savePageAnswers($quiz_id, $answers)
    {
        $session = Factory::getApplication()->getSession();
        //answers is in the format [quizid][question_id] = answer
        //get the answers from the session index sq_answers where the quiz_id is the key
        $current_answers = $session->get('sq_answers', array());
        //if the quiz_id is not in the array, add it
        if (!array_key_exists($quiz_id, $current_answers)) {
            $current_answers[$quiz_id] = array();
        }
        //answer is in the format [question_id] = answer
        //add the answer to the current_answers array
        foreach ($answers as $question_id => $answer) {
            $current_answers[$quiz_id][$question_id] = $answer;
        }

        //save answer to session
        $session->set('sq_answers', $current_answers);
        $session->set('sq_quiz_id', $quiz_id);


    }

    public function resetSession()
    {
        //delete sq_answers and sq_quiz_id from session
        $session = Factory::getApplication()->getSession();

        if (isset($_GET['quiz_id'])) {
            $quiz_id = $_GET['quiz_id'];
            $current_answers = $session->get('sq_answers', array());
            if (array_key_exists($quiz_id, $current_answers)) {
                unset($current_answers[$quiz_id]);
                $session->set('sq_answers', $current_answers);
                $this->setMessage(Text::_('COM_YAQ_QUIZ_RESET'), 'warning');
                $this->setRedirect('index.php?option=com_yaquiz&view=quiz&id=' . $quiz_id . '&page=0');
            } else {
                $this->setMessage(Text::_('COM_YAQ_QUIZ_RESET_ALREADY'), 'warning');
                $this->setRedirect('index.php');
            }
        } else {
            $session->clear('sq_answers');
            $session->clear('sq_quiz_id');
            $this->setMessage(Text::_('COM_YAQ_QUIZ_RESET'), 'warning');
            $this->setRedirect(Route::_('index.php'));
        }
    }

    public function verifyquiz(){


        $app = Factory::getApplication();
        $input = $app->getInput();
        $certcode = $input->get('certcode', '', 'string');
        $model = $this->getModel('quiz');
        $result = $model->getResultFromVerificationCode($certcode);

        if($result){
            $session = Factory::getApplication()->getSession();
            $app->setUserState('sq_verify_result', $result);
            $this->setRedirect(Route::_('index.php?option=com_yaquiz&view=certverify&layout=verifycheck'));
        }
        else{
            $app->enqueueMessage(Text::_('COM_YAQ_QUIZ_VERIFY_FAIL'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_yaquiz&view=certverify'));
        }

    }



    public function startTimedQuiz(){
        $app = Factory::getApplication();
        $user = $app->getIdentity();
        $input = $app->getInput();
        $quiz_id = $input->get('id', 0, 'int');
        $model = $this->getModel('quiz');
        $model->cleanupQuizTimer($user->id, $quiz_id);
        $model->createNewTimer($user->id, $quiz_id);
        $app->enqueueMessage(Text::_('COM_YAQ_QUIZ_TIMER_STARTED'), 'warning');
        $this->setRedirect(Route::_('index.php?option=com_yaquiz&view=quiz&id=' . $quiz_id));

    }


}