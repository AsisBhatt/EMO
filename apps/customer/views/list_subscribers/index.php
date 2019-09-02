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
	<div class="text-right margin">
		
        <?php 
			if($list->customer_id != 0){ 
				$this->widget('customer.components.web.widgets.MailListSubNavWidget', array(
					'list' => $list,
				));
			}
		?>
        <a href="javascript:;" class="btn green btn-circle btn-sm toggle-campaigns-filters-form"><?php echo Yii::t('list_subscribers', 'Toggle campaigns filters form');?></a>       
	</div>
    <hr style="margin:0;">
    <?php $this->renderPartial('_filters');?>
    
    
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-user"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php if($list->customer_id != 0){ ?>
				<?php echo CHtml::link(Yii::t('app', 'Create new'), array('list_subscribers/create', 'list_uid' => $list->list_uid), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'New')));?>
				<?php echo CHtml::link(Yii::t('app', 'Bulk action from source'), '#bulk-from-source-modal', array('data-toggle' => 'modal', 'class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('list_subscribers', 'Bulk action from source')));?>
				<?php } ?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('list_subscribers/index', 'list_uid' => $list->list_uid), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
			</div>
		</div>		
	</div>
	<div class="portlet-body">
		<div id="subscribers-wrapper">
			<?php $this->renderPartial('_list');?>
		</div>
	</div>
    
	
	
    <div class="modal fade" id="bulk-from-source-modal" tabindex="-1" role="dialog" aria-labelledby="bulk-from-source-label" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title"><?php echo Yii::t('list_subscribers', 'Bulk action from source');?></h4>
            </div>
            <div class="modal-body">
                <?php 
                $form = $this->beginWidget('CActiveForm', array(
                    'action'        => array('list_subscribers/bulk_from_source', 'list_uid' => $list->list_uid),
                    'htmlOptions'   => array(
                        'id'        => 'bulk-from-source-form', 
                        'enctype'   => 'multipart/form-data'
                    ),
                ));
                ?>
                <div class="alert alert-success margin-bottom-15">
                    <?php echo Yii::t('list_subscribers', 'Match the subscribers added here against the ones existing in the list and make a bulk action against them!');?>
                    <br />
                    <strong><?php echo Yii::t('list_subscribers', 'Please note, this is not the list import ability, for list import go to your list overview, followed by Tools box followed by the Import box.');?></strong>
                </div>
                    
                <div class="form-group">
                    <?php echo $form->labelEx($subBulkFromSource, 'bulk_from_file', array('class' => 'control-label'));?>
                    <?php echo $form->fileField($subBulkFromSource, 'bulk_from_file', $subBulkFromSource->getHtmlOptions('bulk_from_file')); ?>
                    <?php echo $form->error($subBulkFromSource, 'bulk_from_file');?>
                    <div class="alert alert-success margin-top-15">
                        <?php echo $subBulkFromSource->getAttributeHelpText('bulk_from_file');?>
                    </div>
                </div>
                
                <div class="form-group">
                    <?php echo $form->labelEx($subBulkFromSource, 'bulk_from_text', array('class' => 'control-label'));?>
                    <?php echo $form->textArea($subBulkFromSource, 'bulk_from_text', $subBulkFromSource->getHtmlOptions('bulk_from_text', array('rows' => 3))); ?>
                    <?php echo $form->error($subBulkFromSource, 'bulk_from_text');?>
                    <div class="alert alert-success margin-top-15">
                        <?php echo $subBulkFromSource->getAttributeHelpText('bulk_from_text');?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($subBulkFromSource, 'status', array('class' => 'control-label'));?>
                    <?php echo $form->dropDownList($subBulkFromSource, 'status', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $subBulkFromSource->getBulkActionsList()), $subBulkFromSource->getHtmlOptions('status')); ?>
                    <?php echo $form->error($subBulkFromSource, 'status');?>
                    <div class="alert alert-success margin-top-15">
                        <?php echo Yii::t('list_subscribers', 'For all the subscribers found in file/text area take this action!');?>
                    </div>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn dark btn-outline" data-dismiss="modal"><?php echo Yii::t('app', 'Close');?></button>
              <button type="button" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>" onclick="$('#bulk-from-source-form').submit();"><?php echo Yii::t('app', 'Submit');?></button>
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
