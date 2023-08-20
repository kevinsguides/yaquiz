<?php

defined ( '_JEXEC' ) or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();

$result = $app->getUserState('sq_verify_result', 'default');

$passed = $result->passed == 1 ? true : false;

?>
<div class="card">
    <span class="card-header fs-2"><?php echo Text::_('COM_YAQUIZ_CERT_VERIFIED');?></span>
    <div class="card-body">

    <div class="progress">
        <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $passed ? 'bg-success' : 'bg-danger' ;?>" role="progressbar" aria-valuenow="<?php echo $result->score;?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $result->score;?>%"></div>
    </div>

        <span><?php echo $passed ? Text::_('COM_YAQUIZ_CERT_IS_VALID') : Text::_('COM_YAQUIZ_CERET_IS_VALID_BUT_FAILED'); ?></span>
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

