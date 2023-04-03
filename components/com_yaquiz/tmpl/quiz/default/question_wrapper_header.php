<?php

defined ( '_JEXEC' ) or die ();

//this is the code before each question is displayed
//it should contain the start of any container elements, the question itself, and the $question->details

//warning if item was not filled out
if (isset($question->defaultanswer) && $question->defaultanswer === 'missing') {
    $itemMissing .= '<i class="text-danger fas fa-exclamation-triangle me-2" title="You forgot to answer this..."></i>';
}

?>

<div class="card">
    <h3 class="card-header"><?php echo $itemMissing . $formatted_questionnum . $question->question;?></h3>
    <div class="card-body">
        <?php echo $question->details;?>





