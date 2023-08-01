<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\Helper;

defined ( '_JEXEC' ) or die;


class CertHelper{

    //certificates are just HTML template files so we don't store them in the db

    public function getCertificates(){
        $dir = JPATH_ROOT . '/components/com_yaquiz/certificates';
        $files = scandir($dir);
        $certs = array();
        foreach($files as $file){
            if($file != '.' && $file != '..'){
                $certs[] = $file;
            }
        }

        return $certs;
    }


    public function getCertHtml($certfile){

        $dir = JPATH_ROOT . '/components/com_yaquiz/certificates';
        $file = $dir . '/' . $certfile;

        if(!file_exists($file)){
            return false;
        }

        $html = file_get_contents($file);


        return $html;
    }


}