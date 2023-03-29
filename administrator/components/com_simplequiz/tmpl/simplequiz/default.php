<?php
/**
 * @package     KevinsGuides.SimpleQuiz
 * 
 * This template is for the default quiz view
 * It displays details about the quiz and allows you to add questions to it
 */


namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\SimpleQuiz;
use JFactory;
use JHtml;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\Registry\Registry;
use JUri;
//this the template to display 1 quiz info

defined('_JEXEC') or die;

//get the quiz

$item = $this->item;
$titleFilter = null;
$categoryfilter = null;

//get this form
$this->form = $this->get('Form');

//if $_GET has a filter, use it
if(isset($_POST['filters']['filter_title'])){
    $titleFilter = $_POST['filters']['filter_title'];
}

//if $_GET has categoryfilter
if(isset($_POST['filters']['filter_categories'])){
    $categoryfilter = $_POST['filters']['filter_categories'];
    Log::add('category filter is '.$categoryfilter, Log::INFO, 'com_simplequiz');
}

//set form data
$this->form->setValue('filter_title', null, $titleFilter);
$this->form->setValue('filter_categories', null, $categoryfilter);


//get the question categories
$questionsModel = new \KevinsGuides\Component\SimpleQuiz\Administrator\Model\QuestionsModel();


//get a listbox of all questions in the database
function getQuestionListBox($titleFilter = null, $categoryfilter = null){
    $db = Factory::getContainer()->get('DatabaseDriver');
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('id', 'question', 'details')));
    $query->from($db->quoteName('#__simplequiz_questions'));
    if($titleFilter){
        Log::add('attempt filter by title '.$titleFilter, Log::INFO, 'com_simplequiz');
        $query->where($db->quoteName('question') . ' LIKE ' . $db->quote('%'.$titleFilter.'%'));
    }
    if($categoryfilter){
        Log::add('attempt filter by category '.$categoryfilter, Log::INFO, 'com_simplequiz');
        $query->where($db->quoteName('catid') . ' = ' . $db->quote($categoryfilter));
    }
    $db->setQuery($query);
    $results = $db->loadObjectList();
    $options = array();
    //make multi select list


    $list = '';
    foreach($results as $result){
        $truncated_details = substr($result->details, 0, 100);
        //strip tags
        $truncated_details = strip_tags($truncated_details);
        $truncated_details = ' ('.$truncated_details.'...)';
        $list .= '<option value="'.$result->id.'">[ID: '.$result->id.'] '.$result->question.'   ' . $truncated_details . '</option>';
    }
    $list = '<select name="question_ids[]" multiple class="form-select" size="8">'.$list.'</select>';
    return $list;
    
}

//get questions from model
$model = $this->getModel();
$questions = $model->getQuestionsInQuiz($item->id);

$quizlink = JUri::root().'index.php?option=com_simplequiz&view=quiz&id='.$item->id;
?>

<div class="container">
<div class="card">
<h1 class="card-header">Details: <?php echo $item->title; ?></h1>



<div class="card-body">
    <p><?php echo $item->description; ?></p>
</div>
<div class="card-footer">
    <p>Quiz ID: <?php echo $item->id; ?></p>
    <p>Raw Quiz Link: <a href="<?php echo $quizlink; ?>" target="_blank"><?php echo $quizlink; ?></a>
</p>
    </pre>
</div>
</div>

<div class="card mt-4">
    <h2 class="card-header">Question Insertion</h2>
    <div class="card-body">
<!-- filter by category -->
<form id="adminForm" action="index.php?option=com_simplequiz&view=simplequiz&id=<?php echo $item->id; ?>" method="POST">
<input type="hidden" name="task">
<input type="hidden" name="option" value="com_simplequiz">
    <input type="hidden" name="view" value="simplequiz">
    <input type="hidden" name="id" value="<?php echo $item->id; ?>">
    <!-- render filters fieldset -->
    <?php echo $this->form->renderFieldset('filters'); ?>
    <div class="control-group">
        <div class="control-label">
            <label for="filterSubmit">Submit:</label>
</div>
<div class="controls">
    <button id="filterSubmit" type="submit" class="btn btn-primary btn-sm"><span class="icon-search"></span> Filter Available Questions</button>
</div>
</div>
</form>
<br/>
<h4>Available Questions</h4>
<span style="font-size: 0.8rem;">Hold CTRL/Command to add multiple at once</span>
<form action="index.php?option=com_simplequiz&task=simplequiz.addQuestionsToQuiz" method="post">
    <input type="hidden" name="quiz_id" value="<?php echo $item->id; ?>">
    <!-- get the questions selectlist -->
    <?php echo getQuestionListBox($titleFilter, $categoryfilter); ?>
    <?php echo JHtml::_('form.token'); ?>
    <button type="submit" class="btn btn-primary">Add Question(s)</button>
</form>


</div>
</div>






<div class="card bg-light mt-4 shadow-sm">
    <h2 class="card-header bg-primary text-white">Questions In Quiz</h2>
    <div class="card-body">
<p>Note, removing items from the quiz does not delete the question itself. Remember, if you edit a question the changes apple across all quizzes containing that particular question and not just this quiz.</p>

<?php foreach ($questions as $question): ?>

    <div class="card mb-2">
        <div class="card-header bg-dark text-white">
        
    <h4 class="text-white" id="qn<?php echo $question->id; ?>"><?php echo $question->question; ?></h4>
    </div>
        <div class="card-body"><a class="float-end" href="index.php?option=com_simplequiz&view=Question&layout=edit&qnid=<?php echo $question->id; ?>"><span class="icon-edit"></span></a>
        <?php 
        //fix image paths in question->details if they are relative
        $question->details = str_replace('src="images', 'src="'.JUri::root().'images', $question->details);
        ?>  
        <?php echo $question->details; ?>
        <p>Order: <?php echo $question->ordering; ?></p>
        
        <a href="index.php?option=com_simplequiz&task=SimpleQuiz.orderUp&quiz_id=<?php echo $item->id; ?>&qnid=<?php echo $question->id; ?>" class="btn btn-dark btn-sm">Order Up</a>  
        <a href="index.php?option=com_simplequiz&task=SimpleQuiz.orderDown&quiz_id=<?php echo $item->id; ?>&qnid=<?php echo $question->id; ?>" class="btn btn-dark btn-sm">Order Down</a>
        </div>
        <div class="card-footer">
        <a class="btn btn-danger btn-sm" title="Remove Question From Quiz" href="index.php?option=com_simplequiz&task=simplequiz.removeQuestionFromQuiz&quiz_id=<?php echo $item->id; ?>&question_id=<?php echo $question->id; ?>"><span class="icon-delete"></span> Remove</a>
    <span class="float-end"> Type: 

    <?php
    $question_type = json_decode($question->params)->question_type;
    if($question_type === 'multiple_choice'){
        echo 'Multiple Choice';
    }
    if($question_type === 'true_false'){
        echo 'True/False';
    }
    if ($question_type === 'fill_blank'){
        echo 'Blank / Short Answer';
    }
    ?>
    </span>
</div>
    </div>
<?php endforeach; ?>

</div>
</div>

</div>

