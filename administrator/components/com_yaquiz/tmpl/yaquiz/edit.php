<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Yaquiz;
use JHtml;

defined ( '_JEXEC' ) or die;

//get form
$form = $this->form;

//tell Joomla we're using the YaquizController
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

if(!isset($_GET['id'])){
    $_GET['id'] = 0;
}

?>
<h1>Quiz Details Editor</h1>

<form action="index.php?option=com_yaquiz&task=Yaquiz.save&id=<?php echo $_GET['id']; ?>" method="post" name="adminForm" id="item-form" aria-label="New Quiz" class="form-validate">
<?php echo $form->renderFieldset('yaquiz'); ?>
<input name="task" type="hidden" value="Yaquiz.save">
<?php echo JHtml::_('form.token'); ?>
</form>
