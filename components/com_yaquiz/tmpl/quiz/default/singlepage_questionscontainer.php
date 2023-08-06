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
use Joomla\CMS\Log\Log;



?>




    
    <?php $questionBuilder->renderAllQuestions($questions, $quizparams, $oldanswers); ?>
              
    <?php echo HtmlHelper::_('form.token'); ?>
    <?php include($layout_submit_btn); ?>





