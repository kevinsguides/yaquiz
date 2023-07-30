<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\Helper;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;
class ThemeHelper
{



    //if not found in the theme, returns default file path
    public static function findFile($fileName){

        $app = Factory::getApplication();
        $globalParams = $app->getParams('com_yaquiz');

        $file_extension = substr($fileName, -4);

        //returns the path to the theme file
        $theme = $globalParams->get('theme','default');

        //if it's a php file
        if ($file_extension == '.php') {
            $the_file = 'components/com_yaquiz/tmpl/quiz/' . $theme . '/' . $fileName;

        }
        //if it's a css file
        elseif ($file_extension == '.css') {
            $the_file = '/components/com_yaquiz/tmpl/quiz/' . $theme . '/' . $fileName;
        }

        if (!file_exists(JPATH_ROOT . $the_file)) {
            $theme = 'default';
        }

        $the_file = 'components/com_yaquiz/tmpl/quiz/' . $theme . '/' . $fileName;

        return $the_file;


    }

}