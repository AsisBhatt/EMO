<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */
 
?>

<?php $form = $this->beginWidget('CActiveForm'); ?>

    <div class="portlet-title">
        <div class="caption">
			<span class="glyphicon glyphicon-map-marker"></span>
			<span class="caption-subject font-dark sbold uppercase">
                 <?php echo Yii::t('ext_ip_location_freegeoip', 'Ip location service from Freegeoip.net');?>
            </span>
        </div>
    </div>
    <div class="portlet-body">
         <div class="alert alert-success margin-bottom-20">
            <?php echo Yii::t('ext_ip_location_freegeoip', 'Once the the service is enabled, it will start collecting informations each time when a campaign is opened and/or when a link from within a campaign is clicked.');?> 
         </div>
		<div class="row">
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'status', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'status', $model->getStatusesDropDown(), $model->getHtmlOptions('status')); ?>
				<?php echo $form->error($model, 'status');?>
			</div> 
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'sort_order', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'sort_order', $model->getSortOrderDropDown(), $model->getHtmlOptions('sort_order', array('data-placement' => 'left'))); ?>
				<?php echo $form->error($model, 'sort_order');?>
			</div> 
		</div> 
		<div class="row">
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'status_on_email_open', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'status_on_email_open', $model->getStatusesDropDown(), $model->getHtmlOptions('status_on_email_open')); ?>
				<?php echo $form->error($model, 'status_on_email_open');?>
			</div> 
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'status_on_track_url', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'status_on_track_url', $model->getStatusesDropDown(), $model->getHtmlOptions('status_on_track_url')); ?>
				<?php echo $form->error($model, 'status_on_track_url');?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'status_on_unsubscribe', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'status_on_unsubscribe', $model->getStatusesDropDown(), $model->getHtmlOptions('status_on_unsubscribe')); ?>
				<?php echo $form->error($model, 'status_on_unsubscribe');?>
			</div>
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'status_on_customer_login', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'status_on_customer_login', $model->getStatusesDropDown(), $model->getHtmlOptions('status_on_customer_login')); ?>
				<?php echo $form->error($model, 'status_on_customer_login');?>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
			</div>
		</div>
    </div>
<?php $this->endWidget(); ?>