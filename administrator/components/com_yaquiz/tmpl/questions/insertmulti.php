<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/



defined ( '_JEXEC' ) or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

//this tmpl contains a form to insert multiple questions at once
//questions must be separated by a new line
//each line must contain the info, in this format
// THE_QUESTION || THE_DETAILS || THE_TYPE || ans1 | ans2 | ans3 | ans4 || THE_CATEGORY_ID || THE_POINTVALUE


$exampleFile = URI::root().'administrator/components/com_yaquiz/tmpl/questions/SampleQuestionSpreadsheet.xlsx';


$form = $this->form;



?>
<div class="card">
<h1 class="card-header"><?php echo Text::_('COM_YAQUIZ_INSERTMULTI_TITLE');?></h1>
<div class="card-body">
<?php echo Text::_('COM_YAQUIZ_INSERTMULTI_DESC');?>
</div>
<div class="card-footer">
<a href="<?php echo $exampleFile; ?>" class="btn btn-success" download><?php echo Text::_('COM_YAQUIZ_INSERTMULTI_EXAMPLE');?></a>
</div>
</div>
<br/>

<form enctype="multipart/form-data"  method="POST">
<div class="card">
    <div class="card-body">
<?php echo $form->renderFieldset('insertmulti'); ?>
<input type="hidden" name="task" value="Questions.startInsertMulti">
</div>
<div class="card-footer">
<input type="submit" value="<?php echo Text::_('COM_YAQUIZ_UPLOAD_REVIEW');?>" class="btn btn-primary">
</div></div>
</form>


