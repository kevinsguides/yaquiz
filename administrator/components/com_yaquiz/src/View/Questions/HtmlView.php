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
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{


    public function display($tpl = null)
    {

        //get model
        $model = $this->getModel();
        $app = Factory::getApplication();
        $wa = $app->getDocument()->getWebAssetManager();

        $cParams = ComponentHelper::getParams('com_yaquiz');
        if ($cParams->get('load_mathjax') === '1') {
            $wa->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js');
        }
        $this->form = $model->getForm();
        //get all questions
        $this->items = $model->getItems();

        //set menu location




        $toolbar = Factory::getContainer()->get(ToolbarFactoryInterface::class)->createToolbar('toolbar');

        //add component options

        ToolbarHelper::title(Text::_('COM_YAQUIZ_PAGETITLE_QUESTIONS'), 'list');
        if ($this->getLayout() === 'insertmulti') {
            ToolbarHelper::back();
        }


        //if we're on the default layout, add the toolbar
        if ($app->getIdentity()->authorise('core.edit', 'com_yaquiz') == true) {
            if ($this->getLayout() === 'default') {
                ToolbarHelper::addNew('Question.new', 'COM_YAQUIZ_NEWQUESTION');
                ToolbarHelper::link('index.php?option=com_yaquiz&view=Questions&layout=insertmulti', 'COM_YAQUIZ_IMPORTEXCEL', 'file', 'COM_YAQUIZ_IMPORTEXCEL', false);
            }
        }

        ToolbarHelper::link('index.php?option=com_categories&extension=com_yaquiz', 'JCATEGORIES', 'folder', 'JCATEGORIES', false);
        ToolbarHelper::link('index.php?option=com_yaquiz', 'COM_YAQUIZ_QUIZMANAGER', 'list', 'COM_YAQUIZ_QUIZMANAGER', false);
        ToolbarHelper::preferences('com_yaquiz');

        //display the view
        $app->setUserState('com_yaquiz.redirectbackto', Uri::getInstance()->toString());

        return parent::display($tpl);
    }
}
