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
    
    // and render if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm', array(
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        )); 
        ?>        
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-user"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php if (!$customer->isNewRecord) { ?>
					<?php echo CHtml::link(Yii::t('app', 'Create New Merchant Customer'), array('customers/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
					<?php } ?>
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('customers/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
				</div>
			</div>
			<div class="clearfix"><!-- --></div>
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
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'first_name', array('class' => 'control-label'));?>
					<?php echo $form->textField($customer, 'first_name', $customer->getHtmlOptions('first_name')); ?>
					<?php echo $form->error($customer, 'first_name');?>
				</div>        
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'last_name', array('class' => 'control-label'));?>
					<?php echo $form->textField($customer, 'last_name', $customer->getHtmlOptions('last_name')); ?>
					<?php echo $form->error($customer, 'last_name');?>
				</div>    
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'email', array('class' => 'control-label'));?>
					<?php echo $form->textField($customer, 'email', $customer->getHtmlOptions('email')); ?>
					<?php echo $form->error($customer, 'email');?>
				</div>    
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'fake_password', array('class' => 'control-label'));?>
					<?php echo $form->passwordField($customer, 'fake_password', $customer->getHtmlOptions('password')); ?>
					<?php echo $form->error($customer, 'fake_password');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'confirm_password', array('class' => 'control-label'));?>
					<?php echo $form->passwordField($customer, 'confirm_password', $customer->getHtmlOptions('confirm_password')); ?>
					<?php echo $form->error($customer, 'confirm_password');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'mobile', array('class' => 'control-label'));?>
					<?php echo $form->textField($customer, 'mobile', $customer->getHtmlOptions('mobile')); ?>
					<?php echo $form->error($customer, 'mobile');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'phone', array('class' => 'control-label'));?>
					<?php echo $form->textField($customer, 'phone', $customer->getHtmlOptions('phone')); ?>
					<?php echo $form->error($customer, 'phone');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'cs_number', array('class' => 'control-label'));?>
					<?php echo $form->textField($customer, 'cs_number', $customer->getHtmlOptions('cs_number')); ?>
					<?php echo $form->error($customer, 'cs_number');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'gmail_email', array('class' => 'control-label'));?>
					<?php echo $form->textField($customer, 'gmail_email', $customer->getHtmlOptions('gmail_email')); ?>
					<?php echo $form->error($customer, 'gmail_email');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'timezone', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($customer, 'timezone', $customer->getTimeZonesArray(), $customer->getHtmlOptions('timezone')); ?>
					<?php echo $form->error($customer, 'timezone');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'language_id', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($customer, 'language_id', CMap::mergeArray(array('' => Yii::t('app', 'Application default')), Language::getLanguagesArray()), $customer->getHtmlOptions('language_id')); ?>
					<?php echo $form->error($customer, 'language_id');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'group_id', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($customer, 'group_id', CMap::mergeArray(array('' => ''), CustomerGroup::getGroupsArray()), $customer->getHtmlOptions('group_id')); ?>
					<?php echo $form->error($customer, 'group_id');?>
				</div>
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($customer, 'status', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($customer, 'status', $customer->getStatusesArray(), $customer->getHtmlOptions('status')); ?>
					<?php echo $form->error($customer, 'status');?>
				</div> 
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<div class="row">
						<div class="col-lg-2">
							<img src="<?php echo $customer->getAvatarUrl(90, 90);?>" class="img-thumbnail"/>
						</div>
						<div class="col-lg-10">
							<?php echo $form->labelEx($customer, 'Profile Picture', array('class' => 'control-label'));?>
							<?php echo $form->fileField($customer, 'new_avatar', $customer->getHtmlOptions('new_avatar')); ?>
							<?php echo $form->error($customer, 'Profile Picture');?>    
						</div>
					</div>
				</div>
				<div class="form-group col-lg-6">
					<div class="row">
						<div class="col-lg-2">
							<img src="<?php echo $customer->getAvatar1Url(90, 90);?>" class="img-thumbnail"/>
						</div>
						<div class="col-lg-10">
							<?php echo $form->labelEx($customer, 'Logo', array('class' => 'control-label'));?>
							<?php echo $form->fileField($customer, 'new_avatar_1', $customer->getHtmlOptions('new_avatar_1')); ?>
							<?php echo $form->error($customer, 'Logo');?>    
						</div>
					</div>
				</div>
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