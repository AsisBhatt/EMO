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
if ($viewCollection->renderContent) { ?>
	<script>
		$(document).ready(function(){
			$("#Sms_mobile").change(function(e){
				var total_remain_quota = parseInt('<?php echo $total_remain_quota; ?>');
				
				var input_val =  $("input[name='Sms[mobile]']");
				var btn = $("#sms_btn");
				var result = $(input_val).val().split(',');
				var sum = 0;
				if(total_remain_quota){
					if(result.length > total_remain_quota){
						btn.button('loading');
						$("#sms-reply-model").modal('show');
						//alert('Your Quota limit is exceeded! Please check your SMS Remaining Quota.if any query please contact support..');
					}else{
						btn.button('reset');
					}
				}else{
					btn.button('reset');
				}
				
			});
			$('#Sms_message').keyup(function(e){
				var value = $('#Sms_message').val();
			
				if (value.length == 0) {
					$('#totalChars').html(0);
					return;
				}

				var regex = /\s+/gi;
				
				var totalChars = value.length;
				
				if(value.length == 161){
					return false;
				}
				$('#totalChars').html(totalChars);
			});
		});
	</script>
    <div class="tabs-container">
    <?php 
    //echo $this->renderTabs();
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
    
    // and render only if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm', array(
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        )); 
        ?>
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
					<?php echo $form->labelEx($sms, 'mobile', array('class' => 'control-label'));?>
					<?php echo $form->textField($sms, 'mobile', $sms->getHtmlOptions('mobile')); ?>
					<?php echo $form->error($sms, 'mobile');?>
				</div> 
				<div class="form-group col-lg-12">
					<?php echo $form->labelEx($sms, 'message (Max 160 Characters)', array('class' => 'control-label'));?>
					<?php echo $form->textArea($sms, 'message', $sms->getHtmlOptions('message', array('rows' => 4, 'maxlength'=>160))); ?>
					<?php echo $form->error($sms, 'message');?>
					Total Charecter :<span id="totalChars">0</span><span id="error_span"></span>
				</div>
			</div>
			<!--<div class="form-group col-lg-8">
				<?php echo $form->labelEx($sms, 'SHORCODES:   {{firstname}} , {{lastname}}');?>
			</div>  
			-->
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
					<button type="submit" id="sms_btn" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Send');?></button>
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
    ?>
    </div>
	<div class="modal fade" id="sms-reply-model" tabindex="-1" role="dialog" aria-labelledby="sms-reply-model-label" aria-hidden="true">
		<div class="modal-dialog">
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			  <h4 class="modal-title"><?php echo Yii::t('sms', 'Send Single MMS Warning');?></h4>
			</div>
			<div class="modal-body">
				<p>Your Quota limit is exceeded! Please check your SMS Remaining Quota.if any query please contact support..</p>
			</div>
		  </div>
		</div>
	</div>
<?php 
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