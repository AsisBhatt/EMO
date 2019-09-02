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
        <div class="box box-primary">
            <div class="box-header">
                <div class="pull-left">
                    <h3 class="box-title"><span class="glyphicon glyphicon-credit-card"></span> <?php echo $pageHeading;?></h3>
                </div>
                <div class="pull-right">
                    <?php if (!$plan->isNewRecord) { ?>
                    <?php echo CHtml::link(Yii::t('app', 'Create new'), array('plan/create'), array('class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Create new')));?>
                    <?php } ?>
                    <?php echo CHtml::link(Yii::t('app', 'Cancel'), array('plan/index'), array('class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Cancel')));?>
                </div>
                <div class="clearfix"><!-- --></div>
            </div>
            <div class="box-body">
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
               
				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($plan, 'name');?>
                    <?php echo $form->textField($plan, 'name', $plan->getHtmlOptions('name')); ?>
                    <?php echo $form->error($plan, 'name');?>
                </div> 
				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($plan, 'sms_total');?>
                    <?php echo $form->textField($plan, 'sms_total', $plan->getHtmlOptions('sms_total')); ?>
                    <?php echo $form->error($plan, 'sms_total');?>
                </div>  
				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($plan, 'email_total');?>
                    <?php echo $form->textField($plan, 'email_total', $plan->getHtmlOptions('email_total')); ?>
                    <?php echo $form->error($plan, 'email_total');?>
                </div>  
				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($plan, 'validity');?>
                    <?php echo $form->textField($plan, 'validity', $plan->getHtmlOptions('validity')); ?>
                    <?php echo $form->error($plan, 'validity');?>
                </div>        
                
                <div class="form-group col-lg-4">
                    <?php echo $form->labelEx($plan, 'price');?>
                    <?php echo $form->textField($plan, 'price', $plan->getHtmlOptions('price')); ?>
                    <?php echo $form->error($plan, 'price');?>
                </div>    
                <div class="clearfix"><!-- --></div>
                <div class="form-group col-lg-12">
                    <?php echo $form->labelEx($plan, 'description');?>
                    <?php echo $form->textArea($plan, 'description', $plan->getHtmlOptions('description')); ?>
                    <?php echo $form->error($plan, 'description');?>
                </div>      
                <div class="clearfix"><!-- --></div>
                <div class="form-group col-lg-3">
                    <?php echo $form->labelEx($plan, 'status');?>
                    <?php echo $form->dropDownList($plan, 'status', $plan->getStatusesList(), $plan->getHtmlOptions('status')); ?>
                    <?php echo $form->error($plan, 'status');?>
                </div>
                
				
				
                <div class="clearfix"><!-- --></div>   
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
                <div class="clearfix"><!-- --></div>    
            </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
                </div>
                <div class="clearfix"><!-- --></div>
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