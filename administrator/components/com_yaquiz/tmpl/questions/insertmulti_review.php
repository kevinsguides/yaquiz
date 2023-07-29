<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


use Joomla\CMS\Factory;

defined ( '_JEXEC' ) or die;
use Joomla\CMS\Language\Text;






//get the preview info from session and display
$app = Factory::getApplication();
$session = $app->getSession();

$questions_preview = $session->get('questions_preview');

//get the catid from session
$catid = $session->get('insertmulti_catid');

//get name of that category
$yaquizHelper = new \KevinsGuides\Component\Yaquiz\Administrator\Helper\YaquizHelper();
$catname = $yaquizHelper->getCategoryName($catid);
$filename = $session->get('insertmulti_filename');



?>


<h1><?php echo Text::_('COM_YAQUIZ_MULTISUBMIT_REVIEW');?></h1>
<?php echo Text::_('COM_YAQUIZ_MULTISUBMIT_REVIEW_DESC');?>
<?php echo $questions_preview; ?>
<h3><?php echo Text::_('COM_YAQUIZ_FINAL_DETAILS');?></h3>
<p><?php echo Text::sprintf('COM_YAQUIZ_MULTISUBMIT_QUESTIONS_WILL_GO_INTO_CAT', $catname);?></p>
<?php echo Text::sprintf('COM_YAQUIZ_MULTISUBMIT_QUESTIONS_LOADED_FROM', $filename);?>

<form method="POST">
<input type="hidden" name="task" value="Questions.insertMultiSaveAll" />
<input type="hidden" name="filename" value="<?php echo $filename; ?>" />
<input type="hidden" name="catid" value="<?php echo $catid; ?>" />
<a href="index.php?option=com_yaquiz&task=Questions.insertMultiCancel" class="btn btn-danger"><?php echo Text::_('JCANCEL');?></a>
<input type="submit" class="btn btn-success" value="<?php echo Text::_('COM_YAQUIZ_INSERT_ALL_QUESTIONS');?>" />
</form>



