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

<div id="field-text-javascript-template" style="display: none;">
    
    <div class="field-row" data-start-index="{index}" data-field-type="<?php echo $fieldType->identifier;?>">
        <?php echo CHtml::hiddenField($model->modelName.'['.$fieldType->identifier.'][{index}][field_id]', (int)$model->field_id); ?>

        <ul class="nav nav-tabs no-margin">
            <li class="active">
                <a href="javascript:;"><span class="glyphicon glyphicon-th-list"></span> <?php echo Yii::t('list_fields', 'New text field');?></a>
            </li>
        </ul>
        
        <div class="panel panel-default no-top-border">

            <div class="panel-body">
                <div class="row">
					<div class="form-group col-lg-4">
						<?php echo CHtml::activeLabelEx($model, 'label', array('class' => 'control-label'));?>
						<?php echo CHtml::textField($model->modelName.'['.$fieldType->identifier.'][{index}][label]', $model->label, $model->getHtmlOptions('label')); ?>
						<?php echo CHtml::error($model, 'label');?>
					</div>
					<div class="form-group col-lg-2">
						<?php echo CHtml::activeLabelEx($model, 'tag', array('class' => 'control-label'));?>
						<?php echo CHtml::textField($model->modelName.'['.$fieldType->identifier.'][{index}][tag]', $model->tag, $model->getHtmlOptions('tag')); ?>
						<?php echo CHtml::error($model, 'tag');?>
					</div>
					<div class="form-group col-lg-2">
						<?php echo CHtml::activeLabelEx($model, 'required', array('class' => 'control-label'));?>
						<?php echo CHtml::dropDownList($model->modelName.'['.$fieldType->identifier.'][{index}][required]', $model->required, $model->getRequiredOptionsArray(), $model->getHtmlOptions('required')); ?>
						<?php echo CHtml::error($model, 'required');?>
					</div>
					<div class="form-group col-lg-2">
						<?php echo CHtml::activeLabelEx($model, 'visibility', array('class' => 'control-label'));?>
						<?php echo CHtml::dropDownList($model->modelName.'['.$fieldType->identifier.'][{index}][visibility]', $model->visibility, $model->getVisibilityOptionsArray(), $model->getHtmlOptions('visibility')); ?>
						<?php echo CHtml::error($model, 'visibility');?>
					</div>
					<div class="form-group col-lg-2">
						<?php echo CHtml::activeLabelEx($model, 'sort_order', array('class' => 'control-label'));?>
						<?php echo CHtml::dropDownList($model->modelName.'['.$fieldType->identifier.'][{index}][sort_order]', $model->sort_order, $model->getSortOrderOptionsArray(), $model->getHtmlOptions('sort_order', array('data-placement' => 'left'))); ?>
						<?php echo CHtml::error($model, 'sort_order');?>
					</div>
					
					<div class="clearfix"><!-- --></div>
					
					<div class="form-group col-lg-6">
						<?php echo CHtml::activeLabelEx($model, 'help_text', array('class' => 'control-label'));?>
						<?php echo CHtml::textField($model->modelName.'['.$fieldType->identifier.'][{index}][help_text]', $model->help_text, $model->getHtmlOptions('help_text')); ?>
						<?php echo CHtml::error($model, 'help_text');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo CHtml::activeLabelEx($model, 'default_value', array('class' => 'control-label'));?>
						<?php echo CHtml::textField($model->modelName.'['.$fieldType->identifier.'][{index}][default_value]', $model->default_value, $model->getHtmlOptions('default_value')); ?>
						<?php echo CHtml::error($model, 'default_value');?>
					</div>
				</div>
            </div>

            <div class="panel-footer">
                <div class="pull-right">
                    <a href="javascript:;" class="btn btn-danger btn-xs btn-remove-text-field" data-field-id="0" data-message="<?php echo Yii::t('list_fields', 'Are you sure you want to remove this field? There is no coming back from this after you save the changes.');?>"><?php echo Yii::t('app', 'Remove');?></a>
                </div>
                <div class="clearfix"><!-- --></div>
            </div>    
    
        </div>
    
    </div>
    
</div>