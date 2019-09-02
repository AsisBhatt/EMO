<?php defined('MW_PATH') || exit('No direct script access allowed');
 //Yii:: app()->cache->flush();exit;

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
    <div class="tabs-container">
    <?php 
    echo $this->renderTabs();
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
    
    // and render only if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm'); 
        ?>
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
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'name');?>
						<?php echo $form->textField($company, 'name', $company->getHtmlOptions('name')); ?>
						<?php echo $form->error($company, 'name');?>
					</div>    
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'website');?>
						<?php echo $form->textField($company, 'website', $company->getHtmlOptions('website')); ?>
						<?php echo $form->error($company, 'website');?>
					</div>    
				</div>
			</div>
		</div>
		<div class="portlet-title">
			<div class="caption">
				<span class="caption-subject font-dark sbold uppercase">
					Customer Merchant Address
				</span>
			</div>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'address_1');?>
						<?php echo $form->textField($company, 'address_1', $company->getHtmlOptions('address_1')); ?>
						<?php echo $form->error($company, 'address_1');?>
					</div>    
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'address_2');?>
						<?php echo $form->textField($company, 'address_2', $company->getHtmlOptions('address_2')); ?>
						<?php echo $form->error($company, 'address_2');?>
					</div>    
				</div>
				<div class="col-lg-6 city-wrap">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'city');?>
						<?php echo $form->textField($company, 'city', $company->getHtmlOptions('city')); ?>
						<?php echo $form->error($company, 'city');?>
					</div>    
				</div>
				<div class="col-lg-6 zip-wrap">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'zip_code');?>
						<?php echo $form->textField($company, 'zip_code', $company->getHtmlOptions('zip_code')); ?>
						<?php echo $form->error($company, 'zip_code');?>
					</div>    
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'country_id');?>
						<?php echo $company->getCountriesDropDown(); ?>
						<?php echo $form->error($company, 'country_id');?>
					</div>    
				</div>
				<div class="col-lg-6 zone-name-wrap">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'zone_name');?>
						<?php echo $form->textField($company, 'zone_name', $company->getHtmlOptions('zone_name')); ?>
						<?php echo $form->error($company, 'zone_name');?>
					</div>    
				</div>					
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'zone_id');?>
						<?php echo $company->getZonesDropDown(); ?>
						<?php echo $form->error($company, 'zone_id');?>
					</div>    
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'zone'); ?>
						<?php echo $form->textField($company, 'zone', $company->getHtmlOptions('zone')); ?>
						<?php echo $form->error($company, 'zone'); ?>
					</div>
				</div>				
			</div>
		</div>
		<div class="portlet-title">
			<div class="caption">
				<span class="caption-subject font-dark sbold uppercase">
					Credit Card Billing Address
				</span>
			</div>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'phone');?>
						<?php echo $form->textField($company, 'phone', $company->getHtmlOptions('phone')); ?>
						<?php echo $form->error($company, 'phone');?>
					</div>    
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'fax');?>
						<?php echo $form->textField($company, 'fax', $company->getHtmlOptions('fax')); ?>
						<?php echo $form->error($company, 'fax');?>
					</div>    
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'type_id');?>
						<?php echo $form->dropDownList($company, 'type_id', CMap::mergeArray(array('' => Yii::t('app', 'Please select')), CompanyType::getListForDropDown()), $company->getHtmlOptions('type_id')); ?>
						<?php echo $form->error($company, 'type_id');?>
					</div>    
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'vat_number');?>
						<?php echo $form->textField($company, 'vat_number', $company->getHtmlOptions('vat_number')); ?>
						<?php echo $form->error($company, 'vat_number');?>
					</div>    
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'portal_customer');?>
						<?php echo $form->dropDownList($company, 'portal_customer', CMap::mergeArray(array('' => Yii::t('app', 'Please select')), array('EDATA' => 'eData', 'BEELIFT' => 'BeeLift')), $company->getHtmlOptions('portal_customer')); ?>
						<?php echo $form->error($company, 'portal_customer');?>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'flowroute_sms_num');?>
						<?php echo $form->textField($company, 'flowroute_sms_num', $company->getHtmlOptions('flowroute_sms_num')); ?>
						<?php echo $form->error($company, 'flowroute_sms_num');?>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_street_address');?>
						<?php echo $form->textField($company, 'credit_street_address', $company->getHtmlOptions('credit_street_address')); ?>
						<?php echo $form->error($company, 'credit_street_address');?>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_zip');?>
						<?php echo $form->textField($company, 'credit_zip', $company->getHtmlOptions('credit_zip')); ?>
						<?php echo $form->error($company, 'credit_zip');?>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_city');?>
						<?php echo $form->textField($company, 'credit_city', $company->getHtmlOptions('credit_city')); ?>
						<?php echo $form->error($company, 'credit_city');?>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_state');?>
						<?php echo $form->textField($company, 'credit_state', $company->getHtmlOptions('credit_state')); ?>
						<?php echo $form->error($company, 'credit_state');?>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_county');?>
						<?php echo $form->textField($company, 'credit_county', $company->getHtmlOptions('credit_county')); ?>
						<?php echo $form->error($company, 'credit_county');?>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_country');?>
						<?php echo $form->textField($company, 'credit_country', $company->getHtmlOptions('credit_country')); ?>
						<?php echo $form->error($company, 'credit_country');?>
					</div>
				</div>
			</div>
		</div>
		<div class="portlet-title">
			<div class="caption">
				<span class="caption-subject font-dark sbold uppercase">
					Credit card info
				</span>
			</div>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-lg-3">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_card_number', array('class' => 'control-label'));?>
						<?php echo $form->textField($company, 'credit_card_number', $company->getHtmlOptions('credit_card_number')); ?>
						<?php echo $form->error($company, 'credit_card_number');?>
					</div>
				</div>
				<div class="clearfix"><!-- --></div>
				<div class="col-lg-1">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_card_month', array('class' => 'control-label'));?>
						<?php echo $form->textField($company, 'credit_card_month', $company->getHtmlOptions('credit_card_month')); ?>
						<?php echo $form->error($company, 'credit_card_month');?>
					</div>
				</div>
				<div class="col-lg-1">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_card_year', array('class' => 'control-label'));?>
						<?php echo $form->textField($company, 'credit_card_year', $company->getHtmlOptions('credit_card_year')); ?>
						<?php echo $form->error($company, 'credit_card_year');?>
					</div>
				</div>
				<div class="clearfix"><!-- --></div>
				<div class="col-lg-1">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'credit_card_cvv', array('class' => 'control-label'));?>
						<?php echo $form->textField($company, 'credit_card_cvv', $company->getHtmlOptions('credit_card_cvv')); ?>
						<?php echo $form->error($company, 'credit_card_cvv');?>
					</div>
				</div>				
			</div>
		</div>
		<div class="portlet-body">
			<div class="row">
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'acquisition_source');?>
						<?php echo $form->textArea($company, 'acquisition_source', $company->getHtmlOptions('acquisition_source')); ?>
						<?php echo $form->error($company, 'acquisition_source');?>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<?php echo $form->labelEx($company, 'comments');?>
						<?php echo $form->textArea($company, 'comments', $company->getHtmlOptions('comments')); ?>
						<?php echo $form->error($company, 'comments');?>
					</div>
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
    ?>
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