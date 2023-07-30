<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Language\Text;


?>


</div>

<?php   if($quiz_params->quiz_use_points == 1 && $questionType != 'html_section') :?>
<div class="card-footer">
    <?php
  
        if($params->points > 1){
            echo Text::sprintf('COM_YAQ_POINTSWORTH', $params->points);
        }
        else{
            echo Text::_('COM_YAQ_POINTWORTH');
        }
    ?>

</div>
<?php endif; ?>
</div>
