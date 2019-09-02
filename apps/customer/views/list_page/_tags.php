<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
?>
<div class="alert alert-success">
    <label><?php echo Yii::t('lists', 'Available tags:');?></label>
    <?php foreach ($tags as $tag) { ?>
    <a href="javascript:;" class="btn green btn-xs" data-tag-name="<?php echo CHtml::encode($tag['tag']);?>">
        <?php echo CHtml::encode($tag['tag']);?>
    </a>
    <?php } ?>
</div>