<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\Model;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Language\Text;

class QuizModel extends ItemModel{

    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        Log::add('QuizModel::__construct() called', Log::INFO, 'com_yaquiz');
        parent::__construct($config, $factory);
    }

    protected function populateState()
    {
        $app = Factory::getApplication();
        $pk = $app->input->get('id');
        $this->setState('quiz.id', $pk);

    }

	/**
	 * Method to get an item.
	 *
	 * @param int|null $pk The id of the item
	 * @return object
	 */
	public function getItem($pk = null) {
        if($pk == null){
            $pk = $this->getState('quiz.id');
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__com_yaquiz_quizzes'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($pk));
        $db->setQuery($query);
        $quiz = $db->loadObject();
        return $quiz;
	}

    public function getQuizParams($pk = null){
        
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from($db->quoteName('#__com_yaquiz_quizzes'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($pk));
        $db->setQuery($query);
        $params = $db->loadResult();
        return json_decode($params);
    }



    public function getQuestions($pk = null)
    {
        //the __yaquiz_question_quiz_map table has question_id and quiz_id cols
        //need to join with the questions table to get the questions for this quiz
        //get pk from GET
        if(isset($_GET['id'])){

            $pk = $_GET['id'];
        }
        else{
            $active = Factory::getApplication()->getMenu()->getActive();
            //get params from the menu item
            $pk = $active->getParams()->get('quiz_id');
        }
 
        Log::add('attempt get questions with quiz id'.$pk, Log::INFO, 'com_yaquiz');

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__com_yaquiz_questions'));
        $query->join('INNER', $db->quoteName('#__com_yaquiz_question_quiz_map') . ' ON (' . $db->quoteName('#__com_yaquiz_questions.id') . ' = ' . $db->quoteName('#__com_yaquiz_question_quiz_map.question_id') . ')');
        $query->where($db->quoteName('#__com_yaquiz_question_quiz_map.quiz_id') . ' = ' . $db->quote($pk));
        $query->order('ordering ASC');
        $db->setQuery($query);
        $questions = $db->loadObjectList();
        //decode params
        foreach($questions as $question){
            $question->params = json_decode($question->params);
            $question->id = $question->question_id;
        }

        return $questions;
    }

    public function getQuestionParams($question_id)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from($db->quoteName('#__com_yaquiz_questions'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($question_id));
        $db->setQuery($query);
        $question_params = $db->loadObject();
        //decode
        $question_params = json_decode($question_params->params);
        return $question_params;
    }

    public function getQuestion($question_id)
    {

        Log::add('attempt get question with id'.$question_id, Log::INFO, 'com_yaquiz');
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__com_yaquiz_questions'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($question_id));
        $db->setQuery($query);
        $question = $db->loadObject();
        $question->params = json_decode($question->params);
        $question->correct_answer= $this->getCorrectAnswerText($question);
        Log::add('attempt load question id '.$question->id, Log::INFO, 'com_yaquiz');
        return $question;
    }

    public static function getQuestionNumbering($question_id, $quiz_id){

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('numbering');
        $query->from($db->quoteName('#__com_yaquiz_question_quiz_map'));
        $query->where($db->quoteName('question_id') . ' = ' . $db->quote($question_id));
        $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($quiz_id));

        $db->setQuery($query);
        $numbering = $db->loadResult();
        return $numbering;

    }

    public function getQuestionFromQuizOrdering($quiz_id, $order){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('question_id');
        $query->from($db->quoteName('#__com_yaquiz_question_quiz_map'));
        $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($quiz_id));
        $query->where($db->quoteName('ordering') . ' = ' . $db->quote($order));
        $db->setQuery($query);
        $question_id = $db->loadResult();
        return $this->getQuestion($question_id);
    }

    /**
     * @param $question_id int the id of the question
     * @param $answer string the (form value) answer submitted by the user
     */
    public function checkAnswer($question_id, $answer)
    {

        $app = Factory::getApplication();
        $gConfig = $app->getParams('com_yaquiz');

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('correct, answers');
        $query->from($db->quoteName('#__com_yaquiz_questions'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($question_id));
        $db->setQuery($query);
        $question = $db->loadObject();
        

        $params = $this->getQuestionParams($question_id);
        $type = $params->question_type;
        if ($type === 'multiple_choice') {
            $correct_answer = $question->correct;
            $answer = (int)$answer;
            if ($answer == $correct_answer) {
                return 1;
            } else {
                return 0;
            }
        }
        else if ($type ==='true_false'){
            $correct_answer = $question->correct;
            $answer = (int)$answer;
            if ($answer == $correct_answer) {
                return 1;
            } else {
                return 0;
            }
        }
        else if ($type==='fill_blank'){
            $possibleCorrectAnswers = json_decode($question->answers);
            $caseSensitive = $params->case_sensitive;
            $ignore_trailing = $gConfig->get('shortans_ignore_trailing', "1");
            if($ignore_trailing){
                $answer = rtrim($answer);
            }


            if($caseSensitive){
                if(in_array($answer, $possibleCorrectAnswers)){
                    return 1;
                }
                else{
                    return 0;
                }
            }
            else{
                $answer = strtolower($answer);
                $possibleCorrectAnswers = array_map('strtolower', $possibleCorrectAnswers);
                if(in_array($answer, $possibleCorrectAnswers)){
                    return 1;
                }
                else{
                    return 0;
                }
            }
            
        }
        else{
            return 0;
        }
    }

    public function getPossibleAnswers($question_id)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('answers');
        $query->from($db->quoteName('#__com_yaquiz_questions'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($question_id));
        $db->setQuery($query);
        $possible_answers = $db->loadObject();
        $possible_answers = json_decode($possible_answers->answers);
        return $possible_answers;
    }


    /**
     * @param $question_id int the id of the question
     * @return string|null the text of the correct answer if mchoice, all possible answers if fillinblank
    */
    public function getCorrectAnswerText($question)
    {
        $question_type = $question->params->question_type;
        if ($question_type === 'multiple_choice'){
            $possible_answers = $this->getPossibleAnswers($question->id);
            $correct_answer = $question->correct;
            $correct_answer_text = $possible_answers[$correct_answer];
            return Text::sprintf('COM_YAQ_S_WAS_THE_CORRECT_ANS', $correct_answer_text);
        }
        if ($question_type === 'true_false'){
            if($question->correct === '1'){
                return Text::_('COM_YAQ_TF_CORRECT_ANS_WAS_TRUE');
            }
            else{
                return Text::_('COM_YAQ_TF_CORRECT_ANS_WAS_FALSE');
            }
        }
        if ($question_type === 'fill_blank'){
            $possible_answers = json_decode($question->answers);
            $answerList = '';
            foreach($possible_answers as $answer){
                $answerList .= '<li>' . $answer . '</li>';
            }
            $answerList = '<ul>' . $answerList . '</ul>';
            return Text::_('COM_YAQ_FILLBLANK_ANYCORRECT').$answerList;
        }

        return null;
    }


    /**
     * @param $question_id int the id of the question
     * @param $useranswer string the user's answer
     * @return string|null the text of the selected answer if mchoice, the user's answer if fillinblank
    */
    public function getSelectedAnswerText($question_id, $useranswer){
        $question_type = $this->getQuestionParams($question_id)->question_type;
        if ($question_type === 'multiple_choice'){
            $possible_answers = $this->getPossibleAnswers($question_id);
            $selected_answer_text = $possible_answers[$useranswer];
            return $selected_answer_text;
        }
        if ($question_type === 'true_false'){
            if($useranswer === '1'){
                return 'True';
            }
            else{
                return 'False';
            }
        }
        if ($question_type === 'fill_blank'){
            return $useranswer;
        }
        return null;
    }


    /**
     * @param $pk int the id of the quiz
     * @return int the number of questions in the quiz
     */
    public function getTotalQuestions($pk = null){
        if ($pk === null) {
            $active = Factory::getApplication()->getMenu()->getActive();
            //get params from the menu item
            $pk = $active->getParams()->get('quiz_id');
        }
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from($db->quoteName('#__com_yaquiz_questions'));
        $query->join('INNER', $db->quoteName('#__com_yaquiz_question_quiz_map') . ' ON (' . $db->quoteName('#__com_yaquiz_questions.id') . ' = ' . $db->quoteName('#__com_yaquiz_question_quiz_map.question_id') . ')');
        $query->where($db->quoteName('#__com_yaquiz_question_quiz_map.quiz_id') . ' = ' . $db->quote($pk));
        $db->setQuery($query);
        $total_questions = $db->loadResult();
        return $total_questions;
    }

        /**
     * Increment the hit count by 1 for a given quiz
     * @pk - the quiz id
     */
    public function countAsHit($pk){
            
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->update('#__com_yaquiz_quizzes');
        $query->set('hits = hits + 1');
        $query->where('id = ' . $pk);
        $db->setQuery($query);
        $db->execute();

    }

        /**
     * Increment the submission count by 1 for a given quiz
     * @pk - the quiz id
     */
    public function countAsSubmission($pk){
                
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true);
            $query->update('#__com_yaquiz_quizzes');
            $query->set('submissions = submissions + 1');
            $query->where('id = ' . $pk);
            $db->setQuery($query);
            $db->execute();
        
    }


    public function saveGeneralResults($quiz_id, $scorepercentage, $passfail){
        $app = Factory::getApplication();

        //see if $quiz_id exists in __com_yaquiz_results_general
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('quiz_id');
        $query->from($db->quoteName('#__com_yaquiz_results_general'));
        $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($quiz_id));
        $db->setQuery($query);
        $result = $db->loadResult();

        if($result){
            //get the existing total_average_score and submissions
            $query = $db->getQuery(true);
            $query->select('total_average_score, submissions');
            $query->from($db->quoteName('#__com_yaquiz_results_general'));
            $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($quiz_id));
            $db->setQuery($query);
            $results = $db->loadObject();
            $total_average_score = $results->total_average_score;
            $submissions = $results->submissions;

            $weighted_total_avg = ($total_average_score * $submissions) + $scorepercentage;
            $new_total_avg = $weighted_total_avg / ($submissions + 1);

            //update record
            $query = $db->getQuery(true);
            $query->update($db->quoteName('#__com_yaquiz_results_general'));
            $query->set($db->quoteName('submissions') . ' = ' . $db->quoteName('submissions') . ' + 1');
            if($passfail === 'pass'){
                $query->set($db->quoteName('total_times_passed') . ' = ' . $db->quoteName('total_times_passed') . ' + 1');
            }
            $query->set($db->quoteName('total_average_score') . ' = ' . $db->quote($new_total_avg));
            $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($quiz_id));
            $db->setQuery($query);
            $db->execute();
        }
        else{
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__com_yaquiz_results_general'));
            $query->columns(array($db->quoteName('quiz_id'), $db->quoteName('submissions'), $db->quoteName('total_average_score'), $db->quoteName('total_times_passed')));
            if($passfail == 'pass'){
                $query->values($db->quote($quiz_id) . ', 1, ' . $db->quote($scorepercentage) . ', 1');
            }
            else{
                $query->values($db->quote($quiz_id) . ', 1, ' . $db->quote($scorepercentage) . ', 0');
            }
            $db->setQuery($query);
            $db->execute();
        }






    }


    /**
     * Save the results of an individual quiz attempt
     * @results - the results object
     */
    public function saveIndividualResults($results, $quiz_record_results){

        $userid = Factory::getApplication()->getIdentity()->id;
        if($results->passfail == 'fail'){
            $results->passed = 0;
        }
        else{
            $results->passed = 1;
        }
        $score = $results->correct / $results->total * 100;
        //trim to 1 decimal place
        $results->score = round($score, 1);

        if($quiz_record_results == 3){
            $results->full_results = json_encode($results->questions);
        }
        else{
            $results->full_results = '';
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->insert($db->quoteName('#__com_yaquiz_results'));
        $query->columns(array(
            $db->quoteName('quiz_id'),
            $db->quoteName('user_id'), 
            $db->quoteName('score'), 
            $db->quoteName('points'), 
            $db->quoteName('total_points'), 
            $db->quoteName('passed'),
            $db->quoteName('full_results')));
        $query->values(
            $db->quote($results->quiz_id) . ', ' 
            . $db->quote($userid) . ', ' 
            . $db->quote($results->score) . ', ' 
            . $db->quote($results->correct) . ', ' 
            . $db->quote($results->total) . ', ' 
            . $db->quote($results->passed) . ', '
            . $db->quote($results->full_results)
        );

        $db->setQuery($query);
        $db->execute();

        //check for a record in __com_yaquiz_user_quiz_map linking this user to this quiz
        $query = $db->getQuery(true);
        $query->select('user_id');
        $query->from($db->quoteName('#__com_yaquiz_user_quiz_map'));
        $query->where($db->quoteName('user_id') . ' = ' . $db->quote($userid));
        $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($results->quiz_id));
        $db->setQuery($query);
        $result = $db->loadResult();

        if($result){
            //update record
            $query = $db->getQuery(true);
            $query->update($db->quoteName('#__com_yaquiz_user_quiz_map'));
            $query->set($db->quoteName('attempt_count') . ' = ' . $db->quoteName('attempt_count') . ' + 1');
            $query->where($db->quoteName('user_id') . ' = ' . $db->quote($userid));
            $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($results->quiz_id));
            $db->setQuery($query);
            $db->execute();
        }
        else{
            //insert record
            $query = $db->getQuery(true);
            $query->insert($db->quoteName('#__com_yaquiz_user_quiz_map'));
            $query->columns(array($db->quoteName('user_id'), $db->quoteName('quiz_id'), $db->quoteName('attempt_count')));
            $query->values($db->quote($userid) . ', ' . $db->quote($results->quiz_id) . ', 1');
            $db->setQuery($query);
            $db->execute();
        }


    }

    /**
     * Check if the user has reached the maximum number of attempts for this quiz
     * @quiz_id - the id of the quiz
     * @return - true if the user has reached the maximum number of attempts, false otherwise
     */
    public function reachedMaxAttempts($quiz_id){

        Log::add('id of quiz: ' . $quiz_id, Log::INFO, 'com_yaquiz');
        $max_attempts = (int)$this->getQuizParams($quiz_id)->max_attempts;
        Log::add('max_attempts: ' . $max_attempts, Log::INFO, 'com_yaquiz');
        if($max_attempts == 0){
            return false;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $user = Factory::getApplication()->getIdentity();
        $userid = $user->id;

        //if they're a guest...
        if($user->guest){
            return false;
        }

        $query = $db->getQuery(true);
        $query->select('attempt_count');
        $query->from($db->quoteName('#__com_yaquiz_user_quiz_map'));
        $query->where($db->quoteName('user_id') . ' = ' . $db->quote($userid));
        $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($quiz_id));
        $db->setQuery($query);
        $attempt_count = $db->loadResult();

        if(!$attempt_count){
            return false;
        }
        if($attempt_count >= $max_attempts){
            Log::add('attempt count is ' . $attempt_count . ' and max attempts is ' . $max_attempts . ' so max reached', Log::INFO, 'com_yaquiz');
            return true;
        }
        return false;

    }


    /**
     * Check if user is allowed to keep trying the quiz
     * @quiz_id - the id of the quiz
     * @return - the number of attempts left, 0 if none left, or -1 if unlimited
     */
    public function quizAttemptsLeft($quiz_id){
            
            $max_attempts = (int)$this->getQuizParams($quiz_id)->max_attempts;
            if($max_attempts == 0){
                return -1;
            }
    
            $db = Factory::getContainer()->get('DatabaseDriver');
            $user = Factory::getApplication()->getIdentity();
            $userid = $user->id;
    
            //if they're a guest...
            if($user->guest){
                return -1;
            }
    
            $query = $db->getQuery(true);
            $query->select('attempt_count');
            $query->from($db->quoteName('#__com_yaquiz_user_quiz_map'));
            $query->where($db->quoteName('user_id') . ' = ' . $db->quote($userid));
            $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($quiz_id));
            $db->setQuery($query);
            $attempt_count = $db->loadResult();

            if($attempt_count == 0){
                return $max_attempts;
            }
    
            if(!$attempt_count){
                return -1;
            }
            if($attempt_count >= $max_attempts){
                return 0;
            }
            return $max_attempts - $attempt_count;

    }


    public function getAttemptCount($quiz_id, $userid){
            

            $db = Factory::getContainer()->get('DatabaseDriver');
    
            $query = $db->getQuery(true);
            $query->select('attempt_count');
            $query->from($db->quoteName('#__com_yaquiz_user_quiz_map'));
            $query->where($db->quoteName('user_id') . ' = ' . $db->quote($userid));
            $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($quiz_id));
            $db->setQuery($query);
            $attempt_count = $db->loadResult();
    
            if(!$attempt_count){
                return 0;
            }
            return $attempt_count;
    
    }

}

