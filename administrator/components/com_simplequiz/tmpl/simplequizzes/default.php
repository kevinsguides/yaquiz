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







?>

<div class="accordion" id="accordionFilters">
  <div class="accordion-item">
    <h2 class="accordion-header" id="hdgFilters">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
        Filters...
      </button>
    </h2>
    <div id="collapseFilters" class="accordion-collapse collapse hide" aria-labelledby="hdgFilters" data-bs-parent="#accordionFilters">
      <div class="accordion-body">
      <form id="adminForm" action="index.php?option=com_simplequiz&view=SimpleQuizzes" method="post">
        <!-- render simplequizzes_filterset fieldset -->
        <?php echo $this->form->renderFieldset('filters'); ?>

    <input name="task" type="hidden">
    
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="index.php?option=com_simplequiz&view=SimpleQuizzes" class="btn btn-dark">Reset</a>
    </form></div>
    </div>
  </div>
  </div>
    <br/>

<div>
    <?php if($this->items): ?>
        <div class="row">
            <div class="col-6">
                <h2>Quiz Title: </h2>
    </div>
    <div class="col-2">
        <h2>Category</h2>
    </div>
    <div class="col-4">
        <h2>Actions: </h2>
        </div>
    </div>
   
            <?php foreach($this->items as $item): ?>
                <hr/>
                <div class="row mb-2">
                    <div class="col-6">
                    <a href="index.php?option=com_simplequiz&view=simplequiz&id=<?php echo $item->id ?>"><h3><?php echo $item->title; ?></h3></a></div>
                    <div class="col-2">
                    <h3><?php echo ($quizModel->getCategoryName($item->catid)? $quizModel->getCategoryName($item->catid) : 'Uncategorized'); ?></h3>
                    </div>
                    <div class="col-4">
                    <a class="btn btn-secondary" href="index.php?option=com_simplequiz&view=simplequiz&layout=edit&id=<?php echo $item->id ?>">Quiz Settings</a>
                    <a class="btn btn-primary" href="index.php?option=com_simplequiz&view=simplequiz&id=<?php echo $item->id ?>">Select Questions</a>
                </div>
                </div>
            <?php endforeach; ?>

    <?php else: ?>
        <p>No quizzes found</p>
    <?php endif; ?>

</div>