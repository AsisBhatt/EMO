<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.4
 */
 
?>
<div class="row">
	<div class="col-lg-12">
		<div class="row">			
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'enabled', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'enabled', $model->getYesNoOptions(), $model->getHtmlOptions('enabled')); ?>
				<?php echo $form->error($model, 'enabled');?>
			</div>
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'subdomain', array('class' => 'control-label'));?>
				<?php echo $form->textField($model, 'subdomain', $model->getHtmlOptions('subdomain')); ?>
				<?php echo $form->error($model, 'subdomain');?>
			</div>
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'use_for_email_assets', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($model, 'use_for_email_assets', $model->getYesNoOptions(), $model->getHtmlOptions('use_for_email_assets')); ?>
				<?php echo $form->error($model, 'use_for_email_assets');?>
			</div>			
		</div>		
	</div>
</div>