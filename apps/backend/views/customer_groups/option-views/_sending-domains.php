<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.7
 */
 
 ?>
<div class="row">
	<div class="col-lg-12">
		<div class="caption margin-bottom-10">
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('settings', 'Sending domains')?>
			</h3>
		</div>
		<div class="row">
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'can_manage_sending_domains', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'can_manage_sending_domains', $model->getYesNoOptions(), $model->getHtmlOptions('can_manage_sending_domains')); ?>
				<?php echo $form->error($model, 'can_manage_sending_domains');?>
			</div>
			<div class="form-group col-lg-6">
				<?php echo $form->labelEx($model, 'max_sending_domains', array('class' => 'control-label'));?>
				<?php echo $form->textField($model, 'max_sending_domains', $model->getHtmlOptions('max_sending_domains')); ?>
				<?php echo $form->error($model, 'max_sending_domains');?>
			</div>
		</div>
	</div>
</div>