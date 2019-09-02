<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.2
 */
?>


<?php echo CHtml::form($this->createUrl($this->route, array('list_uid' => $list->list_uid)), 'get', array(
    'id'    => 'campaigns-filters-form',
    'style' => 'display:' . (!empty($getFilterSet) ? 'block' : 'none') . ';',
));?>


<div class="portlet-title">
	<div class="caption">
		<span class="glyphicon glyphicon-filter"></span>
		<span class="caption-subject font-dark sbold uppercase">
			<?php echo Yii::t('list_subscribers', 'Campaigns filters');?>
		</span>
	</div>
	<div class="pull-right">
		<div class="btn-group btn-group-devided">
			<?php echo CHtml::submitButton(Yii::t('list_subscribers', 'Set filters'), array('name' => 'submit', 'class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm'));?>
			<?php echo CHtml::link(Yii::t('list_subscribers', 'Reset filters'), array('list_subscribers/index', 'list_uid' => $list->list_uid), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('list_subscribers', 'Reset filters')));?>
		</div>
	</div>
</div>
<div class="portlet-body">
	<table class="table table-bordered table-hover no-margin">
		<tr>
			<td>
				<label class="control-label"><?php echo Yii::t('list_subscribers', 'Show only subscribers that');?>:</label>
				<?php echo CHtml::dropDownList('filter[campaigns][action]', $getFilter['campaigns']['action'], CMap::mergeArray(array('' => ''), $subscriber->getCampaignFilterActions()), array('class' => 'form-control'));?>
			</td>
			<td>
				<label class="control-label"><?php echo Yii::t('list_subscribers', 'This campaign');?>:</label>
				<?php echo CHtml::dropDownList('filter[campaigns][campaign]', $getFilter['campaigns']['campaign'], CMap::mergeArray(array('' => ''), $listCampaigns), array('class' => 'form-control'));?>
			</td>
			<td style="width:280px">
				<label class="control-label"><?php echo Yii::t ('list_subscribers', 'In the last');?>:</label>
				<div class="input-group">
					<?php echo CHtml::textField('filter[campaigns][atuc]', $getFilter['campaigns']['atuc'], array('class' => 'form-control', 'type' => 'number', 'placeholder' => 2));?>
					<span class="input-group-addon" style="padding:0 5px;">
						<?php echo CHtml::dropDownList('filter[campaigns][atu]', $getFilter['campaigns']['atu'], $subscriber->getFilterTimeUnits(), array('class' => 'xform-control'));?>
					</span>
				</div>
			</td>
		</tr>
	</table>
</div>
<?php echo CHtml::endForm();?>