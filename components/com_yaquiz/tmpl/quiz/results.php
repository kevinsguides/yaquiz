<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\View\Quiz;

use Joomla\CMS\Factory;
use KevinsGuides\Component\Yaquiz\Site\Helper\ThemeHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

//get this item
$item = $this->get('Item');

//get userstate results
$app = Factory::getApplication();
$wam = $app->getDocument()->getWebAssetManager();
$wam->registerAndUseStyle('com_yaquiz.quiz', ThemeHelper::findFile('style.css'));

$results=$app->getUserState('com_yaquiz.results');

//get config from component
$globalParams = $app->getParams('com_yaquiz');
if ($globalParams->get('get_mathjax') === '1') {
    $wam->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js', [], ['defer' => true]);
}
if ($globalParams->get('get_mathjax') === '2') {
    $wam->registerAndUseScript('com_yaquiz.mathjaxlocal', 'components/com_yaquiz/js/mathjax/es5/tex-svg.js', [], ['defer' => true]);
}




$wam->useStyle('fontawesome');


//if results are empty
if(empty($results)){
    $results = Text::_('COM_YAQUIZ_NO_RESULTS');
}

?>

<div class="quiz-results">
<?php echo $results; ?>
</div>






