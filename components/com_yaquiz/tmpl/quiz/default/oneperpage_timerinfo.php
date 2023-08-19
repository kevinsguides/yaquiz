<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
 * 
 * This layout is displayed on the first page of the quiz if the quiz is timed.
 * 
 * Visible vars: time_limit_mins (int) - the time limit in minutes
 * 
 */
defined ('_JEXEC') or die();
use Joomla\CMS\Language\Text;

 ?>


<div class="card card-body mt-2">

<div class="d-flex">
<i class="fas fa-clock text-primary float-end me-2" style="font-size: 64px; "></i>
<span class="fs-2 align-middle mt-auto mb-auto"><?php echo Text::_('COM_YAQ_THIS_IS_TIMED'); ?></span>
</div>


        <hr/>
        <p><?php echo Text::sprintf('COM_YAQ_TIME_LIMIT', $quiz_params->get('quiz_timer_limit', 10)); ?>
</p>

</div>