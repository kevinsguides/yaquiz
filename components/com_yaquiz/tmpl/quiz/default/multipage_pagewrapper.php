<?php
defined ( '_JEXEC' ) or die ();
?>

<div class="card">
<div class="card-header">
    <h1><?php echo $quiz->title; ?></h1>
</div>
<div class="p-1">

<?php if ($currPage == 0){
    echo $quiz->description;
} ?>

<?php if ($currPage > 0 && $currPage <= $totalQuestions):
    $question = $model->getQuestionFromQuizOrdering($quiz->id, $currPage);

    $previous_answer = $qbHelper->checkAnswerInSession($quiz->id, $question->id);
    if($previous_answer != null){
        $question->defaultanswer = $previous_answer;
    }
    $question->question_number = $currPage;
    echo $qbHelper->buildQuestion($question, $quiz_params);
    ?>
    <input type="hidden" name="question_id" value="<?php echo $question->id; ?>" />
<?php endif;?>

</div>
<div class="card-footer">
<?php if ($currPage == 0):?>
    <a href="<?php echo JURI::root(); ?>index.php?option=com_yaquiz&view=quiz&id=<?php echo $quiz->id; ?>&page=<?php echo $currPage + 1; ?>" class="btn btn-primary">Start Quiz</a>
<?php endif;?>
<?php if ($currPage > 0 && $currPage <= $totalQuestions):?>
    <button type="submit" name="nextpage" value="-1" class="btn btn-primary">Previous</button>
<?php endif;?>

<?php if($currPage > 0 && $currPage < $totalQuestions):?>

    <button type="submit" name="nextpage" value="1" class="btn btn-primary">Next</button>
   
<?php endif;?>

<?php if ($currPage == $totalQuestions):?>
    <button type="submit" name="nextpage" value="results" class="btn btn-primary">Finish</button>
<?php endif;?>
<span class="float-end">Page: <?php echo $currPage + 1; ?> of <?php echo $totalQuestions + 1; ?></span>
</div>
</div>