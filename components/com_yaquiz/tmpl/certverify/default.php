<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\View\CertVerify;
defined ( '_JEXEC' ) or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;


?>






<!-- a text input for the certificate code -->
<form action="<?php echo Uri::root(); ?>index.php?option=com_yaquiz&task=quiz.verifyquiz" method="post">
<div class="card">
<span class="card-header fs-2"><?php echo Text::_('COM_YAQ_VERIFY_CERTIFICATE');?></span>
<div class="card-body">

<div class="form-group">
    <?php echo HtmlHelper::_('form.token'); ?>
    <label for="certcode"><?php echo Text::_('COM_YAQ_CERT_CODE');?></label>
    <input type="text" class="form-control" id="certcode" name="certcode" 
    maxlength="8"
    placeholder="<?php echo Text::_('COM_YAQ_CERT_CODE');?>" required>
    <span><?php echo Text::_('COM_YAQ_CERT_CODE_DESC');?></span>
    
</div>

</div>
<div class="card-footer">
<input type="submit" class="btn btn-success" value="<?php echo Text::_('COM_YAQ_VERIFY');?>">
</div>
</div>
</form>