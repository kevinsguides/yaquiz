<?php

namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\SimpleQuizzes;

use Joomla\CMS\HTML\HTMLHelper;Use Joomla\CMS\Log\Log;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use JRoute;
use JText;
use KevinsGuides\Component\SimpleQuiz\Administrator\Model\SimpleQuizModel;



$quizModel = new SimpleQuizModel();

$model = $this->getModel();
//get the items from model

//get this form
$this->form = $this->get('Form');

if(isset($_POST['filters'])){
  $filter_title = $_POST['filters']['filter_title'];
  $filter_categories = $_POST['filters']['filter_categories'];
  $this->items = $model->getItems($filter_title, $filter_categories);
  //if $filter_title or $filter_categories are set, then we need to set the form values
  if($filter_title || $filter_categories){
    $this->form->setValue('filter_title', null, $filter_title);
    $this->form->setValue('filter_categories', null, $filter_categories);
  }
}
else{
  $this->items = $this->get('Items');
}

//does user have core.delete access
$user = \JFactory::getUser();
$canDelete = $user->authorise('core.delete', 'com_simplequiz');








?>
<form id="adminForm" action="index.php?option=com_simplequiz&view=SimpleQuizzes" method="post">
<div class="accordion" id="accordionFilters">
  <div class="accordion-item">
    <h2 class="accordion-header" id="hdgFilters">
      <button class="accordion-button <?php echo (isset($_POST['filters']) ? '' : 'collapsed') ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
        Filters...
      </button>
    </h2>
    <div id="collapseFilters" class="accordion-collapse collapse <?php echo (isset($_POST['filters']) ? 'show' : '') ?>" aria-labelledby="hdgFilters" data-bs-parent="#accordionFilters">
      <div class="accordion-body">

        <!-- render simplequizzes_filterset fieldset -->
        <?php echo $this->form->renderFieldset('filters'); ?>

    <input name="task" type="hidden">
    
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="index.php?option=com_simplequiz&view=SimpleQuizzes" class="btn btn-dark">Reset</a>
 </div>
    </div>
  </div>
  </div>
    <br/>

<div>
    <?php if($this->items): ?>
        <div class="row">
            <div class="col-6">
                <h3>Quiz Title: </h3>
    </div>
    <div class="col-3">
        <h3>Category</h3>
    </div>
    <div class="col-3">
        <h3>Actions: </h3>
        </div>
    </div>
   
            <?php foreach($this->items as $item): ?>
                <hr/>
                <div class="row mb-2">
                    <div class="col-12 col-md-6">
                    <a href="index.php?option=com_simplequiz&view=simplequiz&id=<?php echo $item->id ?>"><?php echo $item->title; ?> <span class="icon-edit"></span></a>
                    <p><?php echo ($item->published == 1 ? 'Published' : 'Unpublished') ?></p>
                  </div>
                    <div class="col-12 col-md-3">
                    <span><?php echo ($quizModel->getCategoryName($item->catid)? $quizModel->getCategoryName($item->catid) : 'Uncategorized'); ?></span>
                  
                  </div>
                    <div class="col-12 col-md-3">
                    <a class="btn btn-info btn-sm w-100 mb-1 text-start" href="index.php?option=com_simplequiz&view=simplequiz&layout=edit&id=<?php echo $item->id ?>"><span class="icon-options"></span> Quiz Settings</a>
                    <a class="btn btn-success btn-sm w-100 mb-1 text-start" href="index.php?option=com_simplequiz&view=simplequiz&id=<?php echo $item->id ?>"><span class="icon-checkbox"></span> Select Questions</a>
                    <?php if($canDelete): ?>
                      <a class="btn btn-danger btn-sm mb-1 float-end" href="index.php?option=com_simplequiz&view=simplequiz&task=SimpleQuiz.remove&quizid=<?php echo $item->id ?>"><span class="icon-trash"></span> Delete</a>
                    <?php endif; ?>
                </div>
                </div>
            <?php endforeach; ?>

    <?php else: ?>
        <p>No quizzes found</p>
    <?php endif; ?>

</div>


</form>