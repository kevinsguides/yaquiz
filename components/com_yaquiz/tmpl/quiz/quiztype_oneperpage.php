<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

/**
 * This is the 1 question per page type
 * renders individual pages for each question with help of questionbuilder class
 * a better name for this would have been multi_page but that's what we're stuck with
*/


defined ( '_JEXEC' ) or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use KevinsGuides\Component\Yaquiz\Site\Helper\QuestionBuilderHelper;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use KevinsGuides\Component\Yaquiz\Site\Helper\ThemeHelper;
use Joomla\CMS\Router\Route;

$currPage = $this->currPage;

//if $this-> item is already set
if(isset($this->item)){
    $quiz = $this->item;
}
else{
    $quiz = $this->get('Item');
}

$app = Factory::getApplication();
$wam = $app->getDocument()->getWebAssetManager();
//get config from component
$gConfig = $app->getParams('com_yaquiz');
if ($gConfig->get('get_mathjax') === '1') {
    $wam->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js', [], ['defer' => true]);
}
if ($gConfig->get('get_mathjax') === '2') {
    Log::add('Loading local mathjax', Log::INFO, 'com_yaquiz');
    $wam->registerAndUseScript('com_yaquiz.mathjaxlocal', 'components/com_yaquiz/js/mathjax/es5/tex-svg.js', [], ['defer' => true]);
}


//config for attempts left display
$showAttemptsLeft = false;
$showAttemptsLeft = $gConfig->get('show_attempts_left', '1');
if($showAttemptsLeft == '1'){
    $showAttemptsLeft = true;
}

$wam->registerAndUseStyle('com_yaquiz.quiz', ThemeHelper::findFile('style.css'));



$model = new QuizModel();
//the total number of questions in quiz from the model...
$totalQuestions = $model->getTotalQuestions($quiz->id);
$quiz_params = $model->getQuizParams($quiz->id);
$quiz_params->quiz_id = $quiz->id;

$attempts_left = $model->quizAttemptsLeft($quiz->id);

//if current page is greater than total questions, redirect to last page
if($currPage > $totalQuestions){
    $app->redirect('index.php?option=com_yaquiz&view=quiz&id='.$quiz->id.'&page='.$totalQuestions);
}



$qbHelper = new QuestionBuilderHelper(); //used in includes

HTMLHelper::_('behavior.keepalive');

?>

<form action="<?php echo Route::_('index.php?option=com_yaquiz&task=quiz.loadNextPage'); ?>" method="POST">
    <input type="hidden" name="quiz_id" value="<?php echo $quiz->id; ?>" />
    <input type="hidden" name="page" value="<?php echo $currPage; ?>" />


    <?php if ($currPage == 0){
        //record hits
        if ($gConfig->get('record_hits') === '1') {
            $model->countAsHit($quiz->id);
        }
    } 
    ?>

    <?php
        include(ThemeHelper::findFile('oneperpage_pagecontainer.php'));
    ?>




<?php echo HTMLHelper::_('form.token'); ?>
</form>