<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.6
 */
 
 ?>
<div class="row">
	 <div class="col-lg-12">
		<div class="row">
			<div class="form-group col-lg-4">
				<?php echo $form->labelEx($model, 'show_articles_menu');?>
				<?php echo $form->dropDownList($model, 'show_articles_menu', $model->getYesNoOptions(), $model->getHtmlOptions('show_articles_menu')); ?>
				<?php echo $form->error($model, 'show_articles_menu');?>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-lg-12">
				<?php echo $form->labelEx($model, 'notification_message');?>
				<?php echo $form->textArea($model, 'notification_message', $model->getHtmlOptions('notification_message')); ?>
				<?php echo $form->error($model, 'notification_message');?>
			</div>
		</div>
	</div>
</div>