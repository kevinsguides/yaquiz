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
                //must be a .html file
                if(substr($file, -5) != '.html'){
                    continue;
                }

                //remove .html extension
                $file = substr($file, 0, -5);
                $certs[] = $file;
            }
        }

        return $certs;
    }


    public function getCertHtml($certfile){

        $dir = JPATH_ROOT . '/components/com_yaquiz/certificates';
        $file = $dir . '/' . $certfile . '.html';

        if(!file_exists($file)){
            return false;
        }

        $html = file_get_contents($file);


        return $html;
    }

    public function deleteCertHtml($certfile){

        $dir = JPATH_ROOT . '/components/com_yaquiz/certificates';
        $file = $dir . '/' . $certfile . '.html';

        if(!file_exists($file)){
            return false;
        }

        unlink($file);

        return true;
    }

    public function saveCertHtml($certfile, $certfile_start, $html){

        //if the certfile was changed, delete the old one if it exists
        if($certfile != $certfile_start){
            $this->deleteCertHtml($certfile_start);
        }

        //make sure certfile ends with .html
        if(substr($certfile, -5) != '.html'){
            $certfile .= '.html';
        }

        //replace spaces with underscores
        $certfile = str_replace(' ', '_', $certfile);

        $dir = JPATH_ROOT . '/components/com_yaquiz/certificates';


        $file = $dir . '/' . $certfile;

        ob_start();
        echo $html;
        $html = ob_get_clean();


 

        file_put_contents($file, $html);

        return true;
    }


}