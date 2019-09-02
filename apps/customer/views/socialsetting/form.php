<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.9
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
        //$form = $this->beginWidget('CActiveForm');
		$form = $this->beginWidget('CActiveForm', array(
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        )); 
        ?>
		<script>
			function toggle_field(Obj){
				if(Obj.value == 'facebook_video'){
					$("#facebook_video_").show();
					$("#facebook_image_").hide();
				}else if(Obj.value == 'facebook_image'){
					$("#facebook_image_").show();
					$("#facebook_video_").hide();
				}else{
					$("#facebook_image_").hide();
					$("#facebook_video_").hide();
				}
			};
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
					<?php if (!$message->isNewRecord) { ?>
					<?php //echo CHtml::link(Yii::t('app', 'Create new'), array('socialsetting/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
					<?php } ?>
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('socialsetting/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
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
			<div class="clearfix"><!-- --></div>
			
			<div class="col-lg-12">
				<div class="row">
					<!--<div class="col-md-2">
						<label class="mt-checkbox mt-checkbox-outline">
							<input placeholder="Facebook" name="Socialpost[facebook]" id="" type="checkbox">
							<b>Facebook </b>
							<span></span>
						</label> 
					</div>
					<div class="col-md-2">
						<label class="mt-checkbox mt-checkbox-outline">
							<input checked Placeholder="Twitter" name="Socialpost[twitter]" id="" type="checkbox">
							<b>Twitter</b>
							<span></span>
						</label>
					</div>
					<div class="col-md-2">
						<label class="mt-checkbox mt-checkbox-outline">
							<input checked Placeholder="LinkedIn" name="Socialpost[linkedin]" id="" type="checkbox">
							<b>LinkedIn</b>
							<span></span>
						</label>
					</div> -->
					<div class="col-md-2">
						<label class="mt-radio mt-radio-outline">
							<input placeholder="Image" name="upload_type" value="facebook_image" id="facebook_select" type="radio" onClick="toggle_field(this);">
							<b>Upload Image</b>
							<span></span>
						</label>
					</div>
					<div class="col-md-2">
						<label class="mt-radio mt-radio-outline">
							<input placeholder="Video" name="upload_type" value="facebook_video" id="facebook_select" type="radio" onClick="toggle_field(this);">
							<b>Upload Video</b>
							<span></span>
						</label>
					</div>
					<div class="col-md-2">
						<label class="mt-radio mt-radio-outline">
							<input placeholder="Video" name="upload_type" value="facebook_text" id="facebook_select" type="radio" onClick="toggle_field(this);">
							<b>Only Text</b>
							<span></span>
						</label>		
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($message, 'text');?>
					<?php echo $form->textArea($message, 'text', $message->getHtmlOptions('text')); ?>
					<?php echo $form->error($message, 'text');?>
				</div>
				<div class="form-group col-lg-6" id="facebook_image_" style="display:none;">
					<?php echo $form->labelEx($message, 'Select Image');?>
					<?php echo $form->fileField($message, 'imagename', $message->getHtmlOptions('imagename')); ?>
					<?php echo $form->error($message, 'imagename');?>    
				</div>
				
				<div class="form-group col-lg-6" id="facebook_video_" style="display:none;">
					<?php echo $form->labelEx($message, 'Select Video');?>
					<?php echo $form->fileField($message, 'videoname', $message->getHtmlOptions('videoname')); ?>
					<?php echo $form->error($message, 'videoname');?>    
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
					<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Post Status');?></button>
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
