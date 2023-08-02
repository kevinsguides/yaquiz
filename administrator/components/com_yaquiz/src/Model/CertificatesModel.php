<?php
/**
 * @copyright   (C) 2023 KevinsGuides.com
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace KevinsGuides\Component\Yaquiz\Administrator\Model;

defined ( '_JEXEC' ) or die;

use Joomla\CMS\MVC\Model\AdminModel;
use KevinsGuides\Component\Yaquiz\Administrator\Helper\CertHelper;
use Joomla\CMS\Factory;
use Exception;

use Joomla\CMS\Language\Text;

class CertificatesModel extends AdminModel
{

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function getForm($data = [], $loadData = true)
    {


        //user needs permission
        $user = Factory::getApplication()->getIdentity();
        if (!$user->authorise('core.edit', 'com_yaquiz')) {
            throw new Exception(Text::_('COM_YAQUIZ_PERM_REQUIRED_EDIT'));
        }

        $app = Factory::getApplication();
        $certfile = $app->input->get('certfile', null, 'STRING');
        


        $certHelper = new CertHelper();
        if($certfile != null){
            $certificate_html = $certHelper->getCertHtml($certfile);
        }
        else{
            $certificate_html = $certHelper->getCertHtml('default');
        }
        

        //load certificates form
        $form = $this->loadForm('com_yaquiz.certificates', 'certificates', ['control' => 'jform', 'load_data' => $loadData]);

        //set templatehtml field value to the html of the certificate
        $form->setValue('templatehtml', null, $certificate_html);
        //if it's default.html, disable the name field

        if($certfile == 'default'){
            $form->setFieldAttribute('certfile', 'disabled', 'true');
            $form->setFieldAttribute('templatehtml', 'disabled', 'true');
            $app->enqueueMessage(Text::_('COM_YAQUIZ_NOEDIT_DEFAULT_CERT'), 'warning');
        }
        
        $form->setValue('certfile', null, $certfile);

        return $form;

    }

    public function rebuildVerifyCodes(){

        
        //user needs permission
        $user = Factory::getApplication()->getIdentity();
        if (!$user->authorise('core.admin', 'com_yaquiz')) {
            throw new Exception(Text::_('COM_YAQUIZ_PERM_REQUIRED_ADMIN'));
        }

        $app = Factory::getApplication();
        $db = Factory::getContainer()->get('DatabaseDriver');

        $query = $db->getQuery(true);
        $query->select('id, user_id, verifyhash');
        $query->from('#__com_yaquiz_results');
        //only where verifyhash is null
        $query->where('verifyhash IS NULL');
        $db->setQuery($query);
        $results = $db->loadObjectList();

        //the new $verifyhash = substr(md5($user_id . $id), 0, 8);

        //loop through results
        foreach($results as $result){
            $verifyhash = substr(md5($result->user_id . $result->id), 0, 8);
            $query = $db->getQuery(true);
            $query->update('#__com_yaquiz_results');
            $query->set('verifyhash = ' . $db->quote($verifyhash));
            $query->where('id = ' . $result->id);
            $db->setQuery($query);
            $db->execute();
        }

        return true;
        
    }


}