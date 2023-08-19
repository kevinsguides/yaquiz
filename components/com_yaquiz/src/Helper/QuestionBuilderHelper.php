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
    public function buildQuestion($question, $quiz_params, $quiz_id = null)
    {
        //get the question params
        $question_params = $question->params;
        //get question type
        $questionType = $question_params->question_type;
        $formatted_questionnum = '';
        if ($quiz_params->get('quiz_question_numbering', 1) == 1 && $questionType != 'html_section') {
            $the_question_number = QuizModel::getQuestionNumbering($question->id, $quiz_id);
            $formatted_questionnum = '<span class="questionnumber">' . $the_question_number . ')</span> ';
        } else {
            $this->questionnumber = '';
        }
        $itemMissing = '';

        $html = '';

        if($quiz_params->get('quiz_displaymode', 'default') == 'default'){
            include(ThemeHelper::findFile('singlepage_question_before.php'));
        }
        elseif($quiz_params->get('quiz_displaymode', 'default')  == 'individual'){
            include(ThemeHelper::findFile('oneperpage_question_before.php'));
        }
        elseif($quiz_params->get('quiz_displaymode', 'default')  == 'jsquiz'){
            include(ThemeHelper::findFile('jsquiz_question_before.php'));
        }
        

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

        if($quiz_params->get('quiz_displaymode', 'default')  == 'default'){
            include(ThemeHelper::findFile('singlepage_question_after.php'));
        }
        elseif($quiz_params->get('quiz_displaymode', 'default')  == 'individual'){
            include(ThemeHelper::findFile('oneperpage_question_after.php'));
        }
        elseif($quiz_params->get('quiz_displaymode', 'default')  == 'jsquiz'){
            include(ThemeHelper::findFile('jsquiz_question_after.php'));
        }

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
        <input class="mchoice-rb"  type="radio" name="answers[' . $question->id . ']" id="answers[' . $question->id . ']t" value="1" ' . ($defaultanswer == 1 ? 'checked' : '') . '/>
        <label class="form-check-label mchoice text-start mt-1" for="answers[' . $question->id . ']t">'.Text::_('COM_YAQ_TRUE').'</label><br/>'
        ;
        $html .= '
        <input class="mchoice-rb"  type="radio" name="answers[' . $question->id . ']" id="answers[' . $question->id . ']f" value="0" ' . ($defaultanswer == 0 ? 'checked' : '') . '/>
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

    /*
    * Called by the QuizController on form submit...
    * @param $quiz_id - the quiz id
    * @param $results - the results object
    * @return $html - the html to display
    */
    public function buildResultsArea($quiz_id, $results, $result_id = 0)
    {
        $app = Factory::getApplication();
        
        $gConfig = $this->globalParams;

        if($result_id == 0){
            //check in app input
            $result_id = $app->input->getInt('resultid', 0);
        }


        //the default will be a simple x/x with percentage
        //trim to 2 decimal places
        $resultPercent = round((((int)$results->correct / (int)$results->total) * 100), 0);
        $model = new QuizModel();
        //get quiz params
        $quizParams = $model->getQuizParams($quiz_id);

        //get the quiz template style from global params
        $theme = $this->globalParams->get('theme', 'default');

        $html = '';
        $feedback = '';
        //check if quiz_showfeedback is 1
        if ($quizParams->get('quiz_showfeedback', 1) == 1) {
            //loop through results $results->question_feedback
            //Log::add('from qbhelper looks like this'.print_r($results->questions, true), Log::INFO, 'yaquiz');
            $questionnum = 0;
            
            foreach ($results->questions as $question) {
                if ($quizParams->get('quiz_question_numbering', 1) == 1) {
                    $questionnum++;
                }
                $feedback .= $this->getQuestionFeedback($quiz_id, $question['question'], $question['iscorrect'], $question['useranswer'], $questionnum);
            }

        }

        //see if we are showing general stats about this quiz
        if($quizParams->get('quiz_show_general_stats', 0) == '1'){
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
        $model = new QuizModel();
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


    //render all questions for singlepage layout
    public function renderAllQuestions($questions, $quizparams, $quiz_id, $oldanswers = null)
    {

        $app = Factory::getApplication();
        $i = 0;
        foreach ($questions as $question){
            if($question->params->question_type != 'html_section'){
                $i++;
            }
            
            $actualid = $question->id;
            //check if $oldanswers is set
            if ($oldanswers) {
                //see if this question is in the old answers
                foreach ($oldanswers as $oldanswer) {
                    if ($oldanswer['question_id'] == $actualid) {
                        $question->defaultanswer = $oldanswer['answer'];
                    }
                }
                if (!isset($question->defaultanswer) && ($app->input->get('status') == 'retry')) {
                    $question->defaultanswer = 'missing';
                }
            }

            $question->question_number = $i;
            echo $this->buildQuestion($question, $quizparams, $quiz_id);
        }

    }

}