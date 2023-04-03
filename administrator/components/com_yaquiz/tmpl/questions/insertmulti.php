<?php

defined ( '_JEXEC' ) or die;

//this tmpl contains a form to insert multiple questions at once
//questions must be separated by a new line
//each line must contain the info, in this format
// THE_QUESTION || THE_DETAILS || THE_TYPE || ans1 | ans2 | ans3 | ans4 || THE_CATEGORY_ID || THE_POINTVALUE


$exampleFile = JURI::root().'administrator/components/com_yaquiz/tmpl/questions/SampleQuestionSpreadsheet.xlsx';


$form = $this->form;



?>
<div class="card">
<h1 class="card-header">Multi-Question Insertion Wizard</h1>
<div class="card-body">
<p>Here, you can add many questions at once.</p>
<p>To do so, you must upload an Excel file with the questions in it.</p>
<p>The easiest way to do this is to download the example spreadsheet and edit as needed. It has examples of every question type.</p>
</div>
<div class="card-footer">
<a href="<?php echo $exampleFile; ?>" class="btn btn-success" download>Download Example Spreadsheet</a>
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
<input type="submit" value="Upload & Review" class="btn btn-primary">
</div></div>
</form>


