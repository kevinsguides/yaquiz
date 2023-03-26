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
        if ($quiz_params->quiz_question_numbering == 1) {
            $this->questionnumber++;
            $formatted_questionnum = '<span class="questionnumber">' . $this->questionnumber . ' )</span> ';
        } else {
            $this->questionnumber = '';
        }
        $itemMissing = '';
        if(isset($question->defaultanswer) && $question->defaultanswer === 'missing'){
            $itemMissing .= '<i class="text-danger fas fa-exclamation-triangle me-2" title="You forgot to answer this..."></i>';
        }
        $html = '<div class="card"><h3 class="card-header">' . $itemMissing . $formatted_questionnum . $question->question . '</h3>';

        $html .= '<div class="card-body">' . $question->details . '<hr/>';
        //if question type is multiple_choice
        if ($questionType == 'multiple_choice') {
            $html .= $this->buildMChoice($question, $params);
        } 
        else if ($questionType == 'true_false'){
            $html .= $this->build_truefalse($question);
        }
        
        else {
            $html .= 'question type' . $questionType . ' not supported';
        }
        $html .= '</div>';
        //if quiz uses points system
        if($quiz_params->quiz_use_points == 1) {
            $html .= '<div class="card-footer">This question is worth ' . $params->points . ' point'.($params->points > 1 ?  's' :  '').'</div>';
        }

        $html .= '</div>';
        return $html;
    }
    protected function buildMChoice($question, $params)
    {
        $html = '';
        //get the answers
        $answers = $question->answers;
        //decode
        $answers = json_decode($answers);
        //for each answer
        $answeridx = 0;
        $answerArr = array();
        //if we are retrying
        if (isset($question->defaultanswer)){
            $defaultanswer = $question->defaultanswer;
        }
        else{
            $defaultanswer = -1;
        }
        foreach ($answers as $answer) {
            $ans = '<div class="form-check">';
            //add radio button
            $ans .= '<input class="form-check-input" type="radio" name="answers[' . $question->question_id . ']" id="question_' . $question->question_id . '_answer_' . $answeridx . '" value="' . $answeridx . '" '.($defaultanswer==$answeridx?'checked':'').'/>';
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


    protected function build_truefalse($question){

        if (isset($question->defaultanswer)){
            $defaultanswer = $question->defaultanswer;
        }
        else{
            $defaultanswer = -1;
        }

        $html = '
        <div class="form-check">
        <input class="form-check-input" type="radio" name="answers['.$question->question_id.']" id="answers['.$question->question_id.']t" value="1" '.($defaultanswer==1?'checked':'').'/>
        <label for="answers['.$question->question_id.']t">True</label><br/>'
        ;
        $html .= '
        <input class="form-check-input" type="radio" name="answers['.$question->question_id.']" id="answers['.$question->question_id.']f" value="0" '.($defaultanswer==0?'checked':'').'/>
        <label for="answers['.$question->question_id.']f">False</label><br/>
        ';
        $html .= '</div>';
        return $html;
    }

    public function getQuizParams($pk)
    {
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
        $resultPercent = round((($results->correct / $results->total) * 100), 0);
        //get quiz params
        $quizParams = $this->getQuizParams($quiz_id);

        $html = '';
        $feedback = '';
        //check if quiz_showfeedback is 1
        if ($quizParams->quiz_showfeedback == 1) {
            //loop through results $results->question_feedback
            Log::add(print_r($results->questions, true), Log::INFO, 'simplequiz');
            $questionnum = 0;
            foreach ($results->questions as $question) {
                if ($quizParams->quiz_question_numbering == 1) {
                    $questionnum++;
                }
                $feedback .= $this->getQuestionFeedback($quiz_id, $question['question'], $question['iscorrect'], $question['useranswer'], $questionnum);
            }
        }
        $pointtext = 'questions right.';
        if ($quizParams->quiz_use_points === '1') {
            $pointtext = 'points.';
        }
        $html .= '<div class="card m-1 mb-3"><div class="card-body">';
        $html .= '<h1><i class="fas fa-info-circle"></i> Results: ' . $title . '</h3><hr/>';
        $html .= '<p>You got ' . $results->correct . ' out of ' . $results->total . ' ' . $pointtext . '</p>';
        $html .= '<p>That is a ' . $resultPercent . '%</p>';
        //progress bar display
        $passColor = ($results->passfail === 'pass') ? 'bg-success' : 'bg-danger';
        $html .= '<div class="progress" role="progressbar" aria-label="Success example" aria-valuenow="' . $resultPercent . '" aria-valuemin="0" aria-valuemax="100">';
        $html .= '<div class="progress-bar ' . $passColor . '" style="width: ' . $resultPercent . '%">' . $resultPercent . '%</div>  </div>';
        $html .= '<br/>';
        if ($results->passfail === 'pass') {
            $html .= '<p class="p-3 bg-light text-success">' . $this->globalParams->get('lang_pass') . '</p>';
        } else {
            $html .= '<p class="p-3 bg-light text-danger">' . $this->globalParams->get('lang_fail') . '</p>';
        }
        $html .= '</div></div>';
        $html .= $feedback;
        return $html;
    }
    protected function getQuestionFeedback($quiz_id, $question, $iscorrect, $useranswer, $questionnum)
    {
        $quizParams = $this->getQuizParams($quiz_id);
        $feedback = '';
        $pointsFeedback = '';
        if ($questionnum != 0) {
            $questionnum = $questionnum . ') ';
        } else {
            $questionnum = '';
        }
        if ($quizParams->quiz_use_points === '1') {
            if ($iscorrect) {
                $pointsFeedback = $question->params->points . ' / ' . $question->params->points . ' points';
            } else {
                $pointsFeedback = '0 / ' . $question->params->points . ' points';
            }
        }



        if ($iscorrect) {
            $feedback .= '<div class="card m-1 mb-3">';
            $feedback .= '<h3 class="card-header bg-success text-light">' . $questionnum . '<i class="fas fa-check-circle float-end"></i> ' . $question->question . '</h3><div class="card-body">';
            $feedback .= $question->details;
            $feedback .= '<br/>';
            $feedback .= '<p>The answer was: ' . $question->correct_answer . '</p>';
            $feedback .= '</div>';
            if ($question->feedback_right != '' || $pointsFeedback != '') {
                $feedback .= '<div class="card-footer">' . $question->feedback_right . ' <span class="float-end">' . $pointsFeedback . '</span></div>';
            }
            $feedback .= '</div>';
        } else {
            $feedback .= '<div class="card m-1 mb-3">';
            $feedback .= '<h3 class="card-header bg-danger text-light">' . $questionnum . '<i class="fas fa-times-circle float-end"></i> ' . $question->question . '</h3>';
            $feedback .= '<div class="card-body">' . $question->details;
            $feedback .= '<br/>';
            $feedback .= '<p>You answered: ' . $useranswer . '</p>';
            if($quizParams->quiz_feedback_showcorrect === '1'){
                $feedback .= 'The correct answer was: '. $question->correct_answer;
            }
            $feedback .= '</div>';
            if ($question->feedback_wrong != '' || $pointsFeedback != '') {
                $feedback .= '<div class="card-footer">' . $question->feedback_wrong . ' <span class="float-end">' . $pointsFeedback . '</span></div>';
            }
            $feedback .= '</div>';
        }
        return $feedback;
    }
}