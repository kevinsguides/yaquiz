<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


use Joomla\CMS\Factory;

defined ( '_JEXEC' ) or die;






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


<h1>Review Your Submission...</h1>
<p>Carefully review the table below for any glaring issues. If everything looks good, click the button at the bottom to insert all the questions at once.</p>
<p>If there are minor issues, you can always fix them after inserting.</p>
<?php echo $questions_preview; ?>
<h3>Final Details...</h3>
<p>I will insert all these questions into YAQuiz <strong>Category: <?php echo $catname; ?></strong></p>
<p>These questions are coming from the file you uploaded to: <?php echo $filename; ?></p>

<form method="POST">
<input type="hidden" name="task" value="Questions.insertMultiSaveAll" />
<input type="hidden" name="filename" value="<?php echo $filename; ?>" />
<input type="hidden" name="catid" value="<?php echo $catid; ?>" />
<a href="index.php?option=com_yaquiz&task=Questions.insertMultiCancel" class="btn btn-danger">Cancel</a>
<input type="submit" class="btn btn-success" value="Insert All Questions" />
</form>



