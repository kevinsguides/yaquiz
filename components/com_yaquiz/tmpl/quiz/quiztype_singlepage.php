<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
 * 
 * This file loads the quiz intro and questions all on one page. This is the default single-page quiz layout.
 * 
 * It should not contain any layout elements, but instead render the layout elements from the theme subdirectory.
 * If you want to create a new style, you are suggested to override the "default" folder files 
 * in the subdirectory this file is in
 * 
*/

namespace KevinsGuides\Component\Yaquiz\Site\View\Quiz;

defined('_JEXEC') or die;

use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use KevinsGuides\Component\Yaquiz\Site\Helper\QuestionBuilderHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
use Joomla\CMS\Language\Text;
use KevinsGuides\Component\Yaquiz\Site\Helper\ThemeHelper;


$app = Factory::getApplication();
$wam = $app->getDocument()->getWebAssetManager();


//get config from component
$globalParams = $app->getParams('com_yaquiz');
if ($globalParams->get('get_mathjax') === '1') {
    $wam->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js', [], ['defer' => true]);
}
if ($globalParams->get('get_mathjax') === '2') {
    Log::add('Loading local mathjax', Log::INFO, 'com_yaquiz');
    $wam->registerAndUseScript('com_yaquiz.mathjaxlocal', 'components/com_yaquiz/js/mathjax/es5/tex-svg.js', [], ['defer' => true]);
}


//config for attempts left display
$showAttemptsLeft = false;
$showAttemptsLeft = $globalParams->get('show_attempts_left', '1');
if($showAttemptsLeft == '1'){
    $showAttemptsLeft = true;
}




//determine theme to use

$stylefile = ThemeHelper::findFile('style.css');
$wam->registerAndUseStyle('com_yaquiz.quiz', $stylefile);


//theme layout files
$error_page = ThemeHelper::findFile('error.php');
$layout_template_intro = ThemeHelper::findFile('singlepage_intro.php');
$layout_submit_btn = ThemeHelper::findFile('submit.php');


if ($app->input->get('status') == 'retry') {
    //get their old answers from the session
    $session = Factory::getSession();
    $oldanswers = $session->get('sq_retryanswers');
} else {
    $oldanswers = null;
}


HtmlHelper::_('behavior.keepalive');


//if $this-> item is already set
if (isset($this->item)) {
    $quiz = $this->item;
} else {
    $quiz = $this->get('Item');
}

$model = new QuizModel();
$attempts_left = $model->quizAttemptsLeft($quiz->id);
$quiz_params = $model->getQuizParams($quiz->id);


//get the questions (a list of objects)
$questions = $model->getQuestions($quiz->id);
$questionBuilder = new QuestionBuilderHelper();

$quizparams = $model->getQuizParams($quiz->id);
$quizparams->quiz_id = $quiz->id;

//record hits
if ($globalParams->get('record_hits') === '1') {
    $model->countAsHit($quiz->id);
}


//menu itemid
if($itemid = $app->getMenu()->getActive()->id){
    $itemid = '&Itemid=' . $itemid;
}
else{
    $itemid = '';
}

//check if using a timer
$uses_timer = $quizparams->get('quiz_use_timer', 0);
if($uses_timer == 1){
    $uses_timer = true;
    $wam->registerAndUseScript('com_yaquiz.timer', 'components/com_yaquiz/js/timer.js', [], ['defer' => true]);
}
else{
    $uses_timer = false;
}

//if the quiz is null, show error
if ($quiz == null){

    $error = new \stdClass();
    $error->type = 'error';
    $error->message = Text::_('COM_YAQUIZ_QUIZ_NOT_FOUND');
    $error->title = Text::_('COM_YAQUIZ_QUIZ_NOT_FOUND');
    include($error_page);

} else {

     include($layout_template_intro);
?>
<form id="yaQuizForm" action="<?php echo Uri::root(); ?>index.php?option=com_yaquiz&task=quiz.submitquiz<?php echo $itemid; ?>" method="post">
                <input type="hidden" name="id" value="<?php echo $quiz->id; ?>" />


<?php


        if ($quizparams->get('quiz_displaymode', 'default') == 'default'){
            include(ThemeHelper::findFile('singlepage_questionscontainer.php'));
        }

        if ($uses_timer){
            $user = $app->getIdentity();
            $seconds_left = $model->getTimeRemainingAsSeconds($user->id, $quiz->id);
            include(ThemeHelper::findFile('quiztimer.php'));
        }
    }

?>

</form>