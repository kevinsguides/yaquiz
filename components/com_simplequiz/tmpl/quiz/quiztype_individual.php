<?php
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use KevinsGuides\Component\SimpleQuiz\Site\Helper\QuestionBuilderHelper;
use KevinsGuides\Component\SimpleQuiz\Site\Model\QuizModel;

defined ( '_JEXEC' ) or die;

$currPage = $this->currPage;

//if $this-> item is already set
if(isset($this->item)){
    $quiz = $this->item;
}
else{
    $quiz = $this->get('Item');
}

$model = new QuizModel();
//the total number of questions in quiz from the model...
$totalQuestions = $model->getTotalQuestions($quiz->id);
$quiz_params = $model->getQuizParams($quiz->id);

//if current page is greater than total questions, redirect to last page
if($currPage > $totalQuestions){
    $app = Factory::getApplication();
    $app->redirect('index.php?option=com_simplequiz&view=quiz&id='.$quiz->id.'&page='.$totalQuestions);
}

$qbHelper = new QuestionBuilderHelper();

JHtml::_('behavior.keepalive');

?>

<form action="index.php?option=com_simplequiz&task=quiz.loadNextPage" method="POST">
    <input type="hidden" name="quiz_id" value="<?php echo $quiz->id; ?>" />
    <input type="hidden" name="page" value="<?php echo $currPage; ?>" />
    
<div class="card">
<div class="card-header">
    <h1><?php echo $quiz->title; ?></h1>
    <span>Page: <?php echo $currPage + 1; ?> of <?php echo $totalQuestions + 1; ?></span>
</div>
<div class="card-body">

<?php if ($currPage == 0){
    echo $quiz->description;
} ?>

<?php if ($currPage > 0 && $currPage <= $totalQuestions):
    $question = $model->getQuestionFromQuizOrdering($quiz->id, $currPage);

    $previous_answer = $qbHelper->checkAnswerInSession($quiz->id, $question->id);
    if($previous_answer != null){
        Log::add('Previous answer found and sent to question : '.$previous_answer, Log::INFO, 'com_simplequiz');
        $question->defaultanswer = $previous_answer;
    }
    echo $qbHelper->buildQuestion($question, $quiz_params);
    ?>
    <input type="hidden" name="question_id" value="<?php echo $question->id; ?>" />
<?php endif;?>

</div>
<div class="card-footer">
<?php if ($currPage == 0):?>
    <a href="index.php?option=com_simplequiz&view=quiz&id=<?php echo $quiz->id; ?>&page=<?php echo $currPage + 1; ?>" class="btn btn-primary">Start Quiz</a>
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

</div>
</div>
<?php echo JHtml::_('form.token'); ?>
</form>


<a href="index.php?option=com_simplequiz&task=quiz.resetSession">Reset Session</a>
