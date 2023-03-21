<?php
namespace KevinsGuides\Component\SimpleQuiz\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

class QuestionBuilderHelper
{

    protected $globalParams;
    protected $questionnumber;
    //get config from component
    public function __construct()
    {
        $this->globalParams = Factory::getApplication()->getParams('com_simplequiz');
        $this->db = Factory::getContainer()->get('DatabaseDriver');
        $this->questionnumber = 0;
    }


    public function buildQuestion($question, $quiz_params)
    {

        //get the question params
        $params = json_decode($question->params);

        //get question type
        $questionType = $params->question_type;
        $formatted_questionnum = '';
        if($quiz_params->quiz_question_numbering == 1){
            $this->questionnumber++;
            $formatted_questionnum = '<span class="questionnumber">'.$this->questionnumber.' )</span> ';
        }
        else{
            $this->questionnumber = '';
        }
        $html = '<div class="card"><h3 class="card-header">'. $formatted_questionnum . $question->question . '</h3>';
        $html .= '<div class="card-body">' . $question->details . '<hr/>';

        //if question type is multiple_choice
        if ($questionType == 'multiple_choice') {
            $html .= $this->buildMChoice($question,  $params);

        } else {
            $html .= 'question type' . $questionType . ' not supported';
        }

        $html .= '</div></div>';

        return $html;

    }

    protected function buildMChoice($question, $params)
    {

        //get the answers
        $answers = $question->answers;
        //decode
        $answers = json_decode($answers);
        //for each answer
        $answeridx = 0;
        $answerArr = array();
        foreach ($answers as $answer) {
            $ans = '<div class="form-check">';
            //add radio button
            $ans .= '<input class="form-check-input" type="radio" name="answers[' . $question->question_id . ']" id="question_' . $question->question_id . '_answer_' . $answeridx . '" value="' . $answeridx . '" />';
            //add label
            $ans .= '<label class="form-check-label mchoice btn btn-dark text-start" for="question_' . $question->question_id . '_answer_' . $answeridx . '">' . $answer . '</label>';
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
            Log::add(print_r($results->questions, true), Log::INFO, 'simplequiz');
            foreach ($results->questions as $question){

$feedback .= $this->getQuestionFeedback($quiz_id, $question['question'], $question['iscorrect'], $question['useranswer']);
            }

        }
        $pointtext = 'questions right.';
        if ($quizParams->quiz_use_points === '1'){
            $pointtext = 'points.';
        }


        $html .= '<div class="card m-1 mb-3"><div class="card-body">';
        $html .= '<h1><i class="fa-solid fa-chart-simple"></i> Results: ' . $title . '</h3>';
        $html .= '<p>You got ' . $results->correct . ' out of ' . $results->total . ' '.$pointtext.'</p>';
        $html .= '<p>That is a ' . $resultPercent . '%</p>';
        if($results->passfail === 'pass'){
            $html .= '<p class="p-3 bg-success text-light">You passed!</p>';
        }
        else{
            $html .= '<p class="p-3 bg-danger text-light">You failed.</p>';
        }
        $html .= '</div></div>';
        $html .= $feedback;

        return $html;
    }

    protected function getQuestionFeedback($quiz_id, $question, $iscorrect, $useranswer){
        $quizParams = $this->getQuizParams($quiz_id);
        $feedback = '';
        $pointsFeedback = '';
        if($quizParams->quiz_use_points === '1'){
            if($iscorrect){
                $pointsFeedback = $question->params->points . ' / ' . $question->params->points . ' points';
            }
            else{
                $pointsFeedback = '0 / ' . $question->params->points . ' points';
            }
        }
        
        if ($iscorrect){
            $feedback .= '<div class="card m-1 mb-3">';
            $feedback .= '<h3 class="card-header bg-success text-light"><i class="fa-solid fa-circle-check float-end"></i> Question: ' . $question->question . '</h3><div class="card-body">';
            $feedback .= $question->details;
            $feedback .= '<br/>';
            $feedback .= '<p>The answer was: ' . $question->correct_answer . '</p>';
            $feedback .= '</div><div class="card-footer">' . $question->feedback_right . ' <span class="float-end">'.$pointsFeedback.'</span></div>';
            $feedback .= '</div>';


        } else {
            $feedback .= '<div class="card m-1 mb-3">';
            $feedback .= '<h3 class="card-header bg-danger text-light"><i class="fa-solid fa-circle-xmark float-end"></i> ' . $question->question . '</h3>';
            $feedback .= '<div class="card-body">'.$question->details;
            $feedback .= '<br/>';
            $feedback .= '<p>You answered: ' . $useranswer . '</p>';
            
            $feedback .= '</div><div class="card-footer">' . $question->feedback_wrong . ' <span class="float-end">'.$pointsFeedback.'</span></div>';
            $feedback .= '</div>';

        }
        return $feedback;


    }
}