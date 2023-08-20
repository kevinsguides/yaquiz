<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;


$use_certificates = $quizParams->get('use_certificates', 0);




$html .= '<div class="card">
            <h3 class="card-header">'.Text::_('COM_YAQ_QUIZ_RESULTS').'</h3>
            <div class="card-body">';
            if ($quizParams->get('quiz_use_points', 0) == 1) {
                $html .= '<p>'.Text::sprintf('COM_YAQ_NUM_CORRECT_OF_POINTS', $results->correct, $results->total).'</p>';
            
            }
            else {
            $html .= '<p>'.Text::sprintf('COM_YAQ_NUMCORRECTOFTOTAL', $results->correct, $results->total).'</p>';
            }

            $html .= '<p>'.Text::sprintf('COM_YAQ_PERCENTOFCORRECT', $resultPercent).'</p>';

            //progress bar display
            $passColor = ($results->passfail === 'pass') ? 'bg-success' : 'bg-danger';
            $html .= '<div class="progress" role="progressbar" aria-label="grade" aria-valuenow="' . $resultPercent . '" aria-valuemin="0" aria-valuemax="100">';
            $html .= '<div class="progress-bar ' . $passColor . '" style="width: ' . $resultPercent . '%">' . $resultPercent . '%</div>  </div>';
            $html .= '<br/>';

            $passMessage = $this->globalParams->get('lang_pass');
            if($passMessage == ""){
                $passMessage = Text::_('COM_YAQUIZ_DEFAULT_PASSMESSAGE');
            }

            $failMessage = $this->globalParams->get('lang_fail');
            if($failMessage == ""){
                $failMessage = Text::_('COM_YAQUIZ_DEFAULT_FAILMESSAGE');
            }

            if ($results->passfail === 'pass') {
                $html .= '<p class="p-3 bg-light text-success">' . $passMessage . '</p>';
            } else {
                $html .= '<p class="p-3 bg-light text-danger">' . $failMessage . '</p>';
            }

            //see if we're showing general stats
            if($gen_stats){
                $html .= '<h4>'.Text::_('COM_YAQ_QSTATS').'</h4>';
                $html .= '<p>'.Text::sprintf('COM_YAQ_ATTEMPTCOUNT', $gen_stats->submissions).'</p>';
                $html .= '<p>'.Text::sprintf('COM_YAQ_AVGSCORE', $gen_stats->total_average_score).'</p>';
                if ($gen_stats->submissions > 0){
                    $percentWhoPassed = $gen_stats->total_times_passed / $gen_stats->submissions * 100;
                    //to 1 decimal
                    $percentWhoPassed = round($percentWhoPassed, 1);
                    $html .= '<p>'.Text::sprintf('COM_YAQ_PERCENTPASS', $percentWhoPassed).'</p>';
                }

            }

            //see if using certificates
            if($use_certificates == '1'){
                $user = $app->getIdentity();
                //user must be logged in
                if($user->id > 0){
                    //see if they passed
                    if($results->passfail === 'pass'){
                        
                        if($result_id != 0){
                           //link to ceretificate
                            $html .= '<hr/><h3>'.Text::_('COM_YAQ_YOUR_CERTIFICATE').'</h3>';
                            $html .= '<p>'.Text::sprintf('COM_YAQ_EARNED_CERTIFICATE_MESSAGE', $resultPercent).'</p>';
                            $html .= '<p><a href="index.php?option=com_yaquiz&task=User.generateQuizCert&format=raw&quiz_id='.$quiz_id.'&result_id='.$result_id.'" class="btn btn-success btn-lg"><i class="fas fa-certificate"></i>'.Text::_('COM_YAQ_VIEW_CERTIFICATE').'</a></p>';
                        }
                    }
                }
            }


$html .= '</div></div><br/>';
?>
    


