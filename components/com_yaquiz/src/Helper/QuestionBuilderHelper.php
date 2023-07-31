<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\Helper;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
use KevinsGuides\Component\Yaquiz\Site\Helper\ThemeHelper;

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
    }


    /**
     * @return string html for the question's form elements
     */
    public function buildQuestion($question, $quiz_params)
    {
        //get the question params
        $question_params = $question->params;
        //get question type
        $questionType = $question_params->question_type;
        $formatted_questionnum = '';
        if ($quiz_params->quiz_question_numbering == 1 && $questionType != 'html_section') {
            $the_question_number = QuizModel::getQuestionNumbering($question->id, $quiz_params->quiz_id);
            $formatted_questionnum = '<span class="questionnumber">' . $the_question_number . ')</span> ';
        } else {
            $this->questionnumber = '';
        }
        $itemMissing = '';

        $html = '';
        
        include(ThemeHelper::findFile('question_wrapper_header.php'));



        //if question type is multiple_choice
        if ($questionType == 'multiple_choice') {
            echo $this->buildMChoice($question, $question_params);
        } else if ($questionType == 'true_false') {
            echo $this->build_truefalse($question);
        } else if ($questionType == 'fill_blank') {
            echo $this->build_fill_blank($question);
        } else if ($questionType == 'html_section'){
            echo $this->build_html_section($question);
        }
         else {
            echo 'question type' . $questionType . ' not supported';
        }

        include(ThemeHelper::findFile('question_wrapper_footer.php'));

        return '';
        //return $html;
    }
    protected function buildMChoice($question, $question_params)
    {
        $html = '<div class="mchoice-holder">';
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
            $ans = '<input class="mchoice-rb" type="radio" name="answers[' . $question->id . ']" id="question_' . $question->id . '_answer_' . $answeridx . '" value="' . $answeridx . '" ' . ($defaultanswer == $answeridx ? 'checked' : '') . '/>';
            //add label
            $ans .= '<label class="form-check-label mchoice text-start mt-1" for="question_' . $question->id . '_answer_' . $answeridx . '">' . $answer . '</label>';
            $answeridx++;

            //add to array
            $answerArr[] = $ans;
        }
        //if randomize answers
        if ($question_params->randomize_mchoice == 1 || ($this->globalParams->get('randomize_mchoice') == 1 && $question_params->randomize_mchoice == -1)) {
            shuffle($answerArr);
        }
        //add answers to html
        foreach ($answerArr as $ans) {
            $html .= $ans;
        }
        //end radio button group
        $html .= '</div>';
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
        <label class="form-check-label mchoice text-start mt-1" for="answers[' . $question->id . ']t">'.Text::_('COM_YAQ_TRUE').'</label><br/>'
        ;
        $html .= '
        <input class="d-none" type="radio" name="answers[' . $question->id . ']" id="answers[' . $question->id . ']f" value="0" ' . ($defaultanswer == 0 ? 'checked' : '') . '/>
        <label class="form-check-label mchoice text-start mt-1" for="answers[' . $question->id . ']f">'.Text::_('COM_YAQ_FALSE').'</label><br/>
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

    protected function build_html_section($question)
    {
        $html = '<input type="hidden" name="answers[' . $question->id . ']" value="-1"/>';

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
    * @param $quiz_id - the quiz id
    * @param $results - the results object
    * @return $html - the html to display
    */
    public function buildResultsArea($quiz_id, $results)
    {
        $app = Factory::getApplication();
        $gConfig = $this->globalParams;

        //the default will be a simple x/x with percentage
        //trim to 2 decimal places
        $resultPercent = round((((int)$results->correct / (int)$results->total) * 100), 0);
        //get quiz params
        $quizParams = $this->getQuizParams($quiz_id);

        //get the quiz template style from global params
        $theme = $this->globalParams->get('theme', 'default');

        $html = '';
        $feedback = '';
        //check if quiz_showfeedback is 1
        if ($quizParams->quiz_showfeedback == 1) {
            //loop through results $results->question_feedback
            Log::add('from qbhelper looks like this'.print_r($results->questions, true), Log::INFO, 'yaquiz');
            $questionnum = 0;
            
            foreach ($results->questions as $question) {
                if ($quizParams->quiz_question_numbering == 1) {
                    $questionnum++;
                }
                $feedback .= $this->getQuestionFeedback($quiz_id, $question['question'], $question['iscorrect'], $question['useranswer'], $questionnum);
            }

        }

        //see if we are showing general stats about this quiz
        if($quizParams->quiz_show_general_stats == '-1'){
            $quizParams->quiz_show_general_stats = $gConfig->get('quiz_show_general_stats');
        }
        if($quizParams->quiz_show_general_stats == '1'){
            $gen_stats = $this->getGeneralQuizStats($quiz_id);
        }
        else{
            $gen_stats = null;
        }

        //include the template for the result_summary.php template
        include(ThemeHelper::findFile('result_summary.php'));
        $html .= $feedback;
        return $html;
    }


    protected function getQuestionFeedback($quiz_id, $question, $iscorrect, $useranswer, $questionnum)
    {
        include(ThemeHelper::findFile('result_wrapper.php'));
        return $html;
    }

    protected function getGeneralQuizStats($quiz_id){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__com_yaquiz_results_general'));
        $query->where($db->quoteName('quiz_id') . ' = ' . $db->quote($quiz_id));
        $db->setQuery($query);
        $results = $db->loadObjectList()[0];
        return $results;
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