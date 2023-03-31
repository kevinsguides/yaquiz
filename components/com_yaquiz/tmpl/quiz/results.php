<?php
namespace KevinsGuides\Component\Yaquiz\View\Quiz;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

//get this item
$item = $this->get('Item');


//get userstate results
$app = Factory::getApplication();
$wa = $app->getDocument()->getWebAssetManager();

$results = $app->getUserState('com_yaquiz.results');



//get config from component
$globalParams = $app->getParams('com_yaquiz');
if($globalParams->get('load_mathjax') === '1'){
    $wa->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js');
}

$wa->useStyle('fontawesome');

//get results from session




//if results are empty
if(empty($results)){
    $results = 'No results to display';
}

?>

<?php echo $results; ?>




