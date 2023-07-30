<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

/**
 * submit template file
 * This file just contains the layout for a submit button. It comes at the end of each quiz.
 * It is part of a form, so it must be a BUTTON element with type="submit"
 */


defined ( '_JEXEC' ) or die;
use Joomla\CMS\Language\Text;

?>

<button type="submit" class="btn btn-success btn-lg"><?php echo Text::_('COM_YAQ_SUBMITQUIZ') ?></button>


