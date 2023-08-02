<?php

defined ( '_JEXEC' ) or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();

$result = $app->getUserState('sq_verify_result', 'default');

?>
<div class="card">
    <span class="card-header fs-2"><?php echo Text::_('COM_YAQUIZ_CERT_VERIFIED');?></span>
    <div class="card-body">
        <span><?php echo Text::_('COM_YAQUIZ_CERT_IS_VALID'); ?></span>
        <table class="table table-striped">
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_CERT_NAME');?></td>
                <td><?php echo $result->name;?></td>
            </tr>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_QUIZ_TITLE');?></td>
                <td><?php echo $result->quiz_title;?></td>
            </tr>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_SUBMITTED');?></td>
                <td><?php echo $result->submitted;?></td>
            </tr>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_CERT_SCORE');?></td>
                <td><?php echo $result->score;?></td>
            </tr>
        </table>
    </div>
</div>
