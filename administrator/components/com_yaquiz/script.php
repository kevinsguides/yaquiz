<?php

defined ( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Version;
use KevinsGuides\Component\Yaquiz\Administrator\Model\YaquizModel;

class com_yaquizInstallerScript
{

    public function install($parent) 
    {
        echo '<p>' . Text::_('COM_YAQUIZ_INSTALL_TEXT') . '</p>';
        echo '<br/><a href="index.php?option=com_yaquiz&view=help" class="btn btn-primary">' . Text::_('COM_YAQUIZ_OPEN_HELP') . '</a>';
    }

    public function uninstall($parent) 
    {
        echo '<p>YaQuiz Is Gone. Good bye!</p>';
    }

    public function update($parent) 
    {
        echo '<p>' . Text::sprintf('COM_YAQUIZ_UPDATE_TEXT', $this->new_version) . '</p>';

    }

    public function preflight($type, $parent) 
    {

        if($type == 'update'){

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
            $db = Factory::getContainer()->get('DatabaseDriver');
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
        

    }

    public function postflight($type, $parent) 
    {

        if($type == 'update'){
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


            //if we're updating from a version older than 0.4.25, we need to generate alias for quizzes based off their titles
            if (version_compare($this->old_version_short, '0.4.25', '<')){ 

                $db = Factory::getContainer()->get('DatabaseDriver');
                $db->setQuery('SELECT * FROM #__com_yaquiz_quizzes');
                $quizzes = $db->loadObjectList();

                $yaqmodel = new YaquizModel;

                foreach ($quizzes as $quiz){
                    $alias = $yaqmodel->sanitizeAlias('', $quiz->title);
                    $db->setQuery('UPDATE #__com_yaquiz_quizzes SET alias = "' . $alias . '" WHERE id = ' . $quiz->id);
                    $db->execute();
                }

                $app = Factory::getApplication();
                $app->enqueueMessage(Text::_('COM_YAQUIZ_UPDATE_0_4_25'), 'notice');
            }

            if (version_compare($this->old_version_short, '0.5.00', '<')){ 
                $app->enqueueMessage(Text::_('COM_YAQUIZ_UPDATE_0_5_00'), 'notice');
            }

        }


        
    }




}