<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Language\Text;

if (isset($question->defaultanswer) && $question->defaultanswer === 'missing') {
    $itemMissing .= '<i class="text-danger fas fa-exclamation-triangle me-2" title="'.Text::_('COM_YAQ_FORGOTANS').'"></i>';
}

//This layout is inserted before every question on the single-page test type, you could leave it blank or open/close cards/containers here

?>


<div class="card mb-3">
    <span class="card-header"><span class="fs-3"><?php echo $itemMissing . $formatted_questionnum . $question->question;?></span></span>
    <div class="card-body">
        <?php echo $question->details;?>
        <hr/>


