<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */

?>
<?php foreach ($items as $value) { ?>
<li>
    <a href="<?php echo $value['url'];?>">
        <h3><?php echo $value['heading'];?> <small class="pull-right percentage"><?php echo $value['percent'];?>% <?php echo $value['used'];?>/<?php echo $value['allowed'];?></small></h3>
        <div class="progress xs">
            <div class="progress-bar progress-bar-<?php echo $value['bar_color'];?>" style="width: <?php echo $value['percent'];?>%" role="progressbar" aria-valuenow="<?php echo $value['percent'];?>" aria-valuemin="0" aria-valuemax="100">
                <span class="sr-only"><?php echo $value['percent'];?>% <?php echo Yii::t('app', 'Complete');?></span>
            </div>
        </div>
    </a>
</li>
<?php } ?>
