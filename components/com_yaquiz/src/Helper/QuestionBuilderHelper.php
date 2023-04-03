<?php
namespace KevinsGuides\Component\Yaquiz\Site\Helper;

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
        $this->globalParams = Factory::getApplication()->getParams('com_yaquiz');
        $this->db = Factory::getContainer()->get('DatabaseDriver');
        $this->theme = $this->globalParams->get('theme');
    }


    /**
     * @return string html for the question's form elements
     */
    public function buildQuestion($question, $quiz_params)
    {
        //get the question params
        $params = $question->params;
        //get question type
        $questionType = $params->question_type;
        $formatted_questionnum = '';
        if ($quiz_params->quiz_question_numbering == 1) {
            $formatted_questionnum = '<span class="questionnumber">' . $question->question_number . ')</span> ';
        } else {
            $this->questionnumber = '';
        }
        $itemMissing = '';

        $html = '';
        
        $question_wrapper_header = (JPATH_SITE . '/components/com_yaquiz/tmpl/quiz/' . $this->theme . '/question_wrapper_header.php');
        $question_wrapper_footer = (JPATH_SITE . '/components/com_yaquiz/tmpl/quiz/' . $this->theme . '/question_wrapper_footer.php');
        include($question_wrapper_header);



        //if question type is multiple_choice
        if ($questionType == 'multiple_choice') {
            echo $this->buildMChoice($question, $params);
        } else if ($questionType == 'true_false') {
            echo $this->build_truefalse($question);
        } else if ($questionType == 'fill_blank') {
            echo $this->build_fill_blank($question);
        } else {
            echo 'question type' . $questionType . ' not supported';
        }

        include($question_wrapper_footer);

        return '';
        //return $html;
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
        if (isset($question->defaultanswer)) {
            $defaultanswer = $question->defaultanswer;
        } else {
            $defaultanswer = -1;
        }
        foreach ($answers as $answer) {
            //add radio button
            $ans = '<input class="d-none" type="radio" name="answers[' . $question->id . ']" id="question_' . $question->id . '_answer_' . $answeridx . '" value="' . $answeridx . '" ' . ($defaultanswer == $answeridx ? 'checked' : '') . '/>';
            //add label
            $ans .= '<label class="form-check-label mchoice btn btn-dark text-start mt-1" for="question_' . $question->id . '_answer_' . $answeridx . '">' . $answer . '</label>';
            $answeridx++;

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


    protected function build_truefalse($question)
    {

        if (isset($question->defaultanswer)) {
            $defaultanswer = $question->defaultanswer;
        } else {
            $defaultanswer = -1;
        }

        $html = '
        <input class="d-none" type="radio" name="answers[' . $question->id . ']" id="answers[' . $question->id . ']t" value="1" ' . ($defaultanswer == 1 ? 'checked' : '') . '/>
        <label class="btn btn-success w-100 text-center truefalse-t" for="answers[' . $question->id . ']t">True</label><br/>'
        ;
        $html .= '
        <input class="d-none" type="radio" name="answers[' . $question->id . ']" id="answers[' . $question->id . ']f" value="0" ' . ($defaultanswer == 0 ? 'checked' : '') . '/>
        <label class="btn btn-danger w-100 text-center truefalse-f" for="answers[' . $question->id . ']f">False</label><br/>
        ';
        return $html;
    }

    protected function build_fill_blank($question)
    {

        if (isset($question->defaultanswer)) {
            $defaultanswer = $question->defaultanswer;
        } else {
            $defaultanswer = '';
        }

        $html = '';
        $html .= '<input type="text" name="answers[' . $question->id . ']" value="' . $defaultanswer . '"/>';
        return $html;
    }

    public function getQuizParams($pk)
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from($db->quoteName('#__com_yaquiz_quizzes'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($pk));
        $db->setQuery($query);
        $quiz_params = $db->loadObject();
        //decode
        $quiz_params = json_decode($quiz_params->params);
        return $quiz_params;
    }


    /*
    * Called by the QuizController on form submit...
    * @param $title - the quiz params
    * @param $quiz_id - the quiz id
    * @param $results - the results object
    * @return $html - the html to display
    */
    public function buildResultsArea($title, $quiz_id, $results)
    {
        //the default will be a simple x/x with percentage
        //trim to 2 decimal places
        $resultPercent = round((($results->correct / $results->total) * 100), 0);
        //get quiz params
        $quizParams = $this->getQuizParams($quiz_id);

        //get the quiz template style from global params

        $theme = $this->globalParams->get('theme', 'default');

        $html = '';
        $feedback = '';
        //check if quiz_showfeedback is 1
        if ($quizParams->quiz_showfeedback == 1) {
            //loop through results $results->question_feedback
            Log::add(print_r($results->questions, true), Log::INFO, 'yaquiz');
            $questionnum = 0;
            foreach ($results->questions as $question) {
                if ($quizParams->quiz_question_numbering == 1) {
                    $questionnum++;
                }
                $feedback .= $this->getQuestionFeedback($quiz_id, $question['question'], $question['iscorrect'], $question['useranswer'], $questionnum);
            }
        }

        //include the template for the result_summary.php template
        $template = (JPATH_SITE . '/components/com_yaquiz/tmpl/quiz/' . $theme . '/result_summary.php');
        include($template);
        $html .= $feedback;
        return $html;
    }


    protected function getQuestionFeedback($quiz_id, $question, $iscorrect, $useranswer, $questionnum)
    {
        $theme = $this->globalParams->get('theme', 'default');
        $template = (JPATH_SITE . '/components/com_yaquiz/tmpl/quiz/' . $theme . '/result_wrapper.php');

        include($template);
        return $html;


    }


    /**
     * Checks if the user already answered this question
     * @param $quiz_id int
     * @param $question_id int
     * @return null if not answered, otherwise the answer
     */
    public function checkAnswerInSession($quiz_id, $question_id)
    {
        Log::add('Checking answer in session for quiz: ' . $quiz_id . ' question: ' . $question_id, Log::INFO, 'yaquiz');
        $session = Factory::getApplication()->getSession();
        $answers = $session->get('sq_answers');
        if (isset($answers[$quiz_id][$question_id])) {
            Log::add('Answer found in session: ' . $answers[$quiz_id][$question_id], Log::INFO, 'yaquiz');
            return $answers[$quiz_id][$question_id];
        } else {
            return null;
        }

    }

}