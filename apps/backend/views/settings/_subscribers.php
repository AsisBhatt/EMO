<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.2
 */
 
?>
	<div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('settings', 'Subscribers settings')?>
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
			'controller'            => $this,
			'form'                  => $form    
		)));
		?>
		<div class="row">
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronSubscribersModel, 'memory_limit', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($cronSubscribersModel, 'memory_limit', $cronSubscribersModel->getMemoryLimitOptions(), $cronSubscribersModel->getHtmlOptions('memory_limit', array('data-placement' => 'right'))); ?>
				<?php echo $form->error($cronSubscribersModel, 'memory_limit');?>
			</div>    
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronSubscribersModel, 'unsubscribe_days', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronSubscribersModel, 'unsubscribe_days', $cronSubscribersModel->getHtmlOptions('unsubscribe_days')); ?>
				<?php echo $form->error($cronSubscribersModel, 'unsubscribe_days');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronSubscribersModel, 'unconfirm_days', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronSubscribersModel, 'unconfirm_days', $cronSubscribersModel->getHtmlOptions('unconfirm_days')); ?>
				<?php echo $form->error($cronSubscribersModel, 'unconfirm_days');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronSubscribersModel, 'blacklisted_days', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronSubscribersModel, 'blacklisted_days', $cronSubscribersModel->getHtmlOptions('blacklisted_days')); ?>
				<?php echo $form->error($cronSubscribersModel, 'blacklisted_days');?>
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
			'controller'            => $this,
			'form'                  => $form    
		)));
		?>
	</div>