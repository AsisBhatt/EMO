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

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false 
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) {
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
        $form = $this->beginWidget('CActiveForm', array(
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        )); 
        ?>
	<script>
		$(document).ready(function(){
			$(".btn-submit").html('Send Now');
			$('#setschedule').bind("click", function() {
				var set_schedule = $("input[name=smsschedule]:checked").val();
				if(set_schedule == 1){
					$(".schedule_class").show();
					$(".btn-submit").html('Set Campaign');
				}else{
					$(".schedule_class").hide();
					$(".btn-submit").html('Send Now');
				}
			});
		})
	</script>
	<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-user"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php if (!$smscampaign->isNewRecord) { ?>
					<?php echo CHtml::link(Yii::t('app', 'Create new'), array('sms_campaign/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
					<?php } ?>
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('sms_campaign/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
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
				<div class="form-group col-lg-12">
					<?php echo $form->labelEx($smscampaign, 'campaign_name', array('class' => 'control-label'));?>
					<?php echo $form->textField($smscampaign, 'campaign_name', $smscampaign->getHtmlOptions('campaign_name')); ?>
					<?php echo $form->error($smscampaign, 'campaign_name');?>
				</div>        
				<div class="form-group col-lg-12">
					<?php echo $form->labelEx($smscampaign, 'campaign_text', array('class' => 'control-label'));?>
					<?php echo $form->textArea($smscampaign, 'campaign_text', $smscampaign->getHtmlOptions('campaign_text')); ?>
					<?php echo $form->error($smscampaign, 'campaign_text');?>
				</div>
				<div class="form-group col-lg-12">
					<?php echo $form->labelEx($smscampaign, 'list_id', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($smscampaign, 'list_id', $mylists, $smscampaign->getHtmlOptions('list_id')); ?>
					<?php echo $form->error($smscampaign, 'list_id');?>
				</div>
				<div class="form-group col-lg-12">
					<label class="control-label">Schedule SMS</label>
					&nbsp;
					<?php echo CHtml::checkBox('smsschedule','',array('id' => 'setschedule','class' => 'inline')); ?>
				</div>
				<?php
					//echo date('Y-m-d H:i:s');
					//echo $smscampaign->send_at;exit;
					//echo $smscampaign->dateTimeFormatter->formatDateTime($smscampaign->send_at);exit; ?>
				<div class="form-group col-lg-6 schedule_class" style="display:none;">
					<?php echo $form->labelEx($smscampaign, 'send_at');?>
					<?php echo $form->hiddenField($smscampaign, 'send_at', $smscampaign->getHtmlOptions('send_at')); ?>
					<?php echo $form->textField($smscampaign, 'sendAt', $smscampaign->getHtmlOptions('send_at')); ?>
					<?php echo CHtml::textField('fake_send_at', $smscampaign->dateTimeFormatter->formatDateTime(date('Y-m-d H:i:s')), array(
						'data-date-format'  => 'yyyy-mm-dd hh:ii:ss', 
						'data-autoclose'    => true, 
						'data-language'     => LanguageHelper::getAppLanguageCode(),
						'data-syncurl'      => $this->createUrl('sms_campaign/sync_datetime'),
						'class'             => 'form-control',
						'style'             => 'visibility:hidden; height:1px; margin:0; padding:0;',
					)); ?>
					<?php echo $form->error($smscampaign, 'send_at');?>
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
				<div class="col-md-12">
					<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Set Campaign');?></button>
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