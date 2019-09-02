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
				<?php echo Yii::t('settings', 'Settings for processing bounce servers')?>
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
				<?php echo $form->labelEx($cronBouncesModel, 'memory_limit', array('class' => 'control-label'));?>
				<?php echo $form->dropDownList($cronBouncesModel, 'memory_limit', $cronBouncesModel->getMemoryLimitOptions(), $cronBouncesModel->getHtmlOptions('memory_limit', array('data-placement' => 'right'))); ?>
				<?php echo $form->error($cronBouncesModel, 'memory_limit');?>
			</div>    
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronBouncesModel, 'servers_at_once', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronBouncesModel, 'servers_at_once', $cronBouncesModel->getHtmlOptions('servers_at_once')); ?>
				<?php echo $form->error($cronBouncesModel, 'servers_at_once');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronBouncesModel, 'emails_at_once', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronBouncesModel, 'emails_at_once', $cronBouncesModel->getHtmlOptions('emails_at_once')); ?>
				<?php echo $form->error($cronBouncesModel, 'emails_at_once');?>
			</div>
			<div class="form-group col-lg-3">
				<?php echo $form->labelEx($cronBouncesModel, 'pause', array('class' => 'control-label'));?>
				<?php echo $form->textField($cronBouncesModel, 'pause', $cronBouncesModel->getHtmlOptions('pause')); ?>
				<?php echo $form->error($cronBouncesModel, 'pause');?>
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