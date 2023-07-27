<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


/**
 * @package     KevinsGuides.Yaquiz
 * 
 * This template is for the default quiz view
 * It displays details about the quiz and allows you to add questions to it
 */


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Yaquiz;
use JFactory;
use JHtml;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use JUri;
use Joomla\CMS\Language\Text;
use KevinsGuides\Component\Yaquiz\Administrator\Model\QuestionModel;
//this the template to display 1 quiz info

defined('_JEXEC') or die;


$app = Factory::getApplication();

//permissions
$user = $app->getIdentity();
$canEdit = $user->authorise('core.edit', 'com_yaquiz');

if(!$canEdit){
    $app->enqueueMessage("You do not have permission to edit quizzes.", "error");
    return;
}

//get the quiz

$item = $this->item;

$filters = new \stdClass();
$filters->filter_title = null;
$filters->filter_categories = null;

//get this form
$this->form = $this->get('Form');

//filter stuff

//see if a new filter was set
if(isset($_POST['filters']['filter_title'])){
    $filters->filter_title = $_POST['filters']['filter_title'];
    $app->setUserState('com_yaquiz.yaquiz.filter_title', $filters->filter_title);
}
elseif($app->getUserState('com_yaquiz.yaquiz.filter_title')){
    $filters->filter_title = $app->getUserState('com_yaquiz.yaquiz.filter_title');
}

if(isset($_POST['filters']['filter_categories'])){
    $filters->filter_categories = $_POST['filters']['filter_categories'];
    Log::add('category filter is '.$filters->filter_categories, Log::INFO, 'com_yaquiz');
    $app->setUserState('com_yaquiz.yaquiz.filter_categories', $filters->filter_categories);
}
elseif($app->getUserState('com_yaquiz.yaquiz.filter_categories')){
    $filters->filter_categories = $app->getUserState('com_yaquiz.yaquiz.filter_categories');
}

//view mode for questions can be full or compact
$viewModeFull = $app->getUserState('com_yaquiz.yaquiz.viewfull', true);


if(isset($_GET['setviewcompact'])){
    $app->setUserState('com_yaquiz.yaquiz.viewfull', false);
    $viewModeFull = false;
}
if(isset($_GET['setviewfull'])){
    $app->setUserState('com_yaquiz.yaquiz.viewfull', true);
    $viewModeFull = true;
}



//set form data
$this->form->setValue('filter_title', null, $filters->filter_title);
$this->form->setValue('filter_categories', null, $filters->filter_categories);


//get the question categories
$questionsModel = new \KevinsGuides\Component\Yaquiz\Administrator\Model\QuestionsModel();


//get a listbox of all questions in the database
function getQuestionListBox($titleFilter = null, $categoryfilter = null){

    $db = Factory::getContainer()->get('DatabaseDriver');
    $query = $db->getQuery(true);
    $query->select($db->quoteName(array('id', 'question', 'details')));
    $query->from($db->quoteName('#__com_yaquiz_questions'));
    if($titleFilter){
        Log::add('attempt filter by title '.$titleFilter, Log::INFO, 'com_yaquiz');
        $query->where($db->quoteName('question') . ' LIKE ' . $db->quote('%'.$titleFilter.'%'));
    }
    if($categoryfilter){
        Log::add('attempt filter by category '.$categoryfilter, Log::INFO, 'com_yaquiz');
        $query->where($db->quoteName('catid') . ' = ' . $db->quote($categoryfilter));
    }
    $query->order('id DESC');
    //limit 100
    $query->setLimit(100);
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
    //count number of results
    $count = count($results);
    if($count == 100){
        $list = '<div class="p-2 rounded bg-info text-white mb-2">'.Text::_('COM_YAQUIZ_100QNLIMIT').'</div>'.$list;
    }
    return $list;
    
}

//get questions from model
$model = $this->getModel();
$questions = $model->getQuestionsInQuiz($item->id);

$quizlink = JUri::root().'index.php?option=com_yaquiz&view=quiz&id='.$item->id;
?>

<div class="container">
<div class="card">
<h1 class="card-header bg-light"><?php echo Text::_('COM_YAQUIZ_DETAILS');?>: <?php echo $item->title; ?></h1>



<div class="card-body">
    <p><?php echo $item->description; ?></p>
</div>
<div class="card-footer">
    <p><?php echo Text::_('COM_YAQUIZ_QUIZ_ID');?>: <?php echo $item->id; ?></p>
    <p><?php echo Text::_('COM_YAQUIZ_RAWQUIZLINK');?>: <a href="<?php echo $quizlink; ?>" target="_blank"><?php echo $quizlink; ?></a>
</p>
    </pre>
</div>
</div>

<div class="card mt-4">
    <h2 class="card-header bg-light"><?php echo Text::_('COM_YAQUIZ_QN_INSERTION');?></h2>
    <div class="card-body">
        <p><?php echo Text::_('COM_YAQUIZ_QN_INSERTION_DESC');?></p>
