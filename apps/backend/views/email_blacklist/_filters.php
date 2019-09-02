<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.3
 */
?>


<?php $form = $this->beginWidget('CActiveForm', array(
    'id'          => 'filters-form',
    'method'      => 'get',
    'action'      => $this->createUrl($this->route),
    'htmlOptions' => array(
        'style'        => 'display:' . ($filter->hasSetFilters ? 'block' : 'none'),
        'data-confirm' => Yii::t('email_blacklist', 'Are you sure you want to run this action?')
    ),
));?> 
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-filter"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('email_blacklist', 'Filters');?>
			</span>
		</div>
	</div>
	<div class="portlet-body">
		<table class="table table-bordered table-hover">
			<tr>
				<td>
					<div class="form-group">
						<?php echo $form->labelEx($filter, 'email', array('class' => 'control-label'));?>
						<?php echo $form->textField($filter, 'email', $filter->getHtmlOptions('email', array('name' => 'email'))); ?>
						<?php echo $form->error($filter, 'email');?>
					</div>
				</td>
				<td>
					<div class="form-group">
						<?php echo $form->labelEx($filter, 'reason', array('class' => 'control-label'));?>
						<?php echo $form->textField($filter, 'reason', $filter->getHtmlOptions('reason', array('name' => 'reason'))); ?>
						<?php echo $form->error($filter, 'reason');?>
					</div>
				</td>
				<td>
					<div class="form-group">
						<?php echo $form->labelEx($filter, 'date_start', array('class' => 'control-label'));?>
						<?php
						$this->widget('zii.widgets.jui.CJuiDatePicker',array(
							'model'     => $filter,
							'attribute' => 'date_start',
							'language'  => $filter->getDatePickerLanguage(),
							'cssFile'   => null,
							'options'   => array(
								'showAnim'      => 'fold',
								'dateFormat'    => $filter->getDatePickerFormat(),
							),
							'htmlOptions'=>$filter->getHtmlOptions('date_start', array('name' => 'date_start')),
						));
						?>
						<?php echo $form->error($filter, 'date_start');?>
					</div>
				</td>
				<td>
					<div class="form-group">
						<?php echo $form->labelEx($filter, 'date_end', array('class' => 'control-label'));?>
						<?php
						$this->widget('zii.widgets.jui.CJuiDatePicker',array(
							'model'     => $filter,
							'attribute' => 'date_end',
							'language'  => $filter->getDatePickerLanguage(),
							'cssFile'   => null,
							'options'   => array(
								'showAnim'      => 'fold',
								'dateFormat'    => $filter->getDatePickerFormat(),
							),
							'htmlOptions'=>$filter->getHtmlOptions('date_end', array('name' => 'date_end')),
						));
						?>
						<?php echo $form->error($filter, 'date_end');?>
					</div>
				</td>
				<td>
					<div class="form-group">
						<?php echo $form->labelEx($filter, 'action', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($filter, 'action', $filter->getActionsList(), $filter->getHtmlOptions('action', array('name' => 'action'))); ?>
						<?php echo $form->error($filter, 'action');?>
					</div>
				</td>
			</tr>
		</table>
		<div class="row">
			<div class="col-md-12">
				<?php echo CHtml::submitButton(Yii::t('email_blacklist', 'Submit'), array('name' => '', 'class' => 'btn green'));?>
			</div>
		</div>
	</div>
	<hr style="margin:0;">
<?php $this->endWidget(); ?>