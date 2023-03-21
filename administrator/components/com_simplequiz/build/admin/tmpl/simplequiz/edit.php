<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\SimpleQuiz;
use JHtml;

defined ( '_JEXEC' ) or die;

//get form
$form = $this->form;

//tell Joomla we're using the SimpleQuizController
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');



?>
<h1>Quiz Details Editor</h1>
<!-- load the fieldset -->
<form action="index.php?option=com_simplequiz&task=simplequiz.edit" method="post" id="sq-quiz">
<?php echo $form->renderFieldset('simplequiz'); ?>
<?php echo JHtml::_('form.token'); ?>
<button type="submit" class="btn btn-success">Save</button>
</form>
