<?php

namespace KevinsGuides\Component\Yaquiz\Administrator\View\Help;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

//get current version from xml
$manifest = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_yaquiz/yaquiz.xml');
$version = (string)$manifest->version;

//get xml from manifest server
$updateserver = $manifest->updateservers->server;
$updatemanifest = simplexml_load_file($updateserver);
$latestversion = (string)$updatemanifest->update->version;

if($latestversion == $version){
    $latestversion = Text::_('COM_YAQUIZ_LATEST_VERSION_INSTALLED');
}
else{
    $latestversion = Text::_('COM_YAQUIZ_UPDATE_AVAILABLE') . ': ' . $latestversion;
}


?>

<h1><?PHP echo Text::_('COM_YAQUIZ_HELP'); ?></h1>
<h3><?PHP echo Text::_('COM_YAQUIZ_VERSION'); ?>: <?php echo $version; ?></h3>
<h3><?php echo $latestversion; ?></h3>
<br/>
<a href="https://kevinsguides.com" target="_blank" class="btn btn-lg btn-primary"><?PHP echo Text::_('COM_YAQUIZ_BY_KG'); ?></a>
<a href="https://kevinsguides.com/guides/webdev/joomla4/free-extensions/yaq" target="_blank" class="btn btn-info btn-lg">Official Docs</a>
<a href="https://github.com/kevinsguides/yaquiz/releases" target="_blank" class="btn btn-dark btn-lg">Release Downloads</a>
<hr/>
<?php echo Text::_('COM_YAQUIZ_HELP_INTRO'); ?>
<hr/>
<h3><?php echo Text::_('COM_YAQUIZ_CATEGORIES'); ?></h3>
<?php echo Text::_('COM_YAQUIZ_CATEGORIES_INFO'); ?>
<HR/>
<h3><?php echo Text::_('COM_YAQUIZ_GETTING_STARTED'); ?></h3>
<p><?php echo Text::_('COM_YAQUIZ_GETTING_STARTED_DESC'); ?></p>
<ol>
    <li><?php echo Text::_('COM_YAQUIZ_GETTING_STARTED_1'); ?></li>
    <li><?php echo Text::_('COM_YAQUIZ_GETTING_STARTED_2'); ?></li>
    <li><?php echo Text::_('COM_YAQUIZ_GETTING_STARTED_3'); ?></li>
    <li><?php echo Text::_('COM_YAQUIZ_GETTING_STARTED_4'); ?></li>
    <li><?php echo Text::_('COM_YAQUIZ_GETTING_STARTED_5'); ?></li>
</ol>
<h3><?php echo Text::_('COM_YAQUIZ_PERM_CONFIG'); ?></h3>
<?php echo Text::_('COM_YAQUIZ_PERMISSIONS_HOWTO'); ?>
<h3><?php echo Text::_('COM_YAQUIZ_MATHJAX_HELP'); ?></h3>
<?php echo Text::_('COM_YAQUIZ_MATHJAX_HELP_DESC'); ?>
<pre>\(y=mx+b\)</pre>
<p>You can try a tool like this <a href="https://latexeditor.lagrida.com/" target="_blank">Latex Editor</a> to help you format your equations.</p>

<h3><?php echo Text::_('COM_YAQUIZ_MISC_HELP'); ?></h3>
<?php echo Text::_('COM_YAQUIZ_MISC_HELP_DETAILS'); ?>