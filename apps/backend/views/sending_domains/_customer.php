<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.8
 */
 
?>

<div class="portlet-title">
	<div class="caption">
		<span class="glyphicon glyphicon-user"></span>
		<span class="caption-subject font-dark sbold uppercase">
			<?php echo Yii::t('sending_domains', 'Customer');?>
		</span>
	</div>
</div>
<div class="portlet-body">
	<div class="row">
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($domain, 'customer_id', array('class' => 'control-label'));?>
			<?php echo $form->hiddenField($domain, 'customer_id', $domain->getHtmlOptions('customer_id')); ?>
			<?php
			$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
				'name'          => 'customer',
				'value'         => !empty($domain->customer) ? $domain->customer->getFullName() : null,
				'source'        => $this->createUrl('customers/autocomplete'),
				'cssFile'       => false,
				'options'       => array(
					'minLength' => '2',
					'select'    => 'js:function(event, ui) {
						$("#'.CHtml::activeId($domain, 'customer_id').'").val(ui.item.customer_id);
					}',
					'search'    => 'js:function(event, ui) {
						$("#'.CHtml::activeId($domain, 'customer_id').'").val("");
					}',
					'change'    => 'js:function(event, ui) {
						if (!ui.item) {
							$("#'.CHtml::activeId($domain, 'customer_id').'").val("");
						}
					}',
				),
				'htmlOptions'   => $domain->getHtmlOptions('customer_id'),
			));
			?>
			<?php echo $form->error($domain, 'customer_id');?>
		</div>
		<div class="form-group col-lg-4">
			<?php echo $form->labelEx($domain, 'locked', array('class' => 'control-label'));?>
			<?php echo $form->dropDownList($domain, 'locked', $domain->getYesNoOptions(), $domain->getHtmlOptions('locked')); ?>
			<?php echo $form->error($domain, 'locked');?>
		</div>
	</div>       
</div>
