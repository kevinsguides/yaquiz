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

//get this form
$this->form = $this->get('Form');

//get the filters
$filters = $input->get('filters', '', 'array');
if($filters) {
    $filter_title = $filters['filter_title'];
    $filter_categories = $filters['filter_categories'];
    $filter_limit = $filters['filter_limit'];
} else {
    $filter_title = $input->get('filter_title', '','string');
    $filter_categories = $input->get('catid', '0','int');
    $filter_limit = $input->get('filter_limit', '10','int');
}



Log::add('attempt set filter tiltle ' . $filter_title, Log::INFO, 'com_yaquiz');
$this->form->setValue('filter_title', null, $filter_title);
$this->form->setValue('filter_categories', null, $filter_categories);
$this->form->setValue('filter_limit', null, $filter_limit);
$this->items = $model->getItems($filter_title, $filter_categories, $page, $filter_limit);

//does user have core.delete access
$user = \JFactory::getUser();
$canDelete = $user->authorise('core.delete', 'com_yaquiz');


$gConfig = \JComponentHelper::getParams('com_yaquiz');

Log::add('attempt load page ' . $page, Log::INFO, 'com_yaquiz');

//if we have filters, expand the filters accordion
$filterset = $input->get('filters', '', 'array');
if($filterset) {
    $filterset = 'show';
} 
else if($input->get('filter_title', '','string') != '' || $input->get('catid', '0','int') != 0|| $input->get('filter_limit', '10','int') != 10) {
    $filterset = 'show';
}
else {
    $filterset = '';
}


?>

<div class="container">
<div class="card">
  <h1 class="card-header">Quizzes</h1>
  <div class="card-body">
<form id="adminForm" action="index.php?option=com_yaquiz&view=Yaquizzes" method="post">
<div class="accordion" id="accordionFilters">
  <div class="accordion-item">
    <h2 class="accordion-header" id="hdgFilters">
      <button class="accordion-button <?php echo (isset($_POST['filters']) ? '' : 'collapsed') ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
        Filters...
      </button>
    </h2>
    <div id="collapseFilters" class="accordion-collapse collapse <?php echo $filterset; ?>" aria-labelledby="hdgFilters" data-bs-parent="#accordionFilters">
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
$pagecount = $model->getTotalPages($filter_limit, $filter_title, $filter_categories);



?>
</div>
<div class="card-footer">
<?php if ($pagecount > 1): ?>
    <nav class="pagination__wrapper">
    <span class="float-end">Page <?php echo $page + 1; ?> of <?php echo $pagecount; ?></span>
        <div class="pagination pagination-toolbar">
            <ul class="pagination m-0">

                <?php if ($page > 0): ?>
                    <li class="page-item"><a class="page-link"
                            href="index.php?option=com_yaquiz&view=Yaquizzes&page=<?php echo $page - 1; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit; ?>"><span
                                class="icon-angle-left" aria-hidden="true"></span></a>
                    </li>
                <?php endif; ?>
                <?php if($pagecount <= 10) :?>
                <?php for ($i = 0; $i < $pagecount; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                      <a class="page-link" href="index.php?option=com_yaquiz&view=Yaquizzes&page=<?php echo $i; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit; ?>"><?php echo $i + 1; ?></a>
                    </li>
                
                <?php endfor; ?>
                <?php else : ?>
                <?php if($page < 5) : ?>
                <?php for ($i = 0; $i < 10; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                      <a class="page-link" href="index.php?option=com_yaquiz&view=Yaquizzes&page=<?php echo $i; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit; ?>"><?php echo $i + 1; ?></a>
                    </li>
                <?php endfor; ?>
                <?php elseif($page >= 5 && $page < $pagecount - 5) : ?>
                <?php for ($i = $page - 5; $i < $page + 5; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                      <a class="page-link" href="index.php?option=com_yaquiz&view=Yaquizzes&page=<?php echo $i; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit; ?>"><?php echo $i + 1; ?></a>
                    </li>
                <?php endfor; ?>
                <?php else : ?>
                <?php for ($i = $pagecount - 10; $i < $pagecount; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                      <a class="page-link" href="index.php?option=com_yaquiz&view=Yaquizzes&page=<?php echo $i; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit; ?>"><?php echo $i + 1; ?></a>
                    </li>
                <?php endfor; ?>
                <?php endif;?>
                <?php endif; ?>

                <?php if ($page < $pagecount - 1): ?>
                    <li class="page-item"><a class="page-link"
                            href="index.php?option=com_yaquiz&view=Yaquizzes&page=<?php echo $page + 1; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit; ?>"><span
                                class="icon-angle-right" aria-hidden="true"></span></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    <?php if ($pagecount > 10): ?>
      <p>You have a lot of pages, consider filtering your results.</p>
    <?php endif; ?>
                </div>
                </div>
<?php endif; ?>

                </div>