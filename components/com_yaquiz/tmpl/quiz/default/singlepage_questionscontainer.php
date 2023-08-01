<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

//This file is container for all the questions on a single-page quiz/test
// This is the section after the intro, where all the questions are held.

defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

?>



<form action="<?php echo Uri::root(); ?>index.php?option=com_yaquiz&task=quiz.submitquiz" method="post">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz->id; ?>" />
    
    <?php $questionBuilder->renderAllQuestions($questions, $quizparams, $oldanswers); ?>
              
    <?php echo HtmlHelper::_('form.token'); ?>
    <?php include($layout_submit_btn); ?>
</form>




