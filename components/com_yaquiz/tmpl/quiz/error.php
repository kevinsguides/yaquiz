<?php
namespace KevinsGuides\Component\Yaquiz\Site\View\Quiz;


defined ( '_JEXEC' ) or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use KevinsGuides\Component\Yaquiz\Site\Helper\ThemeHelper;

//if $this-> item is already set
if (isset($this->item)) {
    $quiz = $this->item;
} else {
    $quiz = $this->get('Item');
}


$app = Factory::getApplication();
$globalParams = $app->getParams('com_yaquiz');
$theme = $globalParams->get('theme','default');

$error = $this->error;

$error_page = ThemeHelper::findFile('error.php');
include($error_page);

?>

