<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

//echo everything after this
$pointtext = 'questions right.';
if ($quizParams->quiz_use_points === '1') {
    $pointtext = 'points.';
}


$html .= '<div class="card">
            <h3 class="card-header">'.Text::_('COM_YAQ_QUIZ_RESULTS').'</h3>
            <div class="card-body">
            <p>'.Text::sprintf('COM_YAQ_NUMCORRECTOFTOTAL', $results->correct, $results->total).'</p>
            <p>'.Text::sprintf('COM_YAQ_PERCENTOFCORRECT', $resultPercent).'</p>';

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

            //see if we're showing general stats
            if($gen_stats){
                $html .= '<h4>'.Text::_('COM_YAQ_QSTATS').'</h4>';
                $html .= '<p>'.Text::sprintf('COM_YAQ_ATTEMPTCOUNT', $gen_stats->submissions).'</p>';
                $html .= '<p>'.Text::sprintf('COM_YAQ_AVGSCORE', $gen_stats->total_average_score).'</p>';
                $percentWhoPassed = $gen_stats->total_times_passed / $gen_stats->submissions * 100;
                //to 1 decimal
                $percentWhoPassed = round($percentWhoPassed, 1);
                $html .= '<p>'.Text::sprintf('COM_YAQ_PERCENTPASS', $percentWhoPassed).'</p>';
            }


$html .= '</div></div><br/>';
?>
    


