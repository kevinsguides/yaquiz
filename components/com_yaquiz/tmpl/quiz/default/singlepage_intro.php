<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

defined ( '_JEXEC' ) or die ();
//this is the introductory card for the quiz which displays the details and title
//it appears above the questions on a single page quiz
?>


<div class="card">
    <h2 class="card-header">
        <?php echo $quiz->title; ?>
    </h2>
    <div class="card-body">
        <?php echo $quiz->description; ?>
    </div>
</div>

<br />