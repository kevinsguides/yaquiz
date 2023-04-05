<?php

namespace KevinsGuides\Component\Yaquiz\Administrator\View\Yaquizzes;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;Use Joomla\CMS\Log\Log;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use JRoute;
use JText;
use KevinsGuides\Component\Yaquiz\Administrator\Model\YaquizModel;



$quizModel = new YaquizModel();

$model = $this->getModel();
//get the items from model

$app = Factory::getApplication();
$input = $app->input;
$page = $input->get('page', 0);
$limit = $input->get('limit', 10);

//get this form
$this->form = $this->get('Form');

if(isset($_POST['filters'])){
  $filter_title = $_POST['filters']['filter_title'];
  $filter_categories = $_POST['filters']['filter_categories'];
  //if $filter_title or $filter_categories are set, then we need to set the form values

}
else{
  $filter_title = $input->get('filter_title', null);
  $filter_categories = $input->get('filter_categories', null);
}

$this->form->setValue('filter_title', null, $filter_title);
$this->form->setValue('filter_categories', null, $filter_categories);

$this->items = $model->getItems($filter_title, $filter_categories, $page, $limit);

//does user have core.delete access
$user = \JFactory::getUser();
$canDelete = $user->authorise('core.delete', 'com_yaquiz');


$gConfig = \JComponentHelper::getParams('com_yaquiz');

Log::add('attempt load page ' . $page, Log::INFO, 'com_yaquiz');

?>
<form id="adminForm" action="index.php?option=com_yaquiz&view=Yaquizzes" method="post">
<div class="accordion" id="accordionFilters">
  <div class="accordion-item">
    <h2 class="accordion-header" id="hdgFilters">
      <button class="accordion-button <?php echo (isset($_POST['filters']) ? '' : 'collapsed') ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
        Filters...
      </button>
    </h2>
    <div id="collapseFilters" class="accordion-collapse collapse <?php echo (isset($_POST['filters']) ? 'show' : '') ?>" aria-labelledby="hdgFilters" data-bs-parent="#accordionFilters">
      <div class="accordion-body">
        <!-- render yaquizzes_filterset fieldset -->
        <?php echo $this->form->renderFieldset('filters'); ?>

    <input name="task" type="hidden">
    
    <button type="submit" class="btn btn-primary">Filter</button>
    <a href="index.php?option=com_yaquiz&view=Yaquizzes" class="btn btn-dark">Reset</a>
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
                    <a href="index.php?option=com_yaquiz&view=yaquiz&id=<?php echo $item->id ?>"><?php echo $item->title; ?> (View Details/Questions)</a>
                    <br/>
                    <?php echo ($item->published == 1 ? '<span class="badge bg-success text-white"><i class="fas fa-check-circle"></i> Published</span>' : '<span class="badge bg-dark text-white"><i class="fas fa-times-circle"></i> Unpublished</span>') ?>
                    <span class="badge bg-info text-white">ID: <?php echo $item->id ?></span>
                    <?php echo ($gConfig->get('record_hits')==='1' ? '<span class="badge bg-primary text-white">Hits: '. $item->hits .'</span>': '');?>
                    <?php echo ($gConfig->get('record_submissions')==='1' ? '<span class="badge bg-primary text-white">Submits: '. $item->submissions .'</span>': '');?>
                    <?php echo ($item->checked_out == 0 ? '<span class="badge bg-success text-white">Checked In</span>' : '<span class="badge bg-warning text-white">Checked Out</span>') ?>

                  </div>
                    <div class="col-12 col-md-3">
                    <span><?php echo ($quizModel->getCategoryName($item->catid)? $quizModel->getCategoryName($item->catid) : 'Uncategorized'); ?></span>
                  
                  </div>
                    <div class="col-12 col-md-3">
                    <a class="btn btn-info btn-sm w-100 mb-1 text-start" href="index.php?option=com_yaquiz&view=yaquiz&layout=edit&id=<?php echo $item->id ?>"><span class="icon-options"></span> Quiz Settings</a>
                    <a class="btn btn-success btn-sm w-100 mb-1 text-start" href="index.php?option=com_yaquiz&view=yaquiz&id=<?php echo $item->id ?>"><span class="icon-checkbox"></span> Select Questions</a>
                    <?php if($canDelete): ?>
                      <a class="btn btn-danger btn-sm mb-1 float-end" href="index.php?option=com_yaquiz&view=yaquiz&task=Yaquiz.remove&quizid=<?php echo $item->id ?>"><span class="icon-trash"></span> Delete</a>
                    <?php endif; ?>
                </div>
                </div>
            <?php endforeach; ?>

    <?php else: ?>
        <p>No quizzes found</p>
    <?php endif; ?>

</div>


</form>

<?php
//custom bootstrap pagination...
$pagecount = $model->getTotalPages($limit, $filter_title, $filter_categories);


?>


<?php if ($pagecount > 1): ?>
    <nav class="pagination__wrapper">
        <div class="pagination pagination-toolbar">
            <ul class="pagination ">

                <?php if ($page > 0): ?>
                    <li class="page-item"><a class="page-link"
                            href="index.php?option=com_yaquiz&view=Yaquizzes&page=<?php echo $page - 1; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>"><span
                                class="icon-angle-left" aria-hidden="true"></span></a>
                    </li>
                <?php endif; ?>

                <?php for ($i = 0; $i < $pagecount; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                      <a class="page-link" href="index.php?option=com_yaquiz&view=Yaquizzes&page=<?php echo $i; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>"><?php echo $i + 1; ?></a>
                    </li>
                
                            <?php endfor; ?>
                <?php if ($page < $pagecount - 1): ?>
                    <li class="page-item"><a class="page-link"
                            href="index.php?option=com_yaquiz&view=Yaquizzes&page=<?php echo $page + 1; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>"><span
                                class="icon-angle-right" aria-hidden="true"></span></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
<?php endif; ?>