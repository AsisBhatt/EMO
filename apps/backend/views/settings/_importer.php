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
			<?php echo Yii::t('settings', 'Importer settings')?>
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
		'controller'    => $this,
		'form'          => $form
	)));
	?>
	<div class="row">	
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($importModel, 'enabled');?>
			<?php echo $form->dropDownList($importModel, 'enabled', $importModel->getYesNoOptions(), $importModel->getHtmlOptions('enabled')); ?>
			<?php echo $form->error($importModel, 'enabled');?>
		</div>
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($importModel, 'web_enabled');?>
			<?php echo $form->dropDownList($importModel, 'web_enabled', $importModel->getYesNoOptions(), $importModel->getHtmlOptions('web_enabled')); ?>
			<?php echo $form->error($importModel, 'web_enabled');?>
		</div>
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($importModel, 'file_size_limit');?>
			<?php echo $form->dropDownList($importModel, 'file_size_limit', $importModel->getFileSizeOptions(), $importModel->getHtmlOptions('file_size_limit')); ?>
			<?php echo $form->error($importModel, 'file_size_limit');?>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($importModel, 'memory_limit');?>
			<?php echo $form->dropDownList($importModel, 'memory_limit', $importModel->getMemoryLimitOptions(), $importModel->getHtmlOptions('memory_limit')); ?>
			<?php echo $form->error($importModel, 'memory_limit');?>
		</div>
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($importModel, 'import_at_once');?>
			<?php echo $form->textField($importModel, 'import_at_once', $importModel->getHtmlOptions('import_at_once')); ?>
			<?php echo $form->error($importModel, 'import_at_once');?>
		</div>
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($importModel, 'pause');?>
			<?php echo $form->textField($importModel, 'pause', $importModel->getHtmlOptions('pause')); ?>
			<?php echo $form->error($importModel, 'pause');?>
		</div>
	</div>
	<div class="row">
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($importModel, 'check_mime_type');?>
			<?php echo $form->dropDownList($importModel, 'check_mime_type', $importModel->getYesNoOptions(), $importModel->getHtmlOptions('check_mime_type')); ?>
			<?php echo $form->error($importModel, 'check_mime_type');?>
		</div>
	</div>
	<hr />
	<div class="row">
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($importModel, 'cli_enabled');?>
			<?php echo $form->dropDownList($importModel, 'cli_enabled', $importModel->getYesNoOptions(), $importModel->getHtmlOptions('cli_enabled')); ?>
			<?php echo $form->error($importModel, 'cli_enabled');?>
		</div>
	</div>
	<div class="alert alert-success">
		<?php echo Yii::t('settings', 'The command line importer(CLI) is used to queue import files to be processed from the command line instead of having customers wait for the import to finish in the browser.');?><br />
		<?php echo Yii::t('settings', 'Please note that in order for the command line importer to work, after you enable it, you need to add the following cron job, which runs once at 5 minutes:');?><br />
		<span class="badge">*/5 * * * * <?php echo CommonHelper::findPhpCliPath();?> -q <?php echo MW_PATH;?>/apps/console/console.php list-import folder >/dev/null 2>&1 </span>
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
</div>