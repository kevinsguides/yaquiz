<?php
namespace KevinsGuides\Component\Yaquiz\View\Quiz;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

//get this item
$item = $this->get('Item');


//get userstate results
$app = Factory::getApplication();
$wam = $app->getDocument()->getWebAssetManager();

$results = $app->getUserState('com_yaquiz.results');



//get config from component
$globalParams = $app->getParams('com_yaquiz');
if ($globalParams->get('get_mathjax') === '1') {
    $wam->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js', [], ['defer' => true]);
}
if ($globalParams->get('get_mathjax') === '2') {
    $wam->registerAndUseScript('com_yaquiz.mathjaxlocal', 'components/com_yaquiz/js/mathjax/es5/tex-svg.js', [], ['defer' => true]);
}

$theme = $globalParams->get('theme','default');
$stylefile = '/components/com_yaquiz/tmpl/' . $theme . '/style.css';
//if file exists
if (file_exists(JPATH_ROOT . $stylefile)) {
    $wam->registerAndUseStyle('com_yaquiz.quizstyle', $stylefile);
}


$wam->useStyle('fontawesome');


//if results are empty
if(empty($results)){
    $results = 'No results to display';
}

?>

<?php echo $results; ?>




