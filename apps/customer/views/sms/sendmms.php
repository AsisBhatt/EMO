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
		<script>
			$(document).ready(function() {
				$("#choose_from_gallery").click(function(){
					$("#mms_gallery").modal('show');	
				});
				$("#Sms_mobile").change(function(e){
					var total_remain_quota = parseInt('<?php echo $total_remain_quota; ?>');
					var input_val =  $("input[name='Sms[mobile]']");
					var btn = $("#sms_btn");
					var result = $(input_val).val().split(',');
					var sum = 0;
					
					if(result.length > total_remain_quota){
						btn.button('loading');
						$("#sms-reply-model").modal('show');
						//alert('Your Quota limit is exceeded! Please check your SMS Remaining Quota.if any query please contact support..');
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
			function choose_media(){
				var select_media = $('input[name=select_media]:checked').val();
				if(select_media == 'FRM_LOCAL'){
					$("#frm_local").show();
					$("#frm_media").hide();
				}else if(select_media == 'FRM_GALLERY'){
					$("#frm_local").hide();
					$("#frm_media").show();
				}
				
			}
			function choose_image(image_name){
				var base_url = "<?php echo 'http://'.Yii::app()->getRequest()->serverName.'/myuploads/'; ?>";
				var pop_image = $("#choose_file").val(base_url+image_name);
				$("#image_preview").html("<img src='"+base_url+image_name+"' class='img-rounded img-responsive'>");
				
				if(pop_image.length > 0){
					$("#mms_gallery").modal('hide');
					$("#choose_from_gallery").html('Choose From MMS Gallery');
					$("#choose_from_gallery").prop("disabled", false);
					$("#choose_from_gallery").removeClass("disabled");
					
				}
			}
		</script>
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
				<div class="form-group col-lg-6">
					<label class="control-label">Select Media From</label>
					<div class="clearfix"></div>
					<?php echo CHtml::radioButtonList('select_media','',array('FRM_GALLERY'=>'From MMs Gallery','FRM_LOCAL'=>'From Local'),array( 'separator' => "  ",'onclick' => 'choose_media();'));?>
				</div>
				<div class="form-group col-lg-6">
					<div id="frm_local" style="display:none;">
						<?php echo $form->labelEx($sms, 'Select Media');?>
						<?php echo $form->fileField($sms, 'media', $sms->getHtmlOptions('media')); ?>
						<?php echo $form->error($sms, 'media');?>
					</div>
					<div id="frm_media" style="display:none;">
						<div class="row">
							<div id="image_preview" style="padding:15px;" class="col-md-4"></div>
						</div>
						
						<button type="button" id="choose_from_gallery" name="choose_from_gallery" value="1" class="btn green btn-submit btn-go-next"><?php echo Yii::t('campaigns', 'Choose From MMS Gallery');?></button>
						<?php echo CHtml::hiddenField('choose_file' , '', array('id' => 'choose_file')); ?>
					</div>
				</div>
				<div class="form-group col-lg-12">
					<span class="label label-danger"> NOTES: </span>
					<?php //echo $form->labelEx($sms, ''); ?>
					<span style="color:red;">File Size Should be less then 730kB.</span>
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
	<div class="modal fade" id="mms_gallery" tabindex="-1" role="dialog" aria-labelledby="mms_gallery" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title"><?php echo Yii::t('image_gallery',  'Upload MMS Image');?></h4>
            </div>
            <div class="modal-body">
                 <!--<div class="alert alert-success margin-bottom-20"></div>-->
                <?php 
                $form = $this->beginWidget('CActiveForm', array(
                    'action'        => array('image_gallery/create'),
                    'id'            => $image_gallery->modelName.'-upload-form',
                    'htmlOptions'   => array(
                        'id'        => 'upload-template-form', 
                        'enctype'   => 'multipart/form-data'
                    ),
                ));
                ?>
				<div class="portlet-body custom-panel">
					<ul class="row">
						<?php 
							$count_image = 1;
							$base_url = 'http://'.Yii::app()->getRequest()->serverName;
							$target_dir = "/myuploads/";
							foreach ($imagegallery as $model) { 
						?>	
						<li class="col-md-4">
							<div class="panel panel-default panel-template-box" style="height: 170px;" data-id="<?php echo $model->image_id;?>" data-url="<?php echo $this->createUrl('templates/update_sort_order');?>">
								<div class="panel-heading"><h5 class="panel-title"><?php echo $model->filename;?></h5></div>
								<div class="panel-body">
									<a title="<?php echo Yii::t('email_templates',  'Preview');?> <?php echo CHtml::encode($model->filename);?>" href="<?php echo $base_url.$target_dir.$model->filename;?>" target="__blanck">
										<img class="img-rounded img-responsive" src="<?php echo $base_url.$target_dir.$model->filename;?>"/>
									</a>
								</div>
								<div class="panel-footer">
									<a href="javascript:void(0);" class="btn dark btn-xs" onclick="choose_image('<?php echo $model->filename; ?>');"><?php echo Yii::t('app', 'Choose');?></a>
								</div>
							</div>
						</li>
						<?php 
								// if(round($count_image/4) == 0){
									// echo '<div class="clearfix"></div>';
								// }
								$count_image++;
							} 
						?>
					</ul>
				</div>
                <?php $this->endWidget(); ?>
            </div>
          </div>
        </div>
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