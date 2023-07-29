<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Questions;




//this the template to display 1 quiz info

defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use KevinsGuides\Component\Yaquiz\Administrator\Helper\YaquizHelper;
use Joomla\CMS\Factory;
$app = Factory::getApplication();
$wa = $app->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('yaquiz-admin-yaquizstyle', 'administrator/components/com_yaquiz/src/Style/com_yaquiz.min.css');
$wa->registerAndUseScript('yaquiz-utils', 'administrator/components/com_yaquiz/src/Scripts/utils.js');


$sqhelper = new YaquizHelper();

//get this form
$form = $this->form;
$filter_title = null;
$filter_categories = null;
$filter_limit = null;
//check if filters exist in POST
if (isset($_POST['filters'])) {
    //check filters
    if ($_POST['filters']['filter_title']) {
        $form->setValue('filter_title', null, $_POST['filters']['filter_title']);
        $filter_title = $_POST['filters']['filter_title'];
    }
    if (isset($_POST['filters']['filter_categories'])) {
        $form->setValue('filter_categories', null, $_POST['filters']['filter_categories']);
        $filter_categories = $_POST['filters']['filter_categories'];
        $app->setUserState('com_yaquiz.questions.filter_categories', $filter_categories);
    }
    if ($_POST['filters']['filter_limit']) {
        $form->setValue('filter_limit', null, $_POST['filters']['filter_limit']);
        $filter_limit = $_POST['filters']['filter_limit'];
    }
}

$filter_categories = $app->getUserState('com_yaquiz.questions.filter_categories', null);




$page = 0;
if (isset($_GET['page'])) {
    $page = $_GET['page'];
}

//get items
$model = $this->getModel('Questions');
$items = $model->getItems($filter_title, $filter_categories, $page, $filter_limit);
$pagecount = $model->getPageCount($filter_categories, $filter_title, $filter_limit);

//see if we need to show accordion
$showAccordion = false;
if ($filter_title || $filter_categories || $filter_limit) {
    $showAccordion = true;
}

?>
<div class="container">
<div class="card mb-2">
    <h1 class="card-header"><span class="icon-check"></span> <?php echo Text::_('COM_YAQUIZ_QUESTION_MGR');?></h1>
    <div class="card-body">
    <p><?php echo Text::_('COM_YAQUIZ_QUESTION_MGR_DESC1');?></p>
    <p><?php echo Text::_('COM_YAQUIZ_QUESTION_MGR_DESC2');?></p>
    </div>
</div>

<form id="adminForm" action="index.php?option=com_yaquiz&view=Questions" method="POST" class="mb-2">
    <div class="accordion" id="accordionFilters">
        <div class="accordion-item">
            <h2 class="accordion-header" id="hdgFilters">
            <button class="accordion-button <?php echo (($showAccordion) ? '' : 'collapsed'); ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters" aria-expanded="true" aria-controls="collapseFilters">
            <?php echo Text::_('COM_YAQUIZ_FILTERS');?>
            </button>
            </h2>
            <div id="collapseFilters" class="accordion-collapse collapse <?php echo ($showAccordion ? 'show' : '') ?>" aria-labelledby="hdgFilters" data-bs-parent="#accordionFilters">
                <div class="accordion-body">
                    <?php //set filter_categories of form to $filter_categories
                    $form->setValue('filter_categories', null, $filter_categories);
                    ?>
                    <?php echo $form->renderFieldset('filters'); ?>
                    <input name="task" type="hidden">
                    <input type="submit" value="<?php echo Text::_('COM_YAQUIZ_FILTER');?>" class="btn btn-primary">
                </div>
            </div>
        </div>
    </div>
</form>

<!-- begin items form for batch ops -->
<form method="POST">

<?php if ($items != null): ?>
    <?php foreach ($items as $item): ?>
        <?php $params = json_decode($item->params); ?>
        <div class="card questionpreview mb-2">

        <div class="card-header p-1 bg-light">
        <span class="icon-question questionicon bg-light"> </span>
            <span class="w-100 ps-3" id="qn<?php echo $item->id; ?>"><?php echo $item->question; ?></span>
            <input class="float-end form-check-input" type="checkbox" name="selectedQuestions[]" value="<?php echo $item->id;?>"></input>
        </div>

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
                if(strlen($details) > 100){
                    $details .= '...';
                }
                echo $details;
                if($details != ''){
                    echo '<br/>';
                }
                ?>
                
                <span class="badge bg-primary float-end">ID: <?php echo $item->id; ?></span>
                <span class="badge text-dark bg-light"><?php echo Text::_('COM_YAQUIZ_CATEGORY');?>:
                    <?php echo $sqhelper->getCategoryName($item->catid); ?>
                </span>
                <span class="badge text-dark bg-light"><?php echo Text::_('COM_YAQUIZ_TYPE');?>: 
                    <?php if($params->question_type == 'multiple_choice'){
                        echo Text::_('COM_YAQUIZ_QUESTION_TYPE_MULTIPLECHOICE');    
                    }
                    if($params->question_type == 'fill_blank'){
                        echo Text::_('COM_YAQUIZ_QUESTION_TYPE_FILLBLANK');  
                    }
                    if($params->question_type == 'true_false'){
                        echo Text::_('COM_YAQUIZ_QUESTION_TYPE_TRUEFALSE');  
                    }
                    if ($params->question_type === 'html_section'){
                        echo Text::_('COM_YAQUIZ_QUESTION_TYPE_HTML_SECTION');
                    }
                ?> </span>
                <span class="badge text-dark bg-light"><?php echo Text::_('COM_YAQUIZ_QUESTION_POINTS_LABEL');?>: <?php echo $params->points; ?></span>
            </div>
            <div class="card-footer p-1">
                <a class="btn-danger float-end btn btn-sm doublecheckdialog"
                    data-confirm="<?php echo Text::_('COM_YAQUIZ_DELETE_CONFIRM').' '.$item->question;?>"
                    href="index.php?option=com_yaquiz&task=questions.deleteQuestion&delete=<?php echo $item->id; ?>"><span
                        class="icon-delete"></span> <?php echo Text::_('COM_YAQUIZ_DELETE');?></a>
                <a 
                class="btn btn-info btn-sm"
                href="index.php?option=com_yaquiz&view=Question&layout=edit&qnid=<?php echo $item->id; ?>"><span
                        class="icon-edit"></span> <?php echo Text::_('COM_YAQUIZ_EDIT');?></a>
                       
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p><?php echo Text::_('COM_YAQUIZ_NOQUESTIONS');?></p>
<?php endif; ?>


