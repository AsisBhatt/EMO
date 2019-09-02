<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.7
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
        $form = $this->beginWidget('CActiveForm'); 
        ?>
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-send"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
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
			<div class="form-group">
				<?php echo $form->labelEx($model, 'subject', array('class' => 'control-label'));?>
				<?php echo $form->textField($model, 'subject', $model->getHtmlOptions('subject')); ?>
				<?php echo $form->error($model, 'subject');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model, 'message', array('class' => 'control-label'));?>
				<?php echo $form->textArea($model, 'message', $model->getHtmlOptions('message', array('rows' => 15))); ?>
				<?php echo $form->error($model, 'message');?>
				<div class="alert alert-success">
					<?php echo Yii::t('customers', 'Following tags are available for message but also for subject: {tags}', array(
						'{tags}' => '
							<span class="btn btn-sm green">[FULL_NAME]</span> 
							<span class="btn btn-sm green">[FIRST_NAME]</span> 
							<span class="btn btn-sm green">[LAST_NAME]</span>
							<span class="btn btn-sm green">[EMAIL]</span>
						',
					));?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($model, 'groups', array('class' => 'control-label'));?>
					<div class="form-group">		 
						<?php echo CHtml::checkBoxList($model->modelName.'[groups]', $model->groups, $model->getGroupsList(), $model->getHtmlOptions('groups', array(
							'class'        => '',
							'template'     => '{beginLabel}{input} {labelTitle} <span></span>{endLabel}',
							'container'    => '',
							'separator'    => '',
							'labelOptions' => array('class' => 'mt-checkbox mt-checkbox-outline')	
						))); ?> 
					</div>
					<?php echo $form->error($model, 'group');?>
					<div class="alert alert-success">
						<?php echo Yii::t('customers', 'If no group is selected, all customers will receive the email message.');?>
					</div>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($model, 'batch_size', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'batch_size', $model->getBatchSizes(), $model->getHtmlOptions('batch_size')); ?>
					<?php echo $form->error($model, 'batch_size');?>
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
					<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('customers', 'Send message');?></button>
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