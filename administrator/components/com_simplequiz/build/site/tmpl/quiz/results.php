<?php
namespace KevinsGuides\Component\SimpleQuiz\View\Quiz;

defined('_JEXEC') or die;

//get this item
$item = $this->get('Item');

?>

<h1>The Results Page</h1>
<h2><?php echo $item->title; ?></h2>