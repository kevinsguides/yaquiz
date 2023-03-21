<?php
namespace KevinsGuides\Component\SimpleQuiz\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

class QuestionBuilderHelper
{

    protected $globalParams;
    //get config from component
    public function __construct()
    {
        $this->globalParams = Factory::getApplication()->getParams('com_simplequiz');
        $this->db = Factory::getContainer()->get('DatabaseDriver');
    }


    public function buildQuestion($question)
    {

        //get the question params
        $params = json_decode($question->params);

        //get question type
        $questionType = $params->question_type;

        $html = '';

        //if question type is multiple_choice
        if ($questionType == 'multiple_choice') {

            return $this->buildMChoice($question,  $params);

        } else {
            return 'question type' . $questionType . ' not supported';
        }

    }

    protected function buildMChoice($question, $params)
    {

        //get the answers
        $answers = $question->answers;
        //decode
        $answers = json_decode($answers);

        $html .= '<div class="card"><h3 class="card-header">' . $question->question . '</h3>';
        $html .= '<div class="card-body">' . $question->details . '<hr/>';

        //for each answer
        $answeridx = 0;
        $answerArr = array();
        foreach ($answers as $answer) {
            $ans = '<div class="form-check">';
            //add radio button
            $ans .= '<input class="form-check-input" type="radio" name="answers[' . $question->question_id . ']" id="question_' . $question->question_id . '_answer_' . $answeridx . '" value="' . $answeridx . '" />';
            //add label
            $ans .= '<label class="form-check-label" for="question_' . $question->question_id . '_answer_' . $answeridx . '">' . $answer . '</label>';
            $answeridx++;
            $ans .= '</div>';
            //add to array
            $answerArr[] = $ans;
        }

        
        //if randomize answers
        if ($params->randomize_mchoice == 1 || ($this->globalParams->get('randomize_mchoice') == 1 && $params->randomize_mchoice == -1)) {
            shuffle($answerArr);
        }
        //add answers to html
        foreach ($answerArr as $ans) {
            $html .= $ans;
        }

        //end radio button group
        $html .= '</div></div>';
        return $html;

    }

    public function getQuizParams($pk){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from($db->quoteName('#__simplequiz_quizzes'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($pk));
        $db->setQuery($query);
        $quiz_params = $db->loadObject();
        //decode
        $quiz_params = json_decode($quiz_params->params);
        return $quiz_params;
    }

    public function buildResultsArea($title, $quiz_id, $results)
    {


        //the default will be a simple x/x with percentage
        //trim to 2 decimal places
        $resultPercent = round((($results->correct / $results->total) * 100), 2);

        //get quiz params
        $quizParams = $this->getQuizParams($quiz_id);

        $html = '';
        $feedback = '';


        //check if quiz_showfeedback is 1
        if ($quizParams->quiz_showfeedback == 1){
            //loop through results $results->question_feedback
            foreach ($results->feedback as $question_feedback){
$feedback .= $this->getQuestionFeedback($quiz_id, $question_feedback['question_id'], $question_feedback['correct'], $question_feedback['correctanswer'], $question_feedback['useranswer']);
            }

        }



      

        $html .= '<div class="card m-1 mb-3"><div class="card-body">';
        $html .= '<h1><i class="fa-solid fa-chart-simple"></i> Results: ' . $title . '</h3>';
        $html .= '<p>You got ' . $results->correct . ' out of ' . $results->total . ' questions correct.</p>';
        $html .= '<p>That is a ' . $resultPercent . '%</p>';
        $html .= '</div></div>';
        $html .= $feedback;

        return $html;
    }

    protected function getQuestionFeedback($quiz_id, $question_id, $isCorrect, $correctanswer, $useranswer){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('question, feedback_right, feedback_wrong');
        $query->from($db->quoteName('#__simplequiz_questions'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($question_id));
        $db->setQuery($query);
        $question = $db->loadObject();
        $quizParams = $this->getQuizParams($quiz_id);
        Log::add('found these quizparams: ' . print_r($quizParams, true), Log::INFO, 'com_simplequiz');
        $feedback = '';
        
        if ($isCorrect){
            $feedback .= '<div class="card m-1 mb-3">';
            $feedback .= '<h3 class="card-header bg-success text-light"><i class="fa-solid fa-circle-check float-end"></i> Question: ' . $question->question . '</h3><div class="card-body">';
            $feedback .= '<p>Correct!</p>';
            $feedback .= '<p>The answer was: ' . $correctanswer . '</p>';
            $feedback .= '<p>' . $question->feedback_right . '</p>';
            $feedback .= '</div></div>';


        } else {
            $feedback .= '<div class="card m-1 mb-3">';
            $feedback .= '<h3 class="card-header bg-danger text-light"><i class="fa-solid fa-circle-xmark float-end"></i> ' . $question->question . '</h3>';
            $feedback .= '<div class="card-body"><p>Incorrect!</p>';
            $feedback .= '<p>You answered: ' . $useranswer . '</p>';
            if($quizParams->quiz_feedback_showcorrect === '1'){
                $feedback .= '<p>The correct answer was: ' . $correctanswer . '</p>';
            }
            
            $feedback .= '<p>' . $question->feedback_wrong . '</p>';
            $feedback .= '</div></div>';

        }

        return $feedback;


    }
}