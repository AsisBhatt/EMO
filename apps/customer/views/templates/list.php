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
				<?php echo CHtml::link(Yii::t('app', 'Create new'), array('templates/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'New')));?>
				<?php echo CHtml::link(Yii::t('email_templates', 'Upload template'), '#template-upload-modal', array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'data-toggle' => 'modal', 'title' => Yii::t('email_templates', 'Upload template')));?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('templates/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
			</div>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<?php foreach ($templates as $model) { ?>	
			<div class="col-md-3">
				<div class="panel panel-default panel-template-box" style="height: 270px;" data-id="<?php echo $model->template_id;?>" data-url="<?php echo $this->createUrl('templates/update_sort_order');?>">
					<div class="panel-heading"><h3 class="panel-title"><?php echo $model->shortName;?></h3></div>
					<div class="panel-body">
						<a title="<?php echo Yii::t('email_templates',  'Preview');?> <?php echo CHtml::encode($model->name);?>" href="javascript:;" onclick="window.open('<?php echo $this->createUrl('templates/preview', array('template_uid' => $model->template_uid));?>','<?php echo Yii::t('email_templates',  'Preview') . ' '.CHtml::encode($model->name);?>', 'scrollbars=1, resizable=1, height=600, width=600'); return false;">
							<img class="img-rounded" src="<?php echo $model->screenshotSrc;?>" />
						</a>
					</div>
					<div class="panel-footer">
						<a href="<?php echo Yii::app()->createUrl("templates/delete", array("template_uid" => $model->template_uid));?>" class="btn green btn-xs btn-delete-template" data-confirm-text="<?php echo Yii::t('app', 'Are you sure you want to remove this item?')?>"><?php echo Yii::t('app', 'Delete');?></a>
						&nbsp;<a href="<?php echo Yii::app()->createUrl("templates/copy", array("template_uid" => $model->template_uid));?>" class="btn dark btn-xs"><?php echo Yii::t('app', 'Copy');?></a>
						&nbsp;<a href="<?php echo Yii::app()->createUrl("templates/update", array("template_uid" => $model->template_uid));?>" class="btn red-mint btn-xs"><?php echo Yii::t('app', 'Update');?></a>
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
              <h4 class="modal-title"><?php echo Yii::t('email_templates',  'Upload template archive');?></h4>
            </div>
            <div class="modal-body">
                 <div class="alert alert-success margin-bottom-20">
                    <?php
                    $text = '
                    Please see <a href="{templateArchiveHref}">this example archive</a> in order to understand how you should format your uploaded archive!
                    Also, please note we only accept zip files.';
                    echo Yii::t('email_templates',  StringHelper::normalizeTranslationString($text), array(
                        '{templateArchiveHref}' => Yii::app()->apps->getAppUrl('customer', 'assets/files/example-template.zip', false, true),
                    ));
                    ?>
                 </div>
                <?php 
                $form = $this->beginWidget('CActiveForm', array(
                    'action'        => array('templates/upload'),
                    'id'            => $templateUp->modelName.'-upload-form',
                    'htmlOptions'   => array(
                        'id'        => 'upload-template-form', 
                        'enctype'   => 'multipart/form-data'
                    ),
                ));
                ?>
                <div class="form-group">
                    <?php echo $form->labelEx($templateUp, 'archive', array('class' => 'control-label'));?>
                    <?php echo $form->fileField($templateUp, 'archive', $templateUp->getHtmlOptions('archive')); ?>
                    <?php echo $form->error($templateUp, 'archive');?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($templateUp, 'inline_css', array('class' => 'control-label'));?>
                    <?php echo $form->dropDownList($templateUp, 'inline_css', $templateUp->getInlineCssArray(), $templateUp->getHtmlOptions('inline_css')); ?>
                    <div class="help-block"><?php echo $templateUp->getAttributeHelpText('inline_css');?></div>
                    <?php echo $form->error($templateUp, 'inline_css');?>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($templateUp, 'minify', array('class' => 'control-label'));?>
                    <?php echo $form->dropDownList($templateUp, 'minify', $templateUp->getYesNoOptions(), $templateUp->getHtmlOptions('minify')); ?>
                    <div class="help-block"><?php echo $templateUp->getAttributeHelpText('minify');?></div>
                    <?php echo $form->error($templateUp, 'minify');?>
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