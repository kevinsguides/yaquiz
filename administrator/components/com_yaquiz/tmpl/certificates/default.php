<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Certificates;
defined('_JEXEC') or die;

use KevinsGuides\Component\Yaquiz\Administrator\Helper\CertHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();
$wam = $app->getDocument()->getWebAssetManager();
$wam->registerAndUseScript('yaquiz-utils', 'administrator/components/com_yaquiz/src/Scripts/utils.js');


$certHelper = new CertHelper();

$certs = $certHelper->getCertificates();

?>

<h1><?php echo Text::_('COM_YAQUIZ_CERTIFICATES');?></h1>
<p><?php echo Text::_('COM_YAQUIZ_CERTIFICATE_TEMPLATE_DESC');?></p>
<table
class="table table-striped table-hover"
>
<thead>
<tr>
<th><?php echo Text::_('COM_YAQUIZ_TEMPLATE');?></th>
<th><?php echo Text::_('COM_YAQUIZ_EDIT'); ?></th>
<th><?php echo Text::_('COM_YAQUIZ_PREVIEW'); ?></th>
<th><?php echo Text::_('COM_YAQUIZ_DELETE'); ?></th>
</tr>
</thead>
<tbody>
<?php foreach($certs as $cert): ?>
<tr>
<td><?php echo $cert; ?></td>
<td><a href="index.php?option=com_yaquiz&view=certificates&layout=edit&certfile=<?php echo $cert; ?>"><?php echo ($cert == "default" ? Text::_('COM_YAQUIZ_VIEW') : Text::_('COM_YAQUIZ_EDIT')) ;?></a></td>
<td><a href="index.php?option=com_yaquiz&task=Certificates.getCertPreview&format=raw&certfile=<?php echo $cert; ?>"><?php echo Text::_('COM_YAQUIZ_PREVIEW');?></a></td>
<td><a class="doublecheckdialog" href="index.php?option=com_yaquiz&task=certificates.delete&certfile=<?php echo $cert; ?>"><?php echo ($cert == "default" ? '' : Text::_('COM_YAQUIZ_DELETE')) ;?></a></td>
</tr>
<?php endforeach; ?>
</table>

<form id="adminForm" action="index.php?option=com_yaquiz&task=certificates.edit" method="post">
<input name="task" type="hidden">
    <?php HTMLHelper::_('form.token'); ?>
</form>


<div class="card bg-light card-body">

<span><i class="fas fa-info-circle me-2"></i><?php echo Text::_('COM_YAQUIZ_REBUILD_VERIFY_CODES_DESC');?></span>
</div>