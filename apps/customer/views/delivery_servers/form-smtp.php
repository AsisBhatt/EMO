<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false 
 * in order to stop rendering the default content.
 * 
 * @since 1.3.3.1
 */
$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) { 
    $this->renderPartial('_confirm-form');
    /**
     * This hook gives a chance to prepend content before the active form or to replace the default active form entirely.
     * Please note that from inside the action callback you can access all the controller view variables 
     * via {@CAttributeCollection $collection->controller->data}
     * In case the form is replaced, make sure to set {@CAttributeCollection $collection->renderForm} to false 
     * in order to stop rendering the default content.
     * @since 1.3.3.1
     */
    $hooks->doAction('before_active_form', $collection = new CAttributeCollection(array(
        'controller'    => $this,
        'renderForm'    => true,
    )));
    
    // and render if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm');
        ?>
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-send"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('delivery_servers/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
				</div>
			</div>
		</div>
		<div class="portlet-body">
			<?php 
			/**
			 * This hook gives a chance to prepend content before the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables 
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.3.1
			 */
			$hooks->doAction('before_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form    
			)));
			?>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'name', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'name', $server->getHtmlOptions('name')); ?>
					<?php echo $form->error($server, 'name');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'hostname', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'hostname', $server->getHtmlOptions('hostname')); ?>
					<?php echo $form->error($server, 'hostname');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'username', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'username', $server->getHtmlOptions('username')); ?>
					<?php echo $form->error($server, 'username');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'password', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'password', $server->getHtmlOptions('password', array('value' => ''))); ?>
					<?php echo $form->error($server, 'password');?>
				</div>				
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'port', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'port', $server->getHtmlOptions('port')); ?>
					<?php echo $form->error($server, 'port');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'protocol', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'protocol', $server->getProtocolsArray(), $server->getHtmlOptions('protocol')); ?>
					<?php echo $form->error($server, 'protocol');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'timeout', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'timeout', $server->getHtmlOptions('timeout')); ?>
					<?php echo $form->error($server, 'timeout');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'from_email', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'from_email', $server->getHtmlOptions('from_email')); ?>
					<?php echo $form->error($server, 'from_email');?>
				</div>				
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'from_name', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'from_name', $server->getHtmlOptions('from_name')); ?>
					<?php echo $form->error($server, 'from_name');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<div class="row">
						<div class="col-lg-6">
							<?php echo $form->labelEx($server, 'hourly_quota', array('class' => 'control-label'));?>
							<?php echo $form->textField($server, 'hourly_quota', $server->getHtmlOptions('hourly_quota')); ?>
							<?php echo $form->error($server, 'hourly_quota');?>
						</div>
						<div class="col-lg-6">
							<?php echo $form->labelEx($server, 'monthly_quota', array('class' => 'control-label'));?>
							<?php echo $form->textField($server, 'monthly_quota', $server->getHtmlOptions('monthly_quota')); ?>
							<?php echo $form->error($server, 'monthly_quota');?>
						</div>
					</div>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'probability', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'probability', $server->getProbabilityArray(), $server->getHtmlOptions('probability', array('data-placement' => 'left'))); ?>
					<?php echo $form->error($server, 'probability');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'bounce_server_id', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'bounce_server_id', $server->getBounceServersArray(), $server->getHtmlOptions('bounce_server_id')); ?>
					<?php echo $form->error($server, 'bounce_server_id');?>
				</div> 
			</div>
			<div class="row">
				<?php if ($server->getCanUseQueue()) { ?>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'use_queue', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'use_queue', $server->getYesNoOptions(), $server->getHtmlOptions('use_queue')); ?>
					<?php echo $form->error($server, 'use_queue');?>
				</div>
				<?php } ?>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'signing_enabled', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'signing_enabled', $server->getYesNoOptions(), $server->getHtmlOptions('signing_enabled')); ?>
					<?php echo $form->error($server, 'signing_enabled');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'force_from', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'force_from', $server->getForceFromOptions(), $server->getHtmlOptions('force_from')); ?>
					<?php echo $form->error($server, 'force_from');?>
				</div>
			</div>
			<div class="row">
				<?php if (!empty($canSelectTrackingDomains)) { ?>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'tracking_domain_id', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'tracking_domain_id', $server->getTrackingDomainsArray(), $server->getHtmlOptions('tracking_domain_id')); ?>
					<?php echo $form->error($server, 'tracking_domain_id');?>
				</div>
				<?php } ?>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'reply_to_email', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'reply_to_email', $server->getHtmlOptions('reply_to_email')); ?>
					<?php echo $form->error($server, 'reply_to_email');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'force_reply_to', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'force_reply_to', $server->getForceReplyToOptions(), $server->getHtmlOptions('force_reply_to')); ?>
					<?php echo $form->error($server, 'force_reply_to');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'force_sender', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'force_sender', $server->getYesNoOptions(), $server->getHtmlOptions('force_sender')); ?>
					<?php echo $form->error($server, 'force_sender');?>
				</div>
			</div>
			<?php
			/**
			 * This hook gives a chance to append content after the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables 
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.3.1
			 */ 
			$hooks->doAction('after_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form    
			)));
			?> 
			<div class="row">
				<div class="col-lg-12">
					<?php $this->renderPartial('_policies', compact('form'));?> 
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<?php $this->renderPartial('_additional-headers');?> 
				</div>   
			</div>                                        
			<div class="row">
				<div class="col-md-12">
					<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
				</div>
			</div>
		</div>
        <?php 
        $this->endWidget(); 
    } 
    /**
     * This hook gives a chance to append content after the active form.
     * Please note that from inside the action callback you can access all the controller view variables 
     * via {@CAttributeCollection $collection->controller->data}
     * @since 1.3.3.1
     */
    $hooks->doAction('after_active_form', new CAttributeCollection(array(
        'controller'      => $this,
        'renderedForm'    => $collection->renderForm,
    )));
}
/**
 * This hook gives a chance to append content after the view file default content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * @since 1.3.3.1
 */
$hooks->doAction('after_view_file_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));