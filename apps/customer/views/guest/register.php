<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
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
    $timezoneIdentifiers = DateTimeZone::listIdentifiers();
	
    // and render if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm');
        ?>
		
		<h3 class="form-title"><?php echo $pageHeading;?></h3>
		<div class="box-body">
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
			<div class="form-group">
				<?php echo $form->labelEx($model, 'first_name', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($model, 'first_name', $model->getHtmlOptions('first_name')); ?>
				<?php echo $form->error($model, 'first_name');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model, 'last_name', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($model, 'last_name', $model->getHtmlOptions('last_name')); ?>
				<?php echo $form->error($model, 'last_name');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model, 'mobile', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($model, 'mobile', $model->getHtmlOptions('mobile')); ?>
				<?php echo $form->error($model, 'mobile');?>
			</div>			
			<div class="form-group">
				<?php echo $form->labelEx($model, 'email', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($model, 'email', $model->getHtmlOptions('email')); ?>
				<?php echo $form->error($model, 'email');?>
			</div>
			<div class="form-group">
				<?php //echo $form->labelEx($model, 'confirm_email', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php //echo $form->textField($model, 'confirm_email', $model->getHtmlOptions('confirm_email')); ?>
				<?php //echo $form->error($model, 'confirm_email');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model, 'fake_password', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->passwordField($model, 'fake_password', $model->getHtmlOptions('fake_password')); ?>
				<?php echo $form->error($model, 'fake_password');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model, 'confirm_password', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->passwordField($model, 'confirm_password', $model->getHtmlOptions('confirm_password')); ?>
				<?php echo $form->error($model, 'confirm_password');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($model, 'timezone', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->dropDownList($model, 'timezone', $model->getTimeZonesArray(), $model->getHtmlOptions('timezone')); ?>
				<?php echo $form->error($model, 'timezone');?>
			</div>
			<?php if($companyRequired) { ?>
			<h4><?php echo Yii::t('customers', 'Company info');?></h4>
			
			<div class="form-group">
				<?php echo $form->labelEx($company, 'name', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($company, 'name', $company->getHtmlOptions('name')); ?>
				<?php echo $form->error($company, 'name');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($company, 'country_id', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $company->getCountriesDropDown(array(
					'data-zones-by-country-url' => Yii::app()->createUrl('guest/zones_by_country'),
				)); ?>
				<?php echo $form->error($company, 'country_id');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($company, 'address_1', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($company, 'address_1', $company->getHtmlOptions('address_1')); ?>
				<?php echo $form->error($company, 'address_1');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($company, 'zone_id', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $company->getZonesDropDown(); ?>
				<?php echo $form->error($company, 'zone_id');?>
			</div> 
			<div class="form-group">
				<?php echo $form->labelEx($company, 'city', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($company, 'city', $company->getHtmlOptions('city')); ?>
				<?php echo $form->error($company, 'city');?>
			</div> 
			<div class="form-group">
				<?php echo $form->labelEx($company, 'zip_code', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($company, 'zip_code', $company->getHtmlOptions('zip_code')); ?>
				<?php echo $form->error($company, 'zip_code');?>
			</div> 
			<div class="form-group">
				<?php echo $form->labelEx($company, 'phone', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($company, 'phone', $company->getHtmlOptions('phone')); ?>
				<?php echo $form->error($company, 'phone');?>
			</div> 
			<div class="form-group">
				<?php echo $form->labelEx($company, 'vat_number', array('class' => 'control-label visible-ie8 visible-ie9'));?>
				<?php echo $form->textField($company, 'vat_number', $company->getHtmlOptions('vat_number')); ?>
				<?php echo $form->error($company, 'vat_number');?>
			</div> 
			
			<?php } ?>			
			<div class="form-group">
				<label class="check mt-checkbox mt-checkbox-outline">
					<?php echo $form->checkBox($model, 'tc_agree', $model->getHtmlOptions('tc_agree', array('class' => '', 'uncheckValue' => null))); ?>
					<?php echo Yii::t('customers', 'I agree with the specified {terms}', array(
						'{terms}' => CHtml::link(Yii::t('customers', 'Terms and conditions'), Yii::app()->options->get('system.customer_registration.tc_url', 'javascript:;'), array('target' => '_blank')),
					))?>
					<span></span>
				</label>
				<?php echo $form->error($model, 'tc_agree');?>
			</div>
			<?php 
			/**
			 * This hook gives a chance to append content after the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables 
			 * via {@CAttributeCollection $collection->controller->data}
			 * 
			 * @since 1.3.4.3
			 */
			$hooks->doAction('after_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form    
			))); 
			?>
		</div>
		<div class="form-actions form-footer">
			<a href="<?php echo $this->createUrl('guest/index')?>" class="btn green btn-outline btn-sm"><?php echo Yii::t('app', 'GO TO LOGIN');?></a>
			<button type="submit" class="btn green uppercase pull-right btn-submit btn-sm" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Register');?></button>
		</div>
		
        <?php if (!empty($facebookEnabled) || !empty($twitterEnabled)) { ?>
        <div class="box box-success">
            <div class="box-body">
                <div class="clearfix"><!-- --></div>
                <div class="pull-left">
                    <?php if (!empty($facebookEnabled)) { ?>
                        <a href="<?php echo $this->createUrl('guest/facebook')?>" class="btn btn-success btn-flat btn-facebook"><i class="fa fa-facebook-square"></i> <?php echo Yii::t('app', 'Register with Facebook');?></a>
                    <?php } ?>
                    <?php if (!empty($twitterEnabled)) { ?>
                        <a href="<?php echo $this->createUrl('guest/twitter')?>" class="btn btn-success btn-flat btn-twitter"><i class="fa fa-twitter-square"></i> <?php echo Yii::t('app', 'Register with Twitter');?></a>
                    <?php } ?>
                </div>
                <div class="clearfix"><!-- --></div>
            </div>
        </div>
        <?php } ?>
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