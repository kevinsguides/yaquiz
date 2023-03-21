<?php
namespace KevinsGuides\Component\SimpleQuiz\Site\Model;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\MVC\Model\ItemModel;

defined('_JEXEC') or die;


class QuizModel extends ItemModel{

    public function __construct($config = array(), MVCFactoryInterface $factory = null)
    {
        Log::add('QuizModel::__construct() called', Log::INFO, 'com_simplequiz');
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
        Log::add('QuizModel::getItem() called with pk '.$pk, Log::INFO, 'com_simplequiz');
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__simplequiz_quizzes'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($pk));
        $db->setQuery($query);
        $quiz = $db->loadObject();
        return $quiz;
	}

    public function getQuizParams($pk = null){
        if($pk == null){
            $pk = $this->getState('quiz.id');
        }
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from($db->quoteName('#__simplequiz_quizzes'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($pk));
        $db->setQuery($query);
        $params = $db->loadResult();
        return json_decode($params);
        

    }



    public function getQuestions($pk = null)
    {

        //the __simplequiz_question_quiz_map table has question_id and quiz_id cols
        //need to join with the questions table to get the questions for this quiz
        //get pk from GET
        $pk = $this->getState('quiz.id');

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__simplequiz_questions'));
        $query->join('INNER', $db->quoteName('#__simplequiz_question_quiz_map') . ' ON (' . $db->quoteName('#__simplequiz_questions.id') . ' = ' . $db->quoteName('#__simplequiz_question_quiz_map.question_id') . ')');
        $query->where($db->quoteName('#__simplequiz_question_quiz_map.quiz_id') . ' = ' . $db->quote($pk));
        $db->setQuery($query);
        $questions = $db->loadObjectList();
        return $questions;
    }

    public function getQuestionParams($question_id)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from($db->quoteName('#__simplequiz_questions'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($question_id));
        $db->setQuery($query);
        $question_params = $db->loadObject();
        //decode
        $question_params = json_decode($question_params->params);
        return $question_params;
    }

    public function getQuestion($question_id)
    {
        Log::add('GOT TO GET QUESTION', Log::INFO, 'com_simplequiz');
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__simplequiz_questions'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($question_id));
        $db->setQuery($query);
        $question = $db->loadObject();
        $question->params = json_decode($question->params);
        $question->correct_answer= $this->getCorrectAnswerText($question);
        return $question;
    }

    public function checkAnswer($question_id, $answer)
    {
        Log::add('checkAnswer() called with question_id '.$question_id.' and answer '.$answer, Log::INFO, 'com_simplequiz');
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('correct');
        $query->from($db->quoteName('#__simplequiz_questions'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($question_id));
        $db->setQuery($query);
        $correct_answer = $db->loadObject();
        $correct_answer = $correct_answer->correct;

        $params = $this->getQuestionParams($question_id);
        $type = $params->question_type;
        if ($type === 'multiple_choice') {
            $answer = (int)$answer;
            if ($answer == $correct_answer) {
                return 1;
            } else {
                return 0;
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
        $query->from($db->quoteName('#__simplequiz_questions'));
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
            //log all possible answers...
            Log::add('possible answers: '.print_r($possible_answers, true), Log::INFO, 'com_simplequiz');
            $correct_answer = $question->correct;
            $correct_answer_text = $possible_answers[$correct_answer];
            return $correct_answer_text;
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
        return null;
    }
}

