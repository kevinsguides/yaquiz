<?php
namespace KevinsGuides\Component\SimpleQuiz\Site\View\Quiz;
use KevinsGuides\Component\SimpleQuiz\Site\Helper\QuestionBuilderHelper;
use JHtml;
use Joomla\CMS\Factory;




defined('_JEXEC') or die;



//get this quiz from the model
$quiz = $this->get('Item');

//get the questions (a list of objects)
$questions = $this->get('Questions');

$questionBuilder = new QuestionBuilderHelper();

$model = $this->getModel();


//if the quiz is null, show error
if ($quiz == null):
?>
<div class="card">
    <div class="card-body">
        <h1>Quiz not found</h1>
        <p>Sorry, the quiz you are looking for could not be found.</p>
    </div>
</div>
<?php
else:
?>

<h1>Welcome to a quiz.</h1>
<h2><?php echo $quiz->title; ?></h2>

<form action="index.php?option=com_simplequiz&task=quiz.submitquiz" method="post">
    <input type="hidden" name="quiz_id" value="<?php echo $quiz->id; ?>" />


    <button type="submit" class="btn btn-primary">Submit Quiz</button>


<?php foreach($questions as $question): ?>
    <div class="card">
        <div class="card-body">
            <?php echo $questionBuilder->buildQuestion($question); ?>
        </div>
    </div>
<?php endforeach; ?>

<?php echo JHtml::_('form.token'); ?>

</form>

<?php
endif;
?>
