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

?>
    <div class="portlet-title">
        <div class="caption">
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('settings', 'Delivery settings')?>
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
				<?php echo $form->labelEx($cronDeliveryModel, 'memory_limit', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($cronDeliveryModel, 'memory_limit', $cronDeliveryModel->getMemoryLimitOptions(), $cronDeliveryModel->getHtmlOptions('memory_limit', array('data-placement' => 'right'))); ?>
				<?php echo $form->error($cronDeliveryModel, 'memory_limit');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'campaigns_at_once', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronDeliveryModel, 'campaigns_at_once', $cronDeliveryModel->getHtmlOptions('campaigns_at_once')); ?>
				<?php echo $form->error($cronDeliveryModel, 'campaigns_at_once');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'subscribers_at_once', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronDeliveryModel, 'subscribers_at_once', $cronDeliveryModel->getHtmlOptions('subscribers_at_once')); ?>
				<?php echo $form->error($cronDeliveryModel, 'subscribers_at_once');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'send_at_once', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronDeliveryModel, 'send_at_once', $cronDeliveryModel->getHtmlOptions('send_at_once')); ?>
				<?php echo $form->error($cronDeliveryModel, 'send_at_once');?>
			</div>
		</div>
		<div class="row">	
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'pause', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronDeliveryModel, 'pause', $cronDeliveryModel->getHtmlOptions('pause')); ?>
				<?php echo $form->error($cronDeliveryModel, 'pause');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'emails_per_minute', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronDeliveryModel, 'emails_per_minute', $cronDeliveryModel->getHtmlOptions('emails_per_minute')); ?>
				<?php echo $form->error($cronDeliveryModel, 'emails_per_minute');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'change_server_at', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronDeliveryModel, 'change_server_at', $cronDeliveryModel->getHtmlOptions('change_server_at')); ?>
				<?php echo $form->error($cronDeliveryModel, 'change_server_at');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'max_bounce_rate', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronDeliveryModel, 'max_bounce_rate', $cronDeliveryModel->getHtmlOptions('max_bounce_rate')); ?>
				<?php echo $form->error($cronDeliveryModel, 'max_bounce_rate');?>
			</div>
		</div>

        <div class="alert alert-success margin-bottom-15">
            <?php echo Yii::t('settings', 'You can use below settings to increase the delivery speed. Please be aware that wrong changes might have undesired results.');?>
            <br />
            <strong><?php echo Yii::t('settings', 'Also note that below will apply only if you have installed and enabled PHP\'s PCNTL extension on your server. If you are not sure if your server has the extension, ask your hosting.');?></strong>
        </div>
		
		<div class="row">
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'use_pcntl', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($cronDeliveryModel, 'use_pcntl', $cronDeliveryModel->getYesNoOptions(), $cronDeliveryModel->getHtmlOptions('use_pcntl')); ?>
				<?php echo $form->error($cronDeliveryModel, 'use_pcntl');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'campaigns_in_parallel', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronDeliveryModel, 'campaigns_in_parallel', $cronDeliveryModel->getHtmlOptions('campaigns_in_parallel')); ?>
				<?php echo $form->error($cronDeliveryModel, 'campaigns_in_parallel');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronDeliveryModel, 'subscriber_batches_in_parallel', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronDeliveryModel, 'subscriber_batches_in_parallel', $cronDeliveryModel->getHtmlOptions('subscriber_batches_in_parallel')); ?>
				<?php echo $form->error($cronDeliveryModel, 'subscriber_batches_in_parallel');?>
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
