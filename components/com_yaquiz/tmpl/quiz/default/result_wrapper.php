<?php
//wraps a single question's result area
defined ( '_JEXEC' ) or die;


//check points
$quizParams = $this->getQuizParams($quiz_id);
$pointsFeedback = '';
if ($quizParams->quiz_use_points === '1') {
    if ($iscorrect) {
        $pointsFeedback = $question->params->points . ' / ' . $question->params->points . ' points';
    } else {
        $pointsFeedback = '0 / ' . $question->params->points . ' points';
    }
}

$feedback = '';

$feedback .= '<p><strong>Your answer:</strong> ' . $useranswer . '</p>';

if ($question->feedback_right != '' || $question->feedback_wrong != '') {
    if ($iscorrect) {
        $feedback .= $question->feedback_right;
    } else {
        $feedback .= $question->feedback_wrong;
    }
    $feedback .= '<br/>';
}

if ($quizParams->quiz_feedback_showcorrect === '1') {
    $feedback .= $question->correct_answer;
}

if ($iscorrect) {
    $icon = '<i class="fas fa-check-circle text-success"></i>';
} else {
    $icon = '<i class="fas fa-times-circle text-danger"></i>';
}

//numbering
if ($questionnum != 0) {
    $questionnum = $questionnum . ') ';
} else {
    $questionnum = '';
}


$html = '
<div class="card">
    <h2 class="card-header"><span class="float-end">'. $icon .'</span>'.$questionnum.$question->question.'</h2>
    <div class="card-body">
        '.$question->details.$feedback.'
</div>
<div class="card-footer">
    <span class="float-end">'.$pointsFeedback.'</span>
</div>
</div>
<br/>
';

?>


