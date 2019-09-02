<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4
 */
 
?>
<div class="row">
	<div class="col-lg-12">
		<div class="row">
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'campaign_emails', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'campaign_emails', $model->getYesNoOptions(), $model->getHtmlOptions('campaign_emails')); ?>
				<?php echo $form->error($model, 'campaign_emails');?>
			</div>
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'campaign_test_emails', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'campaign_test_emails', $model->getYesNoOptions(), $model->getHtmlOptions('campaign_test_emails')); ?>
				<?php echo $form->error($model, 'campaign_test_emails');?>
			</div>
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'template_test_emails', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'template_test_emails', $model->getYesNoOptions(), $model->getHtmlOptions('template_test_emails')); ?>
				<?php echo $form->error($model, 'template_test_emails');?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'list_emails', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'list_emails', $model->getYesNoOptions(), $model->getHtmlOptions('list_emails')); ?>
				<?php echo $form->error($model, 'list_emails');?>
			</div>
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'transactional_emails', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'transactional_emails', $model->getYesNoOptions(), $model->getHtmlOptions('transactional_emails')); ?>
				<?php echo $form->error($model, 'transactional_emails');?>
			</div>
		</div>
	</div>
</div>