<?php

defined ( '_JEXEC' ) or die;

//this tmpl contains a form to insert multiple questions at once
//questions must be separated by a new line
//each line must contain the info, in this format
// THE_QUESTION || THE_DETAILS || THE_TYPE || ans1 | ans2 | ans3 | ans4 || THE_CATEGORY_ID || THE_POINTVALUE

//see if we can find ../../vendor/autoload.php

$autoload = JPATH_ROOT . '/administrator/components/com_yaquiz/vendor/autoload.php';


$readfile = JPATH_ROOT . '/insertmultiexample.xlsx';

require_once $autoload;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;


$form = $this->form;



?>

<h1>Multi-Question Insertion Wizard</h1>
<p>Here, you can add many questions at once.</p>
<p>To do so, you must upload an Excel file with the questions in it.</p>
<p>Strict formatting must be used!</p>
<p>See the example below:</p>

<form enctype="multipart/form-data"  method="POST">

<?php echo $form->renderFieldset('insertmulti'); ?>
<input type="hidden" name="task" value="Questions.startInsertMulti">
<input type="submit" value="Upload & Review" class="btn btn-primary">
</form>


