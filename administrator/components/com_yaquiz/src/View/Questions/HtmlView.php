<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Questions;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined ( '_JEXEC' ) or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView{


    public function display($tpl = null)
    {
  
        //get model
        $model = $this->getModel();
        $app = Factory::getApplication();
        $wa = $app->getDocument()->getWebAssetManager();

        $cParams = ComponentHelper::getParams('com_yaquiz');
        if($cParams->get('load_mathjax')==='1'){
            $wa->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js');
        }
        $this->form = $model->getForm();
        //get all questions
        $this->items = $model->getItems();

        //set menu location
       


        $toolbar = Toolbar::getInstance('toolbar');
        //if we're on the default layout, add the toolbar
        if ($this->getLayout() === 'default') {
            $toolbar->appendButton('Link', 'new', 'COM_YAQUIZ_NEWQUESTION', 'index.php?option=com_yaquiz&view=Question&layout=edit');
            if($app->getIdentity()->authorise('core.edit', 'com_yaquiz') == true){
                $toolbar->appendButton('Link', 'file', 'COM_YAQUIZ_IMPORTEXCEL', 'index.php?option=com_yaquiz&view=Questions&layout=insertmulti');
            }
           
        }
        if ($this->getLayout() === 'insertmulti') {
            $toolbar->appendButton('Link', 'backward', 'COM_YAQUIZ_QUESTION_MGR', 'index.php?option=com_yaquiz&view=Questions&layout=default');
        }
        //add component options
        $toolbar->appendButton('Link', 'options', 'COM_YAQUIZ_COMPSETTINGS', 'index.php?option=com_config&view=component&component=com_yaquiz');
        $toolbar->appendButton('Link', 'folder', 'JCATEGORIES', 'index.php?option=com_categories&extension=com_yaquiz');
        ToolbarHelper::title(Text::_('COM_YAQUIZ_PAGETITLE_QUESTIONS'), 'list');
        ToolbarHelper::custom('Yaquizzes.display', 'list', 'list', 'COM_YAQUIZ_QUIZMANAGER', false);
        //ToolbarHelper::addNew('Questions.newQuestion');

        //display the view
        $app->setUserState('com_yaquiz.redirectbackto', Uri::getInstance()->toString());

        return parent::display($tpl);

    }


}