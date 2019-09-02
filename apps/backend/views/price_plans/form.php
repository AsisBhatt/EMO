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
        $form = $this->beginWidget('CActiveForm'); ?>
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-credit-card"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php if (!$pricePlan->isNewRecord) { ?>
					<?php echo CHtml::link(Yii::t('app', 'Create new'), array('price_plans/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
					<?php } ?>
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('price_plans/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
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
					<?php echo $form->labelEx($pricePlan, 'name', array('class' => 'control-label'));?>
					<?php echo $form->textField($pricePlan, 'name', $pricePlan->getHtmlOptions('name')); ?>
					<?php echo $form->error($pricePlan, 'name');?>
				</div>        
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($pricePlan, 'group_id', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($pricePlan, 'group_id', CustomerGroup::getGroupsArray(), $pricePlan->getHtmlOptions('group_id')); ?>
					<?php echo $form->error($pricePlan, 'group_id');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($pricePlan, 'price', array('class' => 'control-label'));?>
					<?php echo $form->textField($pricePlan, 'price', $pricePlan->getHtmlOptions('price')); ?>
					<?php echo $form->error($pricePlan, 'price');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-12">
					<?php echo $form->labelEx($pricePlan, 'description', array('class' => 'control-label'));?>
					<?php echo $form->textArea($pricePlan, 'description', $pricePlan->getHtmlOptions('description')); ?>
					<?php echo $form->error($pricePlan, 'description');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($pricePlan, 'status', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($pricePlan, 'status', $pricePlan->getStatusesList(), $pricePlan->getHtmlOptions('status')); ?>
					<?php echo $form->error($pricePlan, 'status');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($pricePlan, 'visible', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($pricePlan, 'visible', $pricePlan->getYesNoOptions(), $pricePlan->getHtmlOptions('visible')); ?>
					<?php echo $form->error($pricePlan, 'visible');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($pricePlan, 'sort_order', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($pricePlan, 'sort_order', $pricePlan->getSortOrderList(), $pricePlan->getHtmlOptions('sort_order')); ?>
					<?php echo $form->error($pricePlan, 'sort_order');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($pricePlan, 'recommended', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($pricePlan, 'recommended', $pricePlan->getYesNoOptions(), $pricePlan->getHtmlOptions('recommended')); ?>
					<?php echo $form->error($pricePlan, 'recommended');?>
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