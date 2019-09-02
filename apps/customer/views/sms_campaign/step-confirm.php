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
    if ($smscampaign->hasErrors()) { ?>
    <div class="alert alert-block alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <?php echo CHtml::errorSummary($smscampaign);?>
    </div>
    <?php 
    }
    
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
        $form = $this->beginWidget('CActiveForm'); ?>
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-envelope"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('sms_campaign/index/type/'.strtolower($smscampaign->campaign_type)), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
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
				<div class="form-group col-lg-2 custom-checkbox">
					<label class="btn green btn-circle">Schedule SMS					
					<?php echo CHtml::checkBox('smsschedule','',array('id' => 'setschedule','class' => 'inline')); ?>
					</label>
				</div>
				<?php
					//echo $campaign->send_at;exit;
					//echo $campaign->dateTimeFormatter->formatDateTime($campaign->send_at);exit;
				?>
				<div class="col-lg-6 schedule_class" style="display:none;">
					<?php echo $form->labelEx($smscampaign, 'send_at');?>
					<?php echo $form->hiddenField($smscampaign, 'send_at', $smscampaign->getHtmlOptions('send_at')); ?>
					<?php echo $form->textField($smscampaign, 'sendAt', $smscampaign->getHtmlOptions('send_at')); ?>
					<?php echo CHtml::textField('fake_send_at', $smscampaign->dateTimeFormatter->formatDateTime($smscampaign->send_at), array(
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
			
			<div class="table-responsive margin-top-20" id="panelPrecios">
				<?php
				$this->widget('zii.widgets.CDetailView', array(
					'data'          => $smscampaign,
					'cssFile'       => false,
					'htmlOptions'   => array('class' => 'table table-striped table-bordered table-hover table-condensed'),
					'attributes'    => array(
						'campaign_name','campaign_text',
						array(
							'label' => $smscampaign->getAttributeLabel('campaign_media'),
							'type'=>'raw',
							'value' => function($smscampaign){
								$media_array = explode("/",$smscampaign->campaign_media);
								return CHtml::link(urldecode($media_array[4]), urldecode($smscampaign->campaign_media), array('data-toggle' =>'tooltip','title' => '<img src="'.urldecode($smscampaign->campaign_media).'" class="img-responsive"/>',"class"=>"btn btn-info","target"=>"_blank"));
							},
							'visible' => ($smscampaign->campaign_type == 'MMS' ? true : false),
							
						),
						array(
							'label' => $smscampaign->getAttributeLabel('list_id'),
							'value' => $smscampaign->list->name,
						),
						array(
							'label' => $smscampaign->getAttributeLabel('campaign_created'),
							'value' => $smscampaign->campaign_created,
						),
					),
				));
				?>
			</div>
			<div class="box-footer">
				<div class="wizard">
					<ul class="steps">
						<li class="complete"><a href="<?php echo $this->createUrl('sms_campaign/update', array('sms_campaign_id' => $smscampaign->sms_campaign_id));?>"><?php echo Yii::t('sms_campaign', 'Details');?></a><span class="chevron"></span></li>
						<li class="complete"><a href="<?php echo $this->createUrl('sms_campaign/setup', array('sms_campaign_id' => $smscampaign->sms_campaign_id));?>"><?php echo Yii::t('sms_campaign', 'Setup');?></a><span class="chevron"></span></li>
						<li class="active"><a href="<?php echo $this->createUrl('sms_campaign/conformation', array('sms_campaign_id' => $smscampaign->sms_campaign_id));?>"><?php echo Yii::t('sms_campaign', 'Confirmation');?></a><span class="chevron"></span></li>
						<li><a href="javascript:;"><?php echo Yii::t('app', 'Done');?></a><span class="chevron"></span></li>
					</ul>
					<div class="actions">
						<button type="submit" id="is_next" name="is_next" value="1" class="btn green btn-submit btn-go-next" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>">
							<?php echo Yii::t('campaigns', 'Send campaign');?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			$('#panelPrecios [data-toggle="tooltip"]').tooltip({
				animated: 'fade',
				placement: 'bottom',
				html: true
			});
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