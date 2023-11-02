<?php
/**
 * @copyright (C) 2023 KevinsGuides.com
 * @license GNU General Public License version 2 or later;
 * 
 * This file produces a quiz using javascript
 * it doesn't save results or anything (yet) Just allows users to take quiz and see results
 */



namespace KevinsGuides\Component\Yaquiz\Site\View\Quiz;
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use KevinsGuides\Component\Yaquiz\Site\Helper\QuestionBuilderHelper;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
use Joomla\CMS\Language\Text;

$model = new QuizModel();

$app = Factory::getApplication();
$wam = $app->getDocument()->getWebAssetManager();
$wam->registerAndUseScript('com_yaquiz.instareview', 'components/com_yaquiz/js/instareview.js', [], ['defer' => true]);
$wam->registerAndUseStyle('com_yaquiz.quiz', 'components/com_yaquiz/src/Style/quiz.css');
$gConfig = $app->getParams('com_yaquiz');

if ($gConfig->get('get_mathjax') === '1') {
    $wam->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js', [], ['defer' => true]);
}
if ($gConfig->get('get_mathjax') === '2') {
    Log::add('Loading local mathjax', Log::INFO, 'com_yaquiz');
    $wam->registerAndUseScript('com_yaquiz.mathjaxlocal', 'components/com_yaquiz/js/mathjax/es5/tex-svg.js', [], ['defer' => true]);
}

$theme = $gConfig->get('theme','default');
$stylefile = '/components/com_yaquiz/tmpl/' . $theme . '/style.css';
//if file exists
if (file_exists(JPATH_ROOT . $stylefile)) {
    $wam->registerAndUseStyle('com_yaquiz.quizstyle', $stylefile);
}



$qbHelper = new QuestionBuilderHelper();


//if $this-> item is already set
if (isset($this->item)) {
    $quiz = $this->item;
} else {
    $quiz = $this->get('Item');
}

//get quiz params
$quiz_params = $model->getQuizParams($quiz->id);

$uses_point_system = false;

if($quiz_params->get('quiz_use_points', "1") === "1"){
    $uses_point_system = true;
}




//loop through all questions
$questions = $model->getQuestions($quiz->id);


//create array of questions, answers, and feedback
$question_array = array();
foreach ($questions as $question) {
    $question_array[$question->id] = array(
        'question' => $question->question,
        'question_type' => $question->params->question_type,
        'answers' => $question->answers ? json_decode($question->answers) : '',
        'correct' => $question->correct,
        'details' => $question->details,
        'feedback_right' => $question->feedback_right,
        'feedback_wrong' => $question->feedback_wrong,
        'points' => $question->params->points,
    );
}



$doc = Factory::getApplication()->getDocument();

$quizData = array(
    'default_correct_text' => Text::_('COM_YAQ_CORRECTANS'),
    'default_incorrect_text' => Text::_('COM_YAQ_INCORRECTANS'),
    'lang_youranswer' => Text::_('COM_YAQ_YOURANSWER'),
    'lang_true' => Text::_('COM_YAQ_TRUE'),
    'lang_false' => Text::_('COM_YAQ_FALSE'),
    'lang_submit' => Text::_('COM_YAQ_SUBMIT'),
    'display_feedback' => $quiz_params->get('quiz_showfeedback', 1),
    'display_correct' => $quiz_params->get('quiz_feedback_showcorrect', 1),
    'use_point_system' => $uses_point_system ? true : false,
    'passing_score' => $quiz_params->get('passing_score', 70),
    'lang_s_was_correct' => Text::_('COM_YAQ_S_WAS_THE_CORRECT_ANS'),
    'lang_was_correct_if_contained' => Text::_('COM_YAQ_FILLBLANK_ANYCORRECT'),
    'lang_true_was_correct' => Text::_('COM_YAQ_TF_CORRECT_ANS_WAS_TRUE'),
    'lang_false_was_correct' => Text::_('COM_YAQ_TF_CORRECT_ANS_WAS_FALSE'),
    'lang_num_correct_of_total' => Text::_('COM_YAQ_NUMCORRECTOFTOTAL'),
    'lang_your_score' => Text::_('COM_YAQ_JSQ_YOURSCORE'),
    'lang_points' => Text::_('COM_YAQ_POINTS'),
    'lang_score_as_percent' => Text::_('COM_YAQ_PERCENTOFCORRECT'),
);

$doc->addScriptOptions('quizData', $quizData);

$doc->addScriptOptions('questionData', $question_array);

?>


<div class="container p-2">

<div id="reviewquiz" class="card" data-reviewquiz-page="0">
    <div class="card-header">
        <h3>
            <?php echo $quiz->title; ?>
        </h3>
    </div>
    <div class="card-body">

        <?php echo $quiz->description; ?>

    </div>
    <div class="card-footer">
        <span id="pageCount"></span>
        <a class="btn btn-primary" id="reviewquiz-btn-start"><?php echo Text::_('COM_YAQ_START_QUIZ');?></a>
        <a class="btn btn-primary float-end hidden" id="reviewquiz-btn-next"><?php echo Text::_('COM_YAQ_NEXT');?></a>
        <a class="btn btn-success float-end hidden" id="reviewquiz-btn-finish"><?php echo Text::_('COM_YAQ_QUIZ_RESULTS');?></a>
    </div>
</div>


<div class="card d-none mb-2" id="reviewquiz-results">
    <h3 class="card-header"><?php echo Text::_('COM_YAQ_RESULTS');?></h3>
    <div class="card-body">
        <p><?php echo Text::_('COM_YAQ_JSQ_YOURSCORE');?><span id="reviewquiz-score"></span></p>
        <p id="reviewquiz-passfail-feedback"></p>
        <div id="reviewquiz-feedback-passed" class="bg-light text-success  p-2 rounded d-none"><i class="fas fa-clipboard-check"></i> <?php echo $gConfig->get('lang_pass');?></div>
        <div id="reviewquiz-feedback-failed" class="bg-light text-danger p-2 rounded d-none"><i class="fas fa-sad-cry"></i> <?php echo $gConfig->get('lang_fail');?></div>
    </div>
</div>


