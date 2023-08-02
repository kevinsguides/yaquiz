<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Certificates;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;



$app = Factory::getApplication();


$certificate_name = $app->input->get('certfile', null, 'STRING');

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.keepalive');


$form = $this->get('Form');

?>

<h1>Edit Certificate: <?php echo $certificate_name; ?></h1>

<form id="adminForm" action="index.php?option=com_yaquiz&task=Certificates.save" method="post">
<?php echo $form->renderFieldset('certificates'); ?>

<input type="hidden" name="certfile_start" value="<?php echo $certificate_name; ?>" />
<input type="hidden" name="task" value="Certificates.save" />
<?php echo HTMLHelper::_('form.token'); ?>
</form>

<h3><?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS');?></h3>
<p><?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_DESC');?></p>
<ul class="list-unstyled">
<li><strong>USER_FULLNAME</strong> - <?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_USER_FULLNAME');?></li>
<li><strong>QUIZ_NAME</strong> - <?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_QUIZ_NAME');?></li>
<li><strong>QUIZ_SCORE</strong> - <?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_QUIZ_SCORE');?></li>
<li><strong>QUIZ_TOTAL</strong> - <?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_QUIZ_TOTAL');?></li>
<li><strong>QUIZ_DATE</strong> - <?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_QUIZ_DATE');?></li>
<li><strong>QUIZ_TIME</strong> - <?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_QUIZ_TIME');?></li>
<li><strong>CERT_CODE</strong> - <?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_CERT_CODE');?></li>
<li><strong>QUIZ_COPYRIGHT</strong> - <?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_QUIZ_COPYRIGHT');?></li>
<li><strong>SITEROOT_URI/</strong> - <?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_CONSTANTS_SITEROOT_URI');?></li>
</ul>
<p><?php echo Text::_('COM_YAQUIZ_CERTTEMPLATE_IMGNOTE');?></p>

