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
			<span class="glyphicon glyphicon-transfer"></span>
            <span class="caption-subject font-dark sbold uppercase">
                 <?php echo $pageHeading;?>
            </span>
        </div>
    </div>
    <div class="portlet-body">
		<div class="row">
			 <div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'email', array('class' => 'control-label'));?>
				<?php echo $form->textField($model, 'email', $model->getHtmlOptions('email')); ?>
				<?php echo $form->error($model, 'email');?>
			 </div>
			 <div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'mode', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'mode', $model->getModes(), $model->getHtmlOptions('mode')); ?>
				<?php echo $form->error($model, 'mode');?>
			 </div> 
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
			<div class="col-md-12">
				<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
			</div>
		</div>
    </div>
<?php $this->endWidget(); ?>