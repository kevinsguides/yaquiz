<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
 * 
 * 
 * This is the layout for the quiz timer controlled using JS when quizzes are timed
 * By default it's a floating timer that appears in the top right corner of the screen
*/
defined ('_JEXEC') or die('restricted access');
use Joomla\CMS\Language\Text;

?>


<div id="yaquizTimer">
    <i class="fas fa-clock me-2"></i>
    <?php echo Text::_('COM_YAQ_TIME_REMAIN');?> <span id="yaqTimerTime"><?php echo $seconds_left; ?></span>
</div>
