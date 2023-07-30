<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Language\Text;
//this is the introductory card for the quiz which displays the details and title
//it appears above the questions on a single page quiz
?>


<div class="card yaq-intro">
    <h2 class="card-header">
        <?php echo $quiz->title; ?>
    </h2>
    <?php if($quiz->description): ?>
    <div class="card-body">
        <?php echo $quiz->description; ?>
    </div>
    <?php endif; ?>
    <?php if ($showAttemptsLeft): ?>
    <div class="card-footer">
        <?php echo '<div class="badge bg-info text-white">';
    if($attempts_left == 0){
        echo Text::_('COM_YAQ_MAX_ATTEMPTS_REACHED');
    }
    elseif($attempts_left == 1){
        echo Text::_('COM_YAQ_1ATTEMPT_LEFT');
    }
    elseif($attempts_left > 1){
        echo Text::sprintf('COM_YAQ_ATTEMPTS_REMAINING', $attempts_left);
    }
    echo '</div>';?>
    </div>
    <?php endif; ?>
</div>

<br />