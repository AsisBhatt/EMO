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
 * @since 1.3.3.1
 */
$hooks->doAction('views_before_content', $viewCollection = new CAttributeCollection(array(
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
    $hooks->doAction('views_before_form', $collection = new CAttributeCollection(array(
        'controller'    => $this,
        'renderForm'    => true,
    )));
    
    // and render if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm');  
        ?>
        
		<div class="portlet-title">
			<div class="caption">
				<i class="fa fa-code"></i>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php 
					if (!$promoCode->isNewRecord) { 
						echo CHtml::link(Yii::t('app', 'Create new'), array('promo_codes/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new'))) . ' ';
					} 
					echo CHtml::link(Yii::t('app', 'Cancel'), array('promo_codes/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));
					?>
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
			$hooks->doAction('views_before_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form    
			)));
			?>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($promoCode, 'code', array('class' => 'control-label'));?>
					<?php echo $form->textField($promoCode, 'code', $promoCode->getHtmlOptions('code')); ?>
					<?php echo $form->error($promoCode, 'code');?>
				</div>  
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($promoCode, 'type', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($promoCode, 'type', $promoCode->getTypesList(), $promoCode->getHtmlOptions('type')); ?>
					<?php echo $form->error($promoCode, 'type');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($promoCode, 'discount', array('class' => 'control-label'));?>
					<?php echo $form->textField($promoCode, 'discount', $promoCode->getHtmlOptions('discount')); ?>
					<?php echo $form->error($promoCode, 'discount');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($promoCode, 'total_amount', array('class' => 'control-label'));?>
					<?php echo $form->textField($promoCode, 'total_amount', $promoCode->getHtmlOptions('total_amount')); ?>
					<?php echo $form->error($promoCode, 'total_amount');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($promoCode, 'total_usage', array('class' => 'control-label'));?>
					<?php echo $form->textField($promoCode, 'total_usage', $promoCode->getHtmlOptions('total_usage')); ?>
					<?php echo $form->error($promoCode, 'total_usage');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($promoCode, 'customer_usage', array('class' => 'control-label'));?>
					<?php echo $form->textField($promoCode, 'customer_usage', $promoCode->getHtmlOptions('customer_usage')); ?>
					<?php echo $form->error($promoCode, 'customer_usage');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($promoCode, 'date_start', array('class' => 'control-label'));?>
					<?php 
					$this->widget('zii.widgets.jui.CJuiDatePicker',array(
						'model'     => $promoCode,
						'attribute' => 'date_start',
						'language'  => $promoCode->getDatePickerLanguage(),
						'cssFile'   => null,
						'options'   => array(
							'showAnim'      => 'fold',
							'dateFormat'    => $promoCode->getDatePickerFormat(),
						),
						'htmlOptions'=>$promoCode->getHtmlOptions('date_start'),
					));
					?>
					<?php echo $form->error($promoCode, 'date_start');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($promoCode, 'date_end', array('class' => 'control-label'));?>
					<?php 
					$this->widget('zii.widgets.jui.CJuiDatePicker',array(
						'model'     => $promoCode,
						'attribute' => 'date_end',
						'language'  => $promoCode->getDatePickerLanguage(),
						'cssFile'   => null,
						'options'   => array(
							'showAnim'      => 'fold',
							'dateFormat'    => $promoCode->getDatePickerFormat(),
						),
						'htmlOptions'=>$promoCode->getHtmlOptions('date_end'),
					));
					?>
					<?php echo $form->error($promoCode, 'date_end');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($promoCode, 'status', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($promoCode, 'status', $promoCode->getStatusesList(), $promoCode->getHtmlOptions('status')); ?>
					<?php echo $form->error($promoCode, 'status');?>
				</div>
			</div>
			<?php 
			/**
			 * This hook gives a chance to append content after the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables 
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.3.1
			 */
			$hooks->doAction('views_after_form_fields', new CAttributeCollection(array(
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
    $hooks->doAction('views_after_form', new CAttributeCollection(array(
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
$hooks->doAction('views_after_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));