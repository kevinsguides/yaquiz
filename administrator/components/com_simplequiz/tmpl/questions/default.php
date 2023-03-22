<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\Questions;
use Joomla\CMS\Log\Log;
use KevinsGuides\Component\SimpleQuiz\Administrator\Helper\SimpleQuizHelper;
//this the template to display 1 quiz info

defined('_JEXEC') or die;



$sqhelper = new SimpleQuizHelper();

//get this form
$form = $this->form;
$filter_title = null;
$filter_categories = null;
//check filters
if($_POST['filters']['filter_title']){
    $form->setValue('filter_title', null, $_POST['filters']['filter_title']);
    $filter_title = $_POST['filters']['filter_title'];
}
if($_POST['filters']['filter_categories']){
    $form->setValue('filter_categories', null, $_POST['filters']['filter_categories']);
    $filter_categories = $_POST['filters']['filter_categories'];
}



//get items
$model = $this->getModel('Questions');
$items = $model->getItems($filter_title, $filter_categories);

?>

<div class="card mb-2">
<h1 class="card-header">Questions Manager</h1>
<div class="card-body">
<form id="adminForm" action="index.php?option=com_simplequiz&view=Questions" method="post">
    <!-- load the form "filters" fieldset -->
    <?php echo $form->renderFieldset('filters'); ?>
    
    <input name="task" type="hidden">
    <input type="submit" value="Filter" class="btn btn-primary float-end">
</form>
</div></div>
<?php if($items != null): ?>
    <?php foreach($items as $item): ?>
        <div class="card mb-2">
            <h3 class="card-header bg-light"><?php echo $item->question; ?></h3>
            <div class="card-body">
            <?php  
            $details = $item->details;
            //strip out the html tags
            //replace img elements with text IMG
            $details = preg_replace('/<img[^>]+\>/i', 'IMG', $details);
            //replace a elements with text LINK
            $details = preg_replace('/<a[^>]+\>/i', 'LINK', $details);
            $details = strip_tags($details);
            //truncate the string
            $details = substr($details, 0, 100);
            //add ellipsis
            $details .= '...';
            echo $details;
            
            ?>
            <p>Category: <?php echo $sqhelper->getCategoryName($item->catid); ?></p>
            </div>
            <div class="card-footer">
            <a class="btn-danger float-end btn btn-sm" href="index.php?option=com_simplequiz&task=questions.deleteQuestion&delete=<?php echo $item->id; ?>"><span class="icon-delete"></span> Delete</a>
            <a href="index.php?option=com_simplequiz&view=Question&layout=edit&qnid=<?php echo $item->id; ?>"><span class="icon-edit"></span> Edit</a>
            </div>
            </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No questions found</p>
<?php endif; ?>