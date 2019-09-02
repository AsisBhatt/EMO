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
<hr />
<div class="col-lg-12">
    <div class="col-lg-9">
        <label><?php echo Yii::t('lists', 'Instead of the above message, redirect the subscriber to the following url:')?></label>
        <?php echo $form->textField($model, 'url', $model->getHtmlOptions('url'));?>
        <?php echo $form->error($model, 'url');?>
    </div>
    <div class="col-lg-3">
        <label><?php echo Yii::t('lists', 'After this number of seconds:');?></label>
        <?php echo $form->textField($model, 'timeout', $model->getHtmlOptions('timeout'));?>
        <?php echo $form->error($model, 'timeout');?>
    </div>
</div>
<div class="clearfix"><!-- --></div>