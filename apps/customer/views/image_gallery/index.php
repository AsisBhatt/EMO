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
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-text-width"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('image_gallery', 'Upload Image'), '#template-upload-modal', array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'data-toggle' => 'modal', 'title' => Yii::t('email_templates', 'Upload template')));?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('image_gallery/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
			</div>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<?php 
				$base_url = 'http://'.Yii::app()->getRequest()->serverName;
				$target_dir = "/myuploads/";
				foreach ($imagegallery as $model) { 
			?>	
			<div class="col-md-3">
				<div class="panel panel-default panel-template-box" style="height: 270px;" data-id="<?php echo $model->image_id;?>" data-url="<?php echo $this->createUrl('templates/update_sort_order');?>">
					<div class="panel-heading"><h3 class="panel-title"><?php echo $model->filename;?></h3></div>
					<div class="panel-body">
						<a title="<?php echo Yii::t('email_templates',  'Preview');?> <?php echo CHtml::encode($model->filename);?>" href="<?php echo $base_url.$target_dir.$model->filename;?>" target="__blanck">
							<img class="img-rounded img-responsive" src="<?php echo $base_url.$target_dir.$model->filename;?>" />
						</a>
					</div>
					<div class="panel-footer">
						<a href="<?php echo Yii::app()->createUrl("image_gallery/delete", array("image_id" => $model->image_id));?>" class="btn green btn-xs btn-delete-template" data-confirm-text="<?php echo Yii::t('app', 'Are you sure you want to remove this item?')?>"><?php echo Yii::t('app', 'Delete');?></a>
						&nbsp;<a href="<?php //echo Yii::app()->createUrl("templates/copy", array("template_uid" => $model->template_uid));?>" class="btn dark btn-xs"><?php echo Yii::t('app', 'Choose');?></a>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
		
    <div class="modal fade" id="template-upload-modal" tabindex="-1" role="dialog" aria-labelledby="template-upload-modal-label" aria-hidden="true">
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
                <div class="form-group">
                    <?php echo $form->labelEx($image_gallery, 'filename', array('class' => 'control-label'));?>
                    <?php echo $form->fileField($image_gallery, 'filename[]', $image_gallery->getHtmlOptions('filename',array('multiple' => true))); ?>
                    <?php echo $form->error($image_gallery, 'filename');?>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline dark" data-dismiss="modal"><?php echo Yii::t('app', 'Close');?></button>
              <button type="button" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>" onclick="$('#upload-template-form').submit();"><?php echo Yii::t('email_templates',  'Upload archive');?></button>
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