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


class CertificatesModel extends AdminModel
{

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function getForm($data = [], $loadData = true)
    {

        $app = Factory::getApplication();
        $certfile = $app->input->get('certfile', null, 'STRING');
        $certHelper = new CertHelper();
        $certificate_html = $certHelper->getCertHtml($certfile);

        //load certificates form
        $form = $this->loadForm('com_yaquiz.certificates', 'certificates', ['control' => 'jform', 'load_data' => $loadData]);

        //set templatehtml field value to the html of the certificate
        $form->setValue('templatehtml', null, $certificate_html);
        //if it's default.html, disable the name field

        if($certfile == 'default.html'){
            $form->setFieldAttribute('certfile', 'disabled', 'true');
        }
        
        $form->setValue('certfile', null, $certfile);


        return $form;

    }


}