<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4
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
    $this->renderPartial('_customers_tabs');
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
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo Yii::t('settings', 'Customer servers')?>
				</span>
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
					<?php echo $form->labelEx($model, 'max_delivery_servers', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_delivery_servers', $model->getHtmlOptions('max_delivery_servers')); ?>
					<?php echo $form->error($model, 'max_delivery_servers');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($model, 'max_bounce_servers', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_bounce_servers', $model->getHtmlOptions('max_bounce_servers')); ?>
					<?php echo $form->error($model, 'max_bounce_servers');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($model, 'max_fbl_servers', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_fbl_servers', $model->getHtmlOptions('max_fbl_servers')); ?>
					<?php echo $form->error($model, 'max_fbl_servers');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($model, 'must_add_bounce_server', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'must_add_bounce_server', $model->getYesNoOptions(), $model->getHtmlOptions('must_add_bounce_server')); ?>
					<?php echo $form->error($model, 'must_add_bounce_server');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($model, 'can_select_delivery_servers_for_campaign', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_select_delivery_servers_for_campaign', $model->getYesNoOptions(), $model->getHtmlOptions('can_select_delivery_servers_for_campaign')); ?>
					<?php echo $form->error($model, 'can_select_delivery_servers_for_campaign');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($model, 'can_send_from_system_servers', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_send_from_system_servers', $model->getYesNoOptions(), $model->getHtmlOptions('can_send_from_system_servers')); ?>
					<?php echo $form->error($model, 'can_send_from_system_servers');?>
				</div>   
			</div>
			<div class="row">
				<div class="form-group col-lg-12">
					<label class="control-label"><?php echo Yii::t('settings', 'Custom headers');?></label>
					<?php echo $form->textArea($model, 'custom_headers', $model->getHtmlOptions('custom_headers', array('rows' => 5))); ?>
				</div>
			</div>
			
			<div class="portlet-title">
				<div class="caption">
					<span class="caption-subject font-dark sbold uppercase">
						<?php echo Yii::t('settings', 'Allowed server types');?>:
					</span>
				</div>
				<?php echo $form->error($model, 'allowed_server_types');?>					
			</div>
			<div class="row">
				<?php foreach ($model->getServerTypesList() as $type => $name) { ?>
				<div class="form-group col-lg-6">
					<div class="row">
						<div class="col-lg-8">
							<?php echo CHtml::label(Yii::t('settings', 'Server type'), '_dummy_' , array('class' => 'control-label'));?>
							<?php echo CHtml::textField('_dummy_', $name, $model->getHtmlOptions('allowed_server_types', array('readonly' => true)));?>
						</div>
						<div class="col-lg-4">
							<?php echo CHtml::label(Yii::t('settings', 'Allowed'), '_dummy_', array('class' => 'control-label'));?>
							<?php echo CHtml::dropDownList($model->modelName . '[allowed_server_types]['.$type.']', in_array($type, $model->allowed_server_types) ? 'yes' : 'no', $model->getYesNoOptions(), $model->getHtmlOptions('allowed_server_types'));?>
						</div>
					</div>
				</div>
				<?php } ?>
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