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

<form action="index.php?option=com_simplequiz&view=simplequiz&layout=edit&id=<?php echo $_GET['id']; ?>" method="post" name="adminForm" id="item-form" aria-label="New Quiz" class="form-validate">
<?php echo $form->renderFieldset('simplequiz'); ?>
<input name="task" type="hidden">
<?php echo JHtml::_('form.token'); ?>
</form>
