<?php

defined ( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;

class com_yaquizInstallerScript
{

    public function install($parent) 
    {
        $parent->getParent()->setRedirectURL('index.php?option=com_yaquiz&view=quizzes');
    }

    public function uninstall($parent) 
    {
        echo '<p>' . Text::_('COM_YAQUIZ_UNINSTALL_TEXT') . '</p>';
    }

    public function update($parent) 
    {
        echo '<p>' . Text::_('COM_YAQUIZ_UPDATE_TEXT') . '</p>';

    }

    public function preflight($type, $parent) 
    {

        $parent   = $parent->getParent();
        $source   = $parent->getPath("source");
        $manifest = $parent->get("manifest");

        $pos = strpos($manifest->version, ' ');
        if ($pos){
            $this->new_version_short     = substr($manifest->version, 0, $pos);
        } else {
            $this->new_version_short     = $manifest->version;
        }    
        $this->new_version              = (string)$manifest->version;

        //get old version
        $db = JFactory::getDbo();
        $db->setQuery('SELECT * FROM #__extensions WHERE `element` = "com_yaquiz" AND `type` = "component"');
        $item = $db->loadObject();
        $old_manifest = json_decode($item->manifest_cache); 
        $pos = strpos($old_manifest->version, ' ');
        if ($pos){
            $this->old_version_short = substr($old_manifest->version, 0, $pos);    
        } else {
            $this->old_version_short = $old_manifest->version;    
        } 

        $this->old_version = (string)$old_manifest->version;
        

    }

    public function postflight($type, $parent) 
    {

       //if we are updating from version 0.4.09 or earlier
       if (version_compare($this->old_version_short, '0.4.10', '<')){ 

            //version 0.4.10 introduced a new 'numbering' column for quiz questions in the question map table
            //the numbering column should be equal to ordering at this point
            $db = Factory::getContainer()->get('DatabaseDriver');
            $db->setQuery('UPDATE #__com_yaquiz_question_quiz_map SET numbering = ordering');
            $db->execute();

            $app = Factory::getApplication();
            $app->enqueueMessage(Text::_('COM_YAQUIZ_UPDATE_0_4_10'), 'notice');

       }
        
    }




}