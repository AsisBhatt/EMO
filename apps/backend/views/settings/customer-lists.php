<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.4
 */

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false 
 * in order to stop rendering the default content.
 * @since 1.3.4.3
 */
$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) {
    $this->renderPartial('_customers_tabs');
    /**
     * This hook gives a chance to prepend content before the active form or to replace the default active form entirely.
     * Please note that from inside the action callback you can access all the controller view variables 
     * via {@CAttributeCollection $collection->controller->data}
     * In case the form is replaced, make sure to set {@CAttributeCollection $collection->renderForm} to false 
     * in order to stop rendering the default content.
     * @since 1.3.4.3
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
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo Yii::t('settings', 'Customer lists')?>
				</span>
			</div>
		</div>
		<div class="portlet-body">
			<?php 
			/**
			 * This hook gives a chance to prepend content before the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables 
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.4.3
			 */
			$hooks->doAction('before_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form    
			)));
			?>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_import_subscribers', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_import_subscribers', $model->getYesNoOptions(), $model->getHtmlOptions('can_import_subscribers')); ?>
					<?php echo $form->error($model, 'can_import_subscribers');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_export_subscribers', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_export_subscribers', $model->getYesNoOptions(), $model->getHtmlOptions('can_export_subscribers')); ?>
					<?php echo $form->error($model, 'can_export_subscribers');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_copy_subscribers', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_copy_subscribers', $model->getYesNoOptions(), $model->getHtmlOptions('can_copy_subscribers')); ?>
					<?php echo $form->error($model, 'can_copy_subscribers');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'max_lists', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_lists', $model->getHtmlOptions('max_lists')); ?>
					<?php echo $form->error($model, 'max_lists');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'max_subscribers', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_subscribers', $model->getHtmlOptions('max_subscribers')); ?>
					<?php echo $form->error($model, 'max_subscribers');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'max_subscribers_per_list', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_subscribers_per_list', $model->getHtmlOptions('max_subscribers_per_list')); ?>
					<?php echo $form->error($model, 'max_subscribers_per_list');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'copy_subscribers_memory_limit', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'copy_subscribers_memory_limit', $model->getMemoryLimitOptions(), $model->getHtmlOptions('copy_subscribers_memory_limit')); ?>
					<?php echo $form->error($model, 'copy_subscribers_memory_limit');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'copy_subscribers_at_once', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'copy_subscribers_at_once', $model->getHtmlOptions('copy_subscribers_at_once')); ?>
					<?php echo $form->error($model, 'copy_subscribers_at_once');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_delete_own_lists', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_delete_own_lists', $model->getYesNoOptions(), $model->getHtmlOptions('can_delete_own_lists')); ?>
					<?php echo $form->error($model, 'can_delete_own_lists');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_delete_own_subscribers', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_delete_own_subscribers', $model->getYesNoOptions(), $model->getHtmlOptions('can_delete_own_subscribers')); ?>
					<?php echo $form->error($model, 'can_delete_own_subscribers');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_segment_lists', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_segment_lists', $model->getYesNoOptions(), $model->getHtmlOptions('can_segment_lists')); ?>
					<?php echo $form->error($model, 'can_segment_lists');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'max_segment_conditions', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_segment_conditions', $model->getHtmlOptions('max_segment_conditions')); ?>
					<?php echo $form->error($model, 'max_segment_conditions');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'max_segment_wait_timeout', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_segment_wait_timeout', $model->getHtmlOptions('max_segment_wait_timeout')); ?>
					<?php echo $form->error($model, 'max_segment_wait_timeout');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_mark_blacklisted_as_confirmed', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_mark_blacklisted_as_confirmed', $model->getYesNoOptions(), $model->getHtmlOptions('can_mark_blacklisted_as_confirmed')); ?>
					<?php echo $form->error($model, 'can_mark_blacklisted_as_confirmed');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_use_own_blacklist', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_use_own_blacklist', $model->getYesNoOptions(), $model->getHtmlOptions('can_use_own_blacklist')); ?>
					<?php echo $form->error($model, 'can_use_own_blacklist');?>
				</div>
			</div>
			<?php 
			/**
			 * This hook gives a chance to append content after the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables 
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.4.3
			 */
			$hooks->doAction('after_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form    
			)));
			?>
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
     * @since 1.3.4.3
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
 * @since 1.3.4.3
 */
$hooks->doAction('after_view_file_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));