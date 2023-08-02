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
<h1><?php echo Text::_('COM_YAQUIZ_QUIZDETAILSEDIT');?></h1>

<form action="index.php?option=com_yaquiz&task=Yaquiz.save&id=<?php echo $_GET['id']; ?>" method="post" name="adminForm" id="item-form" aria-label="New Quiz" class="form-validate">
<?php echo $form->renderFieldset('yaquiz'); ?>
<input name="task" type="hidden" value="Yaquiz.save">
<?php echo HtmlHelper::_('form.token'); ?>
</form>
