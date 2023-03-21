<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\SimpleQuiz;
use JFactory;
use JHtml;
use Joomla\CMS\Factory;
//this the template to display 1 quiz info

defined('_JEXEC') or die;

//get the quiz

$item = $this->item;


//get a listbox of all questions in the database
function getQuestionListBox(){
    $db = Factory::getContainer()->get('DatabaseDriver');
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('id', 'question')));
    $query->from($db->quoteName('#__simplequiz_questions'));
    $db->setQuery($query);
    $results = $db->loadObjectList();
    $options = array();
    //make multi select list


    $list = '';
    foreach($results as $result){
        $list .= '<option value="'.$result->id.'">'.$result->question.'</option>';
    }
    $list = '<select name="question_ids[]" multiple>'.$list.'</select>';
    return $list;
    
}

//get questions from model
$model = $this->getModel();
$questions = $model->getQuestionsInQuiz($item->id);


?>
<a class="btn btn-lg btn-primary" href="index.php?option=com_simplequiz&view=simplequiz&layout=edit&id=<?php echo $item->id; ?>">Edit Details</a>
<h1>Details: <?php echo $item->title; ?></h1>

<div>
    <p><?php echo $item->description; ?></p>
</div>

<form action="index.php?option=com_simplequiz&task=simplequiz.addQuestionsToQuiz" method="post">
    <input type="hidden" name="quiz_id" value="<?php echo $item->id; ?>">
    <!-- get the questions selectlist -->
    <?php echo getQuestionListBox(); ?>
    <?php echo JHtml::_('form.token'); ?>
    
    <button type="submit" class="btn btn-primary">Add Question(s)</button>
</form>

<h3>Questions On This Quiz...</h3>
<p>Note, removing items from the quiz does not delete the question itself.</p>
<?php foreach ($questions as $question): ?>
    <div class="card mb-2 card-body">
        <p><?php echo $question->question; ?></p>
        <a href="index.php?option=com_simplequiz&task=simplequiz.removeQuestionFromQuiz&quiz_id=<?php echo $item->id; ?>&question_id=<?php echo $question->id; ?>">Remove</a>
    </div>
<?php endforeach; ?>


