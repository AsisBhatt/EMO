<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.6
 */
 
 ?>
<div class="row">
	<div class="col-lg-12">
		<div class="caption margin-bottom-10">
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('settings', 'Tracking domains')?>
			</span>
		</div>		
		<div class="alert alert-success margin-bottom-10">
			<?php echo Yii::t('settings', 'Please note, in order for this feature to work this (sub)domain needs a dedicated IP address, otherwise all defined CNAMES for it will point to the default domain on this server.');?>
			<br />
			<strong><?php echo Yii::t('settings', 'If you do not use a dedicated IP address for this domain only or you are not sure you do so, do not enable this feature!');?></strong>
		</div>		
		<div class="row">
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'can_manage_tracking_domains', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'can_manage_tracking_domains', $model->getYesNoOptions(), $model->getHtmlOptions('can_manage_tracking_domains')); ?>
				<?php echo $form->error($model, 'can_manage_tracking_domains');?>
			</div>
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'can_select_for_delivery_servers', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'can_select_for_delivery_servers', $model->getYesNoOptions(), $model->getHtmlOptions('can_select_for_delivery_servers')); ?>
				<?php echo $form->error($model, 'can_select_for_delivery_servers');?>
			</div>
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'can_select_for_campaigns', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'can_select_for_campaigns', $model->getYesNoOptions(), $model->getHtmlOptions('can_select_for_campaigns')); ?>
				<?php echo $form->error($model, 'can_select_for_campaigns');?>
			</div>
		</div>
	</div>
</div>