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
		<div class="row">
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'max_delivery_servers', array('class' => 'control-label'));?>
				<?php echo $form->textField($model, 'max_delivery_servers', $model->getHtmlOptions('max_delivery_servers',array('class' => 'form-control form-filter'))); ?>
				<?php echo $form->error($model, 'max_delivery_servers');?>
			</div>
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'max_bounce_servers', array('class' => 'control-label'));?>
				<?php echo $form->textField($model, 'max_bounce_servers', $model->getHtmlOptions('max_bounce_servers', array('class' => 'form-control form-filter'))); ?>
				<?php echo $form->error($model, 'max_bounce_servers');?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'max_fbl_servers', array('class' => 'control-label'));?>
				<?php echo $form->textField($model, 'max_fbl_servers', $model->getHtmlOptions('max_fbl_servers', array('class' => 'form-control form-filter'))); ?>
				<?php echo $form->error($model, 'max_fbl_servers');?>
			</div>
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'must_add_bounce_server', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'must_add_bounce_server', $model->getYesNoOptions(), $model->getHtmlOptions('must_add_bounce_server', array('class' => 'form-control form-filter'))); ?>
				<?php echo $form->error($model, 'must_add_bounce_server');?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'can_select_delivery_servers_for_campaign', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'can_select_delivery_servers_for_campaign', $model->getYesNoOptions(), $model->getHtmlOptions('can_select_delivery_servers_for_campaign', array('class' => 'form-control form-filter'))); ?>
				<?php echo $form->error($model, 'can_select_delivery_servers_for_campaign');?>
			</div>
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'can_send_from_system_servers', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'can_send_from_system_servers', $model->getYesNoOptions(), $model->getHtmlOptions('can_send_from_system_servers', array('class' => 'form-control form-filter'))); ?>
				<?php echo $form->error($model, 'can_send_from_system_servers');?>
			</div> 
		</div>
		<div class="row">
			<div class="form-group col-lg-12 ">
				<?php echo Yii::t('settings', 'Custom headers');?>
				<?php echo $form->textArea($model, 'custom_headers', $model->getHtmlOptions('custom_headers', array('rows' => 5))); ?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-lg-12">
				<div class="row">
					<div class="col-md-2 col-sm-12">
						<div class="caption">
							<span class="caption-subject font-dark"><?php echo Yii::t('servers', 'Assigned servers');?>:</span>
						</div>
					</div>
					<div class="col-md-10 col-sm-12">					
						<?php foreach ($allDeliveryServers as $server) { ?>
							<div class="md-checkbox">
								<?php echo CHtml::checkBox($deliveryServerToCustomerGroup->modelName.'[]', in_array($server->server_id, $assignedDeliveryServers), array('class' => 'md-check'), array('value' => $server->server_id));?>							
								<label for="DeliveryServerToCustomerGroup"> 
									<span></span>
									<span class="check"></span>
									<span class="box"></span> <?php echo $server->displayName; ?>		
								</label>
							</div>
						<?php } ?> 		
					</div>
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="form-group col-lg-12">
				<div class="caption margin-bottom-10">
					<span class="caption-subject font-dark sbold">
						<?php echo Yii::t('settings', 'Allowed server types');?>:
					</span>
				</div>
				<?php echo $form->error($model, 'allowed_server_types');?>
				<div class="row">
					<?php foreach ($model->getServerTypesList() as $type => $name) { ?>					
						<div class="form-group col-lg-4">							
							<div class="col-lg-8">
								<?php echo CHtml::label(Yii::t('settings', 'Server type'), '_dummy_');?>
								<?php echo CHtml::textField('_dummy_', $name, $model->getHtmlOptions('allowed_server_types', array('readonly' => true)));?>
							</div>
							<div class="col-lg-4">
								<?php echo CHtml::label(Yii::t('settings', 'Allowed'), '_dummy_');?>
								<?php echo CHtml::dropDownList($model->modelName . '[allowed_server_types]['.$type.']', in_array($type, $model->allowed_server_types) ? 'yes' : 'no', $model->getYesNoOptions(), $model->getHtmlOptions('allowed_server_types'));?>
							</div>
						</div>					
					<?php } ?>
				</div>
			</div>
		</div>	
	</div>
</div>