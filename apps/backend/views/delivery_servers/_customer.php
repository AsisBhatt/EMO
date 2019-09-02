<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */
 
?>
<div class="portlet-title">
	<div class="caption">
		<span class="glyphicon glyphicon-user"></span>
		<span class="caption-subject font-dark sbold uppercase">
			<?php echo Yii::t('servers', 'Customer');?>
		</span>
	</div>
</div>
<div class="portlet-body">
	<div class="row">
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($server, 'customer_id', array('class' => 'control-label'));?>
			<?php echo $form->hiddenField($server, 'customer_id', $server->getHtmlOptions('customer_id')); ?>
			<?php
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
				'name'          => 'customer',
				'value'         => !empty($server->customer) ? ($server->customer->getFullName() ? $server->customer->getFullName() : $server->customer->email) : null,
				'source'        => $this->createUrl('customers/autocomplete'),
				'cssFile'       => false,
				'options'       => array(
					'minLength' => '2',
					'select'    => 'js:function(event, ui) {
						$("#'.CHtml::activeId($server, 'customer_id').'").val(ui.item.customer_id);
					}',
					'search'    => 'js:function(event, ui) {
						$("#'.CHtml::activeId($server, 'customer_id').'").val("");
					}',
					'change'    => 'js:function(event, ui) {
						if (!ui.item) {
							$("#'.CHtml::activeId($server, 'customer_id').'").val("");
						}
					}',
				),
				'htmlOptions'   => $server->getHtmlOptions('customer_id'),
			));
			?>
			<?php echo $form->error($server, 'customer_id');?>
		</div>
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($server, 'locked', array('class' => 'control-label'));?>
			<?php echo $form->dropDownList($server, 'locked', $server->getYesNoOptions(), $server->getHtmlOptions('locked')); ?>
			<?php echo $form->error($server, 'locked');?>
		</div>
	</div>
</div>
