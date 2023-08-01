<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Certificates;
defined('_JEXEC') or die;

use KevinsGuides\Component\Yaquiz\Administrator\Helper\CertHelper;

$certHelper = new CertHelper();

$certs = $certHelper->getCertificates();

?>

<h1>Certificate Templates</h1>
<table
class="table table-striped table-hover"
>
<thead>
<tr>
<th>Template</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach($certs as $cert): ?>
<tr>
<td><?php echo $cert; ?></td>
<td><a href="index.php?option=com_yaquiz&view=certificates&layout=edit&certfile=<?php echo $cert; ?>">Edit</a></td>
</tr>
<?php endforeach; ?>
</table>