<!-- filter by category -->
<form id="adminForm" action="index.php?option=com_yaquiz&view=yaquiz&id=<?php echo $item->id; ?>" method="POST">
<input type="hidden" name="task">
<input type="hidden" name="option" value="com_yaquiz">
    <input type="hidden" name="view" value="yaquiz">
    <input type="hidden" name="id" value="<?php echo $item->id; ?>">
    <!-- render filters fieldset -->
    <?php echo $this->form->renderFieldset('filters'); ?>
    <div class="control-group">
        <div class="control-label">
            <label for="filterSubmit">Filter Submit:</label>
</div>
<div class="controls">
    <button id="filterSubmit" type="submit" class="btn btn-primary btn-sm"><span class="icon-search"></span> <?php echo Text::_('COM_YAQUIZ_FILTER_AVAILABLE_QUESTIONS');?></button>
</div>
</div>
</form>
<br/>
<h4><?php echo Text::_('COM_YAQUIZ_AVAILABLE_QUESTIONS');?></h4>
<span style="font-size: 0.8rem;"><?php echo Text::_('COM_YAQUIZ_AVAILABLE_QUESTIONS_DESC');?></span>
<form action="index.php?option=com_yaquiz&task=yaquiz.addQuestionsToQuiz" method="post">
    <input type="hidden" name="quiz_id" value="<?php echo $item->id; ?>">
    <!-- get the questions selectlist -->
    <?php echo getQuestionListBox($filters->filter_title, $filters->filter_categories); ?>
    <?php echo JHtml::_('form.token'); ?>
    <button type="submit" class="btn btn-success mt-2"><?php echo Text::_('COM_YAQUIZ_ADD_QUESTIONS');?></button>
</form>


</div>
</div>





<form method="POST">
<div class="card bg-primary text-white mt-4 shadow-sm">
    <h2 class="card-header bg-primary text-white"><?php echo Text::_('COM_YAQUIZ_QNS_IN_QUIZ');?></h2>
    <div class="card-body">
<p><?php echo Text::_('COM_YAQUIZ_QNS_IN_QUIZ_NOTE');?></p>
<?php if (count($questions) == 0): ?>
    <p class="fs-2"><?php echo Text::_('COM_YAQUIZ_NOQUESTIONSYET');?></p>
    <?php endif; ?>
    <?php if ($viewModeFull) :?>
        <a href="index.php?option=com_yaquiz&view=yaquiz&id=<?php echo $item->id; ?>&setviewcompact=1" class="btn btn-info btn-sm"><span class="icon-eye"></span> <?php echo Text::_('COM_YAQUIZ_VIEWCOMPACT');?></a>
    <?php else : ?>
        <a href="index.php?option=com_yaquiz&view=yaquiz&id=<?php echo $item->id; ?>&setviewfull=1" class="btn btn-info btn-sm"><span class="icon-eye"></span> <?php echo Text::_('COM_YAQUIZ_VIEWFULL');?></a>
    <?php endif;  ?>
    <hr/>
<?php foreach ($questions as $question): ?>

    <div class="card mb-2 questionpreview text-dark">
        <div class="card-header bg-light">
        <span class="icon-question questionicon bg-light"> </span>
            <span class="w-100" id="qn<?php echo $question->id; ?>"><?php echo $question->question; ?></span>
            <input class="float-end form-check-input" type="checkbox" name="selectedQuestions[]" value="<?php echo $question->id;?>"></input>
        </div>

        <?php if ($viewModeFull) : ?>
        <div class="questiondetails bg-light p-1">
        
        <?php 
        //fix image paths in question->details if they are relative
        $question->details = str_replace('src="images', 'src="'.JUri::root().'images', $question->details);
        ?>  
        <div class="p-1 bg-white">
        <?php echo $question->details; ?>
        </div>
        
        
        
    </div><?php endif;?>
        <div class="card-footer bg-light">
        <a class="btn btn-danger btn-sm" title="<?php echo Text::_('COM_YAQUIZ_REMOVE_TITLE');?>" href="index.php?option=com_yaquiz&task=yaquiz.removeQuestionFromQuiz&quiz_id=<?php echo $item->id; ?>&question_id=<?php echo $question->id; ?>"><span class="icon-delete"></span> <?php echo Text::_('COM_YAQUIZ_REMOVE');?></a>
        
        <span class="badge bg-secondary"><?php echo Text::_('COM_YAQUIZ_ORDER');?>: <?php echo $question->ordering; ?></span>
        <span class="badge bg-secondary"> <?php echo Text::_('COM_YAQUIZ_TYPE');?>: 

    <?php
    $question_type = json_decode($question->params)->question_type;
    if($question_type === 'multiple_choice'){
        echo Text::_('COM_YAQUIZ_QUESTION_TYPE_MULTIPLECHOICE');
    }
    if($question_type === 'true_false'){
        echo Text::_('COM_YAQUIZ_QUESTION_TYPE_TRUEFALSE');
    }
    if ($question_type === 'fill_blank'){
        echo Text::_('COM_YAQUIZ_QUESTION_TYPE_FILLBLANK');
    }
    ?>
    </span>

    <span class="badge bg-secondary"><?php echo Text::_('COM_YAQUIZ_CATEGORY').': '.QuestionModel::getCategoryName($question->id); ?></span>



        <a href="index.php?option=com_yaquiz&task=Yaquiz.orderUp&quiz_id=<?php echo $item->id; ?>&qnid=<?php echo $question->id; ?>" class="btn btn-primary btn-sm me-1 float-end"><i class="far fa-caret-square-up"></i> <?php echo Text::_('COM_YAQUIZ_ORDERUP');?></a>  
        <a href="index.php?option=com_yaquiz&task=Yaquiz.orderDown&quiz_id=<?php echo $item->id; ?>&qnid=<?php echo $question->id; ?>" class="btn btn-primary btn-sm me-1 float-end"><i class="far fa-caret-square-down"></i> <?php echo Text::_('COM_YAQUIZ_ORDERDOWN');?></a>

        <a class="btn btn-primary btn-sm me-1 float-end" title="<?php echo Text::_('COM_YAQUIZ_QUESTION_EDITOR');?>" href="index.php?option=com_yaquiz&view=Question&layout=edit&qnid=<?php echo $question->id; ?>"><span class="icon-edit"></span></a>
    
        

</div>
    </div>
<?php endforeach; ?>

</div>
</div>
<br/>
<div class="card">
    <h2 class="card-header bg-danger text-white"><?php echo Text::_('COM_YAQUIZ_BATCHOPS');?></h2>
    <div class="card-body">
        <p><?php echo Text::_('COM_YAQUIZ_BATCHOPS_DESC');?></p>
        <input type="hidden" name="quiz_id" value="<?php echo $item->id; ?>">
        <input type="hidden" name="task" value="Yaquiz.executeBatchOps">
        <label for="batch_op"><?php echo Text::_('COM_YAQUIZ_BATCHOPS_WITHSELECTED');?></label>
        <select name="batch_op" class="form-select">
            <option value="0"><?php echo Text::_('COM_YAQUIZ_BATCHOPS_SELECTOP');?></option>
            <option value="remove"><?php echo Text::_('COM_YAQUIZ_BATCHOPS_REMOVESELECTED');?></option>
        </select>
        <input type="submit" class="btn btn-info btn-sm" value="<?php echo Text::_('COM_YAQUIZ_BATCHOPS_EXECUTE');?>">

    </div>
    <div class="card-footer">
            <span class="w-100 d-block"><?php echo Text::_('COM_YAQUIZ_MISCOPS');?></span>
            <a href="index.php?option=com_yaquiz&task=Yaquiz.removeAllQuestionsFromQuiz&quiz_id=<?php echo $item->id; ?>" class="btn btn-danger btn-sm deleteAllQuestionsBtn"><span class="icon-delete"></span> <?php echo Text::_('COM_YAQUIZ_REMOVEALLQNS');?></a>
            <a
            href="index.php?option=com_yaquiz&task=Yaquiz.resetAllStatsAndRecords&quiz_id=<?php echo $item->id; ?>"
            title="<?php echo Text::_('COM_YAQUIZ_RESET_ALL_STATS_AND_RECORDS_DESC');?>"
            class="btn btn-warning btn-sm"><span class="icon-bars"></span> <?php echo Text::_('COM_YAQUIZ_RESET_ALL_STATS_AND_RECORDS');?></a>

</div>
</div>


</form>
</div>



<script>
const deleteQuizBtns = document.querySelectorAll('.deleteAllQuestionsBtn');
//add click listeners

deleteQuizBtns.forEach((btn) => {

    btn.addEventListener('click', (e) => {
        e.preventDefault();
        //show confirm
        const confirm = window.confirm('<?php echo Text::_('COM_YAQUIZ_REMOVEALLQNS_CONFIRM'); ?>');
        if (confirm) {
            //go to href from btn
            let goto = btn.getAttribute('href');
            window.location.href = goto;
            
        }

        
    });

});


</script>


<style>
.questionpreview .questiondetails{
    max-height: 300px;
    overflow-y: auto;
}
.questionicon{
    padding: 0.5rem;
    border-radius: 50%;
    position: absolute;
    left: -10px;
    top: 0;
}

.questionpreview .card-header{
    padding-left: 1.5rem;
}
</style>


<br/>
<a href="https://kevinsguides.com/tips" class="btn btn-success btn-lg"><?php echo Text::_('COM_YAQUIZ_SUPPORT_THIS_PROJECT');?></a>