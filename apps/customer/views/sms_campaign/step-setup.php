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
            'htmlOptions' => array(
                'enctype' => 'multipart/form-data',
            ),
        ));
        ?>
		<script>
			/*counter = function() {
				var value = $('#SmsCampaign_campaign_text').val();
				
				if (value.length == 0) {
					$('#totalChars').html(0);
					return;
				}

				var regex = /\s+/gi;
				
				var totalChars = value.length;
				console.log(totalChars);
				if(totalChars == 161){
					return false;
				}
				$('#totalChars').html(totalChars);
			};*/
			$(document).ready(function() {
				$("#choose_from_gallery").click(function(){
					$("#mms_gallery").modal('show');	
				});
				//$('#SmsCampaign_campaign_text').keydown(counter);
				//$('#SmsCampaign_campaign_text').keypress(counter);
				$('#SmsCampaign_campaign_text').keyup(function(e){
					var value = $('#SmsCampaign_campaign_text').val();
				
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
				//$('#SmsCampaign_campaign_text').focus(counter);
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
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-envelope"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('sms_campaign/index/'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
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
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($smscampaign, 'list_id', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($smscampaign, 'list_id', $mylists, $smscampaign->getHtmlOptions('list_id')); ?>
					<?php echo $form->error($smscampaign, 'list_id');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($smscampaign, 'campaign_text', array('class' => 'control-label'));?>
					<?php echo $form->textArea($smscampaign, 'campaign_text', $smscampaign->getHtmlOptions('campaign_text')); ?>
					<?php echo $form->error($smscampaign, 'campaign_text');?>
					Total Charecter :<span id="totalChars">0</span><span id="error_span"></span>
				</div>
				<?php if($smscampaign->campaign_type == 'MMS'){ ?>
					<div class="form-group col-lg-6">
						<label class="control-label">Select Media From</label>
						<div class="clearfix"></div>
						<?php echo CHtml::radioButtonList('select_media','',array('FRM_GALLERY'=>'From MMs Gallery','FRM_LOCAL'=>'From Local'),array( 'separator' => "  ",'onclick' => 'choose_media();'));?>
						
						<div id="frm_local" style="display:none;">
							<?php echo $form->labelEx($smscampaign, 'Select Media');?>
							<?php echo $form->fileField($smscampaign, 'campaign_media', $smscampaign->getHtmlOptions('campaign_media')); ?>
							<?php echo $form->error($smscampaign, 'campaign_media');?>
						</div>
						<div id="frm_media" style="display:none;">
							<div class="row">
								<div id="image_preview" style="padding:15px;" class="col-md-4"></div>
							</div>
							
							<button type="button" id="choose_from_gallery" name="choose_from_gallery" value="1" class="btn green btn-submit btn-go-next"><?php echo Yii::t('campaigns', 'Choose From MMS Gallery');?></button>
							<?php echo CHtml::hiddenField('choose_file' , '', array('id' => 'choose_file')); ?>
						</div>
					</div>
					
				<?php } ?>
			</div>
		</div>
		
		<?php
		//}
		/**
		 * This hook gives a chance to append content after the active form fields.
		 * Please note that from inside the action callback you can access all the controller view variables
		 * via {@CAttributeCollection $collection->controller->data}
		 *
		 * @since 1.3.3.1
		 */
		$hooks->doAction('after_active_form_fields', new CAttributeCollection(array(
			'controller'    => $this,
			'form'          => $form
		)));
		?>		
		<div class="portlet-body">
			<div class="box-footer">
				<div class="wizard">
					<ul class="steps">
						<li class="complete"><a href="<?php echo $this->createUrl('sms_campaign/update', array('sms_campaign_id' => $smscampaign->sms_campaign_id));?>"><?php echo Yii::t('campaigns', 'Details');?></a><span class="chevron"></span></li>
						<li class="active"><a href="<?php echo $this->createUrl('sms_campaign/setup', array('sms_campaign_id' => $smscampaign->sms_campaign_id));?>"><?php echo Yii::t('sms_campaigns', 'Setup');?></a><span class="chevron"></span></li>
						<li><a href="<?php echo $this->createUrl('sms_campaign/conformation', array('sms_campaign_id' => $smscampaign->sms_campaign_id));?>"><?php echo Yii::t('sms_campaign', 'Confirmation');?></a><span class="chevron"></span></li>
						<li><a href="javascript:;"><?php echo Yii::t('app', 'Done');?></a><span class="chevron"></span></li>
					</ul>
					<div class="actions">
						<button type="submit" id="is_next" name="is_next" value="1" class="btn green btn-submit btn-go-next" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('campaigns', 'Save and next');?></button>
					</div>
				</div>
			</div>
		</div>
        <?php
        $this->endWidget();
    }
    /**
     * This hook gives a chance to append content after the active form fields.
     * Please note that from inside the action callback you can access all the controller view variables
     * via {@CAttributeCollection $collection->controller->data}
     * @since 1.3.3.1
     */
    $hooks->doAction('after_active_form', new CAttributeCollection(array(
        'controller'      => $this,
        'renderedForm'    => $collection->renderForm,
    )));
    ?>
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
