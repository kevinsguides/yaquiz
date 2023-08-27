<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use KevinsGuides\Component\Yaquiz\Site\Helper\ThemeHelper;

?>

<div class="card yaq-page">
<div class="card-header">
    <h1><?php echo $quiz->title; ?></h1>
</div>
<div class="card-body p-2">

<?php if ($currPage == 0){
    echo $quiz->description;
    if ($showAttemptsLeft) {
        echo '<div class="badge bg-info text-white">';
        if($attempts_left == 0){
            echo Text::_('COM_YAQ_MAX_ATTEMPTS_REACHED');
        }
        elseif($attempts_left == 1){
            echo Text::_('COM_YAQ_1ATTEMPT_LEFT');
        }
        elseif($attempts_left > 1){
            echo Text::sprintf('COM_YAQ_ATTEMPTS_REMAINING', $attempts_left);
        }
        echo '</div>';
    }

    if($uses_timer && $seconds_left == 0){
       include ThemeHelper::findFile('oneperpage_timerinfo.php');
    }

    if($uses_timer && $seconds_left > 0){
        echo '<br/><div class="card card-body bg-warning p-1 text-white fs-5">';
        $seconds_left -= 15;
        $mins_left = floor($seconds_left / 60);
        $remainder_secs = $seconds_left % 60;
        echo Text::sprintf('COM_YAQ_TIMER_ALREADY_STARTED', $mins_left, $remainder_secs);
        echo '</div>';
    }

} ?>

<?php if ($currPage > 0 && $currPage <= $totalQuestions):
    $question = $model->getQuestionFromQuizOrdering($quiz->id, $currPage);

    $previous_answer = $qbHelper->checkAnswerInSession($quiz->id, $question->id);
    if($previous_answer != null){
        $question->defaultanswer = $previous_answer;
    }
    $question->question_number = $currPage;
    echo $qbHelper->buildQuestion($question, $quiz_params, $quiz->id);
    ?>
    
    <input type="hidden" name="question_id" value="<?php echo $question->id; ?>" />
<?php endif;?>

</div>
<div class="card-footer text-center">
<?php if ($currPage == 0):?>
    <a href="<?php echo Route::_('index.php?option=com_yaquiz&view=quiz&id='.$quiz->id.'&page='.$currPage +1);?>"
     class="btn btn-primary yaq-navbtn float-end"><?php echo Text::_('COM_YAQ_START_QUIZ'); ?></a>
<?php endif;?>

<?php if ($currPage == $totalQuestions):?>
    <button type="submit" name="nextpage" value="results" class="btn btn-primary yaq-navbtn float-end"><?php echo Text::_('COM_YAQ_FINISH');?></button>
<?php endif;?>

<?php if($currPage > 0 && $currPage < $totalQuestions):?>

<button type="submit" name="nextpage" value="1" class="btn btn-primary yaq-navbtn float-end"><?php echo Text::_('COM_YAQ_NEXT');?></button>

<?php endif;?>

<?php if ($currPage > 0 && $currPage <= $totalQuestions):?>
    <button type="submit" name="nextpage" value="-1" class="btn btn-primary yaq-navbtn float-start"><?php echo Text::_('COM_YAQ_PREV');?></button>
<?php endif;?>

<span>
    <?php echo Text::sprintf('COM_YAQ_PAGEOF', $currPage + 1, $totalQuestions + 1); ?>
</span>


</div>
</div>