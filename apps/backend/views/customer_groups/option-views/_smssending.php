<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4
 */
 
 ?>
<div class="row">
	<div class="col-lg-12">
		<div class="form-group">
			<div class="alert alert-success">
				<?php echo Yii::t('settings', 'A sending quota of 1000 with a time value of 1 and a time unit of Day means the customer is able to send 1000 emails during 1 day.');?>
				<br />
				<?php echo Yii::t('settings', 'If waiting is enabled and the customer sends all emails in an hour, he will wait 23 more hours until the specified action is taken.');?>
				<br />
				<?php echo Yii::t('settings', 'However, if the waiting is disabled, the action will be taken immediatly.');?>
				<br />
				<?php echo Yii::t('settings', 'You can find a more detailed explanation for these settings {here}.', array(
					'{here}' => CHtml::link(Yii::t('settings', 'here'), Yii::app()->hooks->applyFilters('customer_sending_explanation_url', 'https://kb.mailwizz.com/articles/understanding-sending-quota-limits-work/') , array('target' => '_blank')),
				));?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'sms_quota', array('class' => 'control-label'));?>
				<?php echo $form->textField($model, 'sms_quota', $model->getHtmlOptions('sms_quota')); ?>
				<?php echo $form->error($model, 'sms_quota');?>
			</div>
			<!--<div class="form-group col-lg-4">
				<?php //echo $form->labelEx($model, 'sms_quota_time_value', array('class' => 'control-label'));?>
				<?php //echo $form->textField($model, 'sms_quota_time_value', $model->getHtmlOptions('sms_quota_time_value')); ?>
				<?php //echo $form->error($model, 'sms_quota_time_value');?>
			</div>
			<div class="form-group col-lg-4">
				<?php //echo $form->labelEx($model, 'sms_quota_time_unit', array('class' => 'control-label'));?>
				<?php //echo $form->dropDownList($model, 'sms_quota_time_unit', $model->getTimeUnits(), $model->getHtmlOptions('sms_quota_time_unit')); ?>
				<?php //echo $form->error($model, 'sms_quota_time_unit');?>
			</div>-->
		</div>
		
		<!--<div class="row">
			<div class="form-group col-lg-4">
				<?php //echo $form->labelEx($model, 'quota_wait_expire', array('class' => 'control-label'));?>
				<?php //echo $form->dropDownList($model, 'quota_wait_expire', $model->getYesNoOptions(), $model->getHtmlOptions('quota_wait_expire')); ?>
				<?php //echo $form->error($model, 'quota_wait_expire');?>
			</div>
			<div class="form-group col-lg-4">
				<?php //echo $form->labelEx($model, 'action_quota_reached', array('class' => 'control-label'));?>
				<?php //echo $form->dropDownList($model, 'action_quota_reached', $model->getActionsQuotaReached(), $model->getHtmlOptions('action_quota_reached')); ?>
				<?php //echo $form->error($model, 'action_quota_reached');?>
			</div>
			<div class="form-group col-lg-4" style="display: <?php //echo $model->action_quota_reached == 'move-in-group' ? 'block' : 'none';?>;">
				<?php //echo $form->labelEx($model, 'move_to_group_id', array('class' => 'control-label'));?>
				<?php //echo $form->dropDownList($model, 'move_to_group_id', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $model->getGroupsList()), $model->getHtmlOptions('move_to_group_id')); ?>
				<?php //echo $form->error($model, 'move_to_group_id');?>
			</div>			
		</div>-->
	</div>
</div>