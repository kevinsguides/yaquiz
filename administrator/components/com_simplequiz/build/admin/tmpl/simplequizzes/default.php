<?php

namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\SimpleQuizzes;

use Joomla\CMS\HTML\HTMLHelper;Use Joomla\CMS\Log\Log;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use JRoute;
use JText;

//get the items from model
$this->items = $this->get('Items');
?>
<div>
    <?php if($this->items): ?>
        <ul>
            <?php foreach($this->items as $item): ?>
                <li><?php echo $item->title; ?> <a href="index.php?option=com_simplequiz&view=simplequiz&layout=edit&id=<?php echo $item->id ?>">Edit</a> <a href="index.php?option=com_simplequiz&view=simplequiz&id=<?php echo $item->id ?>">Details and Questions</a></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No quizzes found</p>
    <?php endif; ?>
</div>