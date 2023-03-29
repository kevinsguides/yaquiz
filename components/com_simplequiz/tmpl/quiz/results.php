<?php
namespace KevinsGuides\Component\SimpleQuiz\View\Quiz;
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

//get this item
$item = $this->get('Item');


//get userstate results
$app = Factory::getApplication();
$wa = $app->getDocument()->getWebAssetManager();

$results = $app->getUserState('com_simplequiz.results');



//get config from component
$globalParams = $app->getParams('com_simplequiz');
if($globalParams->get('load_mathjax') === '1'){
    $wa->registerAndUseScript('com_simplequiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js');
}

$wa->useStyle('fontawesome');

//get results from session




//if results are empty
if(empty($results)){
    $results = 'No results to display';
}

?>

<?php echo $results; ?>




