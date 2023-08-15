<?php

/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
 * 
 * 
 * This layout includes info about the timer displayed on the beforebegin timer start page thing
*/

defined ('_JEXEC') or die();
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

?>



<div class="card card-body">

<div class="d-flex">
<i class="fas fa-clock text-primary float-end me-2" style="font-size: 64px; "></i>
<span class="fs-2 align-middle mt-auto mb-auto"><?php echo Text::_('COM_YAQ_THIS_IS_TIMED'); ?></span>
</div>


        <hr/>
        <p><?php echo Text::sprintf('COM_YAQ_TIME_LIMIT', $quiz_params->quiz_timer_limit); ?>
</p>



    <a href="index.php?option=com_yaquiz&task=quiz.startTimedQuiz&id=<?php echo $quiz->id; ?>" class="btn btn-primary"><?php echo Text::_('COM_YAQ_START_QUIZ'); ?></a>

</div>

