<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Yaquiz;



defined ( '_JEXEC' ) or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

//get form
$form = $this->form;

//tell Joomla we're using the YaquizController
HtmlHelper::_('behavior.formvalidator');
HtmlHelper::_('behavior.keepalive');

if(!isset($_GET['id'])){
    $_GET['id'] = 0;
}

$app = Factory::getApplication();
$wa = $app->getDocument()->getWebAssetManager();


?>

<div class="container">
<form action="index.php?option=com_yaquiz&task=Yaquiz.save&id=<?php echo $_GET['id']; ?>" method="post" name="adminForm" id="item-form" aria-label="New Quiz" class="form-validate">
<input name="task" type="hidden" value="Yaquiz.save">
<?php echo HtmlHelper::_('form.token'); ?>
<div class="row">

    <div class="col-12 col-xxl-8">
    <div class="card mb-3">
    <h1 class="card-header"><?php echo Text::_('COM_YAQUIZ_QUIZDETAILSEDIT');?></h1>
    <div class="card-body">
        <?php echo $form->renderFieldset('yaquiz'); ?></div></div>
    </div>
    <div class="col-12 col-xxl-4">
    <div class="card mb-3">
    <h2 class="card-header"><?php echo Text::_('COM_YAQUIZ_PUBLISH_DISPLAY'); ?></h2>
        <div class="card-body"><?php echo $form->renderFieldset('publishing'); ?></div></div>
    <div class="card mb-3">
    <h2 class="card-header"><?php echo Text::_('COM_YAQUIZ_QUIZEDIT_GRADEFEEDBACK'); ?></h2>
        <div class="card-body"><?php echo $form->renderFieldset('grading'); ?></div>
    </div>
    </div>
    <div class="col-12">
    <div class="card mb-3">
        <h2 class="card-header"><?php echo Text::_('COM_YAQUIZ_QUIZEDIT_USERRECORDS'); ?></h2>
        <div class="card-body">
        <?php echo $form->renderFieldset('usersandrecords');?>
        </div>
    </div>
    </div>
    </div>
</form>




</div>


<style>
    .container{
        max-width: 1800px;
    }
</style>