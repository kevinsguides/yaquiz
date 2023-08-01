<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

/*
* This layout file is displayed before each question in the quiz.
* It should open the container (if any) the question is displayed in.
* Note: This affects questions on both paged and single-page quizzes.
* The question answers are displayed after this layout, before the footer.
*/

defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Language\Text;

//this is the code before each question is displayed
//it should contain the start of any container elements, the question itself, and the $question->details

//warning if item was not filled out
if (isset($question->defaultanswer) && $question->defaultanswer === 'missing') {
    $itemMissing .= '<i class="text-danger fas fa-exclamation-triangle me-2" title="'.Text::_('COM_YAQ_FORGOTANS').'"></i>';
}

?>


    <span class="fs-3"><?php echo $itemMissing . $formatted_questionnum . $question->question;?></span>
    <hr/>

        <?php echo $question->details;?>

        <?php // fields user uses to answer question appear here ?>





