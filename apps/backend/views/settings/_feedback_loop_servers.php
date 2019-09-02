<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */
 
?>
    <div class="portlet-title">
		<div class="caption">
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('settings', 'Settings for processing feedback loop servers')?>
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
            'controller'        => $this,
            'form'              => $form    
        )));
        ?>
        <div class="row">
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronFeedbackModel, 'memory_limit', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($cronFeedbackModel, 'memory_limit', $cronFeedbackModel->getMemoryLimitOptions(), $cronFeedbackModel->getHtmlOptions('memory_limit', array('data-placement' => 'right'))); ?>
				<?php echo $form->error($cronFeedbackModel, 'memory_limit');?>
			</div>    
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronFeedbackModel, 'servers_at_once', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronFeedbackModel, 'servers_at_once', $cronFeedbackModel->getHtmlOptions('servers_at_once')); ?>
				<?php echo $form->error($cronFeedbackModel, 'servers_at_once');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronFeedbackModel, 'emails_at_once', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronFeedbackModel, 'emails_at_once', $cronFeedbackModel->getHtmlOptions('emails_at_once')); ?>
				<?php echo $form->error($cronFeedbackModel, 'emails_at_once');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronFeedbackModel, 'pause', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronFeedbackModel, 'pause', $cronFeedbackModel->getHtmlOptions('pause')); ?>
				<?php echo $form->error($cronFeedbackModel, 'pause');?>
			</div> 
		</div>
		<div class="row">
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronFeedbackModel, 'subscriber_action', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($cronFeedbackModel, 'subscriber_action', $cronFeedbackModel->getSubscriberActionOptions(), $cronFeedbackModel->getHtmlOptions('subscriber_action', array('data-placement' => 'left'))); ?>
				<?php echo $form->error($cronFeedbackModel, 'subscriber_action');?>
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
            'controller'        => $this,
            'form'              => $form    
        )));
        ?>
    </div>