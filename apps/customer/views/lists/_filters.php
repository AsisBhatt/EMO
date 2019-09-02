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
        'data-confirm' => Yii::t('list_subscribers', 'Are you sure you want to run this action?')
    ),
));?>
    <div class="portlet-title">
        <div class="caption">
			<span class="glyphicon glyphicon-filter"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('list_subscribers', 'Filters');?>
			</span>
        </div>
    </div>
    <div class="portlet-body">	
        <table class="table table-bordered table-hover">
            <tr>
                <td>
                    <div class="form-group">
                        <?php echo $form->labelEx($filter, 'lists', array('class' => 'control-label'));?>
                        <?php echo $form->dropDownList($filter, 'lists', $filter->getListsList(), $filter->getHtmlOptions('lists', array('multiple' => true, 'name' => 'lists'))); ?>
                        <?php echo $form->error($filter, 'lists');?>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <?php echo $form->labelEx($filter, 'statuses', array('class' => 'control-label'));?>
                        <?php echo $form->dropDownList($filter, 'statuses', $filter->getStatusesList(), $filter->getHtmlOptions('statuses', array('multiple' => true, 'name' => 'statuses'))); ?>
                        <?php echo $form->error($filter, 'statuses');?>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <?php echo $form->labelEx($filter, 'sources', array('class' => 'control-label'));?>
                        <?php echo $form->dropDownList($filter, 'sources', $filter->getSourcesList(), $filter->getHtmlOptions('sources', array('multiple' => true, 'name' => 'sources'))); ?>
                        <?php echo $form->error($filter, 'sources');?>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <?php echo $form->labelEx($filter, 'unique', array('class' => 'control-label'));?>
                        <?php echo $form->dropDownList($filter, 'unique', CMap::mergeArray(array('' => ''), $filter->getYesNoOptions()), $filter->getHtmlOptions('unique', array('name' => 'unique'))); ?>
                        <?php echo $form->error($filter, 'unique');?>
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
                        <?php echo $form->labelEx($filter, 'uid', array('class' => 'control-label'));?>
                        <?php echo $form->textField($filter, 'uid', $filter->getHtmlOptions('uid', array('name' => 'uid'))); ?>
                        <?php echo $form->error($filter, 'uid');?>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <?php echo $form->labelEx($filter, 'ip', array('class' => 'control-label'));?>
                        <?php echo $form->textField($filter, 'ip', $filter->getHtmlOptions('ip', array('name' => 'ip'))); ?>
                        <?php echo $form->error($filter, 'ip');?>
                    </div>
                </td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>
                    <div class="form-group">
                        <?php echo $form->labelEx($filter, 'campaigns_action', array('class' => 'control-label'));?>
                        <?php echo $form->dropDownList($filter, 'campaigns_action', CMap::mergeArray(array('' => ''), $filter->getCampaignFilterActions()), $filter->getHtmlOptions('campaigns_action', array('name' => 'campaigns_action'))); ?>
                        <?php echo $form->error($filter, 'campaigns_action');?>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <?php echo $form->labelEx($filter, 'campaigns', array('class' => 'control-label'));?>
                        <?php echo $form->dropDownList($filter, 'campaigns', $filter->getCampaignsList(), $filter->getHtmlOptions('campaigns', array('multiple' => true, 'name' => 'campaigns'))); ?>
                        <?php echo $form->error($filter, 'campaigns');?>
                    </div>
                </td>
                <td style="width:280px">
                    <label class="control-label"><?php echo Yii::t('list_subscribers', 'In the last');?>:</label>
                    <div class="input-group">
                        <?php echo $form->textField($filter, 'campaigns_atuc', $filter->getHtmlOptions('campaign_atuc', array('name' => 'campaigns_atuc', 'type' => 'number', 'placeholder' => 30))); ?>
                        <span class="input-group-addon" style="padding:0 5px;">
                            <?php echo $form->dropDownList($filter, 'campaigns_atu', $filter->getFilterTimeUnits(), $filter->getHtmlOptions('campaigns_atu', array('name' => 'campaigns_atu', 'class' => 'xform-control'))); ?>
                        </span>
                    </div>
                </td>
                <td></td>
                <td></td>
            </tr>
        </table>
		<div class="row">
			<div class="col-md-12">
				<?php echo CHtml::submitButton(Yii::t('list_subscribers', 'Submit'), array('name' => '', 'class' => 'btn green'));?>
			</div>
		</div>
    </div>
<?php $this->endWidget(); ?>