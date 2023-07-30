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

        //returns the path to the theme file
        $theme = $globalParams->get('theme','default');
        $the_file = 'components/com_yaquiz/tmpl/quiz/' . $theme . '/' . $fileName;
        //if file exists
        if (!file_exists(JPATH_ROOT . $the_file)) {
            $the_file = 'components/com_yaquiz/tmpl/quiz/default/' . $fileName;
        }
        return $the_file;
    }

}