<?php
defined('_JEXEC') or die;

//echo everything after this
$pointtext = 'questions right.';
if ($quizParams->quiz_use_points === '1') {
    $pointtext = 'points.';
}


$html .= '<div class="card">
            <h3 class="card-header">Quiz Results</h3>
            <div class="card-body">
            <p>You got ' . $results->correct . ' out of ' . $results->total . ' ' . $pointtext . '</p>
            <p>That is a ' . $resultPercent . '%</p>';

            //progress bar display
            $passColor = ($results->passfail === 'pass') ? 'bg-success' : 'bg-danger';
            $html .= '<div class="progress" role="progressbar" aria-label="Success example" aria-valuenow="' . $resultPercent . '" aria-valuemin="0" aria-valuemax="100">';
            $html .= '<div class="progress-bar ' . $passColor . '" style="width: ' . $resultPercent . '%">' . $resultPercent . '%</div>  </div>';
            $html .= '<br/>';

            if ($results->passfail === 'pass') {
                $html .= '<p class="p-3 bg-light text-success">' . $this->globalParams->get('lang_pass') . '</p>';
            } else {
                $html .= '<p class="p-3 bg-light text-danger">' . $this->globalParams->get('lang_fail') . '</p>';
            }

$html .= '</div></div><br/>';
?>
    