<div class="row">
    <div class="d-none d-lg-block col-lg-7">

    </div>
    <div class="col-12 col-lg-5">
    <div class="card card-body">
<input type="hidden" name="quiz_id" value="<?php echo $item->id; ?>">
        <input type="hidden" name="task" value="Questions.executeBatchOps">
        <label for="batch_op"><?php echo Text::_('COM_YAQUIZ_BATCHOPS_WITHSELECTED');?></label>
        <select name="batch_op" class="form-select mb-1">
            <option value="0"><?php echo Text::_('COM_YAQUIZ_BATCHOPS_SELECTOP');?></option>
            <option value="remove"><?php echo Text::_('COM_YAQUIZ_DELETE_PERMANENTLY');?></option>
        </select>
        <input type="submit" class="btn btn-info btn-sm" value="<?php echo Text::_('COM_YAQUIZ_BATCHOPS_EXECUTE');?>">
</div>
    </div>
</div>

<?php if ($pagecount > 1): ?>
    <nav class="pagination__wrapper">
        
        <span class="float-end"><?php echo Text::sprintf('COM_YAQUIZ_PAGINATION', $page + 1, $pagecount);?></span>
        <div class="pagination pagination-toolbar">
            <ul class="pagination m-0">
                <?php if ($page > 0): ?>
                    <li class="page-item"><a class="page-link"
                            href="index.php?option=com_yaquiz&view=Questions&page=<?php echo $page - 1; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit;?>"><span
                                class="icon-angle-left" aria-hidden="true"></span></a></li>
                <?php endif; ?>
                <?php if ($pagecount <= 10 ) : ?>
                    <?php for ($i = 0; $i < $pagecount; $i++): ?>
                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>"><a class="page-link"
                                href="index.php?option=com_yaquiz&view=Questions&page=<?php echo $i; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit;?>"><?php echo $i + 1; ?></a></li>
                    <?php endfor; ?>
                <?php else: ?>
                    <?php if ($page < 5): ?>
                        <?php for ($i = 0; $i < 10; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>"><a class="page-link"
                                    href="index.php?option=com_yaquiz&view=Questions&page=<?php echo $i; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit;?>"><?php echo $i + 1; ?></a></li>
                        <?php endfor; ?>
                    <?php elseif ($page > $pagecount - 5): ?>
                        <?php for ($i = $pagecount - 10; $i < $pagecount; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>"><a class="page-link"
                                    href="index.php?option=com_yaquiz&view=Questions&page=<?php echo $i; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit;?>"><?php echo $i + 1; ?></a></li>
                        <?php endfor; ?>
                    <?php else: ?>
                        <?php for ($i = $page - 5; $i < $page + 5; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>"><a class="page-link"
                                    href="index.php?option=com_yaquiz&view=Questions&page=<?php echo $i; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit;?>"><?php echo $i + 1; ?></a></li>
                        <?php endfor; ?>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($page < $pagecount - 1): ?>
                    <li class="page-item"><a class="page-link"
                            href="index.php?option=com_yaquiz&view=Questions&page=<?php echo $page + 1; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit;?>"><span
                                class="icon-angle-right" aria-hidden="true"></span></a></li>
                <?php endif; ?>
                <?php if ($pagecount > 10): ?>
                    <li class="page-item"><a class="page-link"
                            href="index.php?option=com_yaquiz&view=Questions&page=<?php echo $pagecount - 1; ?>&catid=<?php echo $filter_categories; ?>&filter_title=<?php echo $filter_title; ?>&filter_limit=<?php echo $filter_limit;?>"><span class="icon-last"></span></a></li>
                <?php endif; ?>

            </ul>
        </div>
    </nav>
<?php endif; ?>



</div>



</form>

<br/>
<a href="https://kevinsguides.com/tips" class="btn btn-success btn-lg"><?php echo Text::_('COM_YAQUIZ_SUPPORT_THIS_PROJECT');?></a>