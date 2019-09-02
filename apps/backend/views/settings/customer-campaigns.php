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
 * @since 1.3.4.3
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
     * @since 1.3.4.3
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
					<?php echo Yii::t('settings', 'Customer campaigns')?>
				</span>
			</div>
		</div>
		<div class="portlet-body">
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
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'max_campaigns', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_campaigns', $model->getHtmlOptions('max_campaigns')); ?>
					<?php echo $form->error($model, 'max_campaigns');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_delete_own_campaigns', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_delete_own_campaigns', $model->getYesNoOptions(), $model->getHtmlOptions('can_delete_own_campaigns')); ?>
					<?php echo $form->error($model, 'can_delete_own_campaigns');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'send_to_multiple_lists', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'send_to_multiple_lists', $model->getYesNoOptions(), $model->getHtmlOptions('send_to_multiple_lists')); ?>
					<?php echo $form->error($model, 'send_to_multiple_lists');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'must_verify_sending_domain', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'must_verify_sending_domain', $model->getYesNoOptions(), $model->getHtmlOptions('must_verify_sending_domain')); ?>
					<?php echo $form->error($model, 'must_verify_sending_domain');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'can_export_stats', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($model, 'can_export_stats', $model->getYesNoOptions(), $model->getHtmlOptions('can_export_stats')); ?>
					<?php echo $form->error($model, 'can_export_stats');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'subscribers_at_once', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'subscribers_at_once', $model->getHtmlOptions('subscribers_at_once')); ?>
					<?php echo $form->error($model, 'subscribers_at_once');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'send_at_once', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'send_at_once', $model->getHtmlOptions('send_at_once')); ?>
					<?php echo $form->error($model, 'send_at_once');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'pause', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'pause', $model->getHtmlOptions('pause')); ?>
					<?php echo $form->error($model, 'pause');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'emails_per_minute', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'emails_per_minute', $model->getHtmlOptions('emails_per_minute')); ?>
					<?php echo $form->error($model, 'emails_per_minute');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'change_server_at', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'change_server_at', $model->getHtmlOptions('change_server_at')); ?>
					<?php echo $form->error($model, 'change_server_at');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($model, 'max_bounce_rate', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'max_bounce_rate', $model->getHtmlOptions('max_bounce_rate')); ?>
					<?php echo $form->error($model, 'max_bounce_rate');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-12">
					<?php echo $form->labelEx($model, 'feedback_id_header_format', array('class' => 'control-label'));?>
					<?php echo $form->textField($model, 'feedback_id_header_format', $model->getHtmlOptions('feedback_id_header_format')); ?>
					<?php echo $form->error($model, 'feedback_id_header_format');?>
					<div class="alert alert-success margin-top-15">
						<?php echo Yii::t('settings', 'Following placeholders are available:');?>
						<?php echo implode("<br />", $model->getFeedbackIdFormatTagsInfoHtml());?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-12">
					<?php echo $form->labelEx($model, 'email_footer', array('class' => 'control-label'));?>
					<?php echo $form->textArea($model, 'email_footer', $model->getHtmlOptions('email_footer')); ?>
					<?php echo $form->error($model, 'email_footer');?>
					<div class="alert alert-success margin-top-15">
						<?php echo $model->getAttributeHelpText('email_footer');?>
					</div>
				</div>
			</div>
			<?php
			/**
			 * This hook gives a chance to append content after the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.4.3
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
