<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

/*
* This layout file is displayed after each question in the quiz.
* It should close the container surrounding the question.
*
* The following variables are visible
* $questionType - the type of question being displayed
* $quiz_params - all quiz params -> quiz_use_points, quiz_displaymode, max_attempts, etc..
* $question_params - all question params -> points
* $question - the question object, eg. $question->question, $question->details
*/


defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Language\Text;


?>


</div>

<?php   if($quiz_params->quiz_use_points == 1 && $questionType != 'html_section') :?>
<div class="card-footer">
    <?php
  
        if($question_params->points > 1){
            echo Text::sprintf('COM_YAQ_POINTSWORTH', $question_params->points);
        }
        else{
            echo Text::_('COM_YAQ_POINTWORTH');
        }
    ?>

</div>
<?php endif; ?>
</div>
