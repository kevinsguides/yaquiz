<?php
namespace KevinsGuides\Component\Yaquiz\Site\View\Quiz;


defined ( '_JEXEC' ) or die;
use Joomla\CMS\Language\Text;


//if $this-> item is already set
if (isset($this->item)) {
    $quiz = $this->item;
} else {
    $quiz = $this->get('Item');
}
?>

<div class="card">
    <h2 class="card-header">
        <?php echo $quiz->title; ?>
    </h2>

    <div class="card-body">
    <?php if($quiz->description): ?>
        <?php echo $quiz->description; ?>
    <?php endif; ?>
        <div class="card card-body bg-danger text-white"><?php echo Text::_('COM_YAQ_MAX_ATTEMPTS_REACHED'); ?></div>
    </div>
</div>
