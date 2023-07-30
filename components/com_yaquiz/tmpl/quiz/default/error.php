<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

/**
 * This file loads the error page if issues with access, quiz not found, etc.
 * Accessible vars: $error->type $error->message $error->title
 */

defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Language\Text;



?>

<div class="card">
    <h2 class="card-header bg-danger text-white"><span class="icon-error"></span> <?php echo $error->title; ?></h2>
    <div class="card-body bg-white">
        <p><?php echo $error->message; ?></p>
    </div>
</div>
