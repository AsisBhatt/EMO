<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.5
 */
 
?>
<div class="portlet-title">
	<div class="caption">
		<span class="glyphicon glyphicon-lock"></span>
		<span class="caption-subject font-dark sbold uppercase">
			<?php echo Yii::t('servers', 'Domain policies');?>
		</span>
	</div>
	<div class="actions">
		<div class="btn-group btn-group-devided">
			<a href="javascript:;" class="btn btn-transparent grey-salsa btn-outline btn-circle btn-sm btn-add-policy"><?php echo Yii::t('servers', 'Add new policy');?></a>
		</div>
	</div>
</div>
<div class="portlet-body">
	<div class="alert alert-success margin-bottom-15">
		<?php echo Yii::t('servers', 'If your delivery server cannot send emails to certain domains, or it can only send to a small list of domains, you can add domain policies to reflect this.');?><br />
		<?php echo Yii::t('servers', 'If you want to send emails only to yahoo.com but deny for any other domain, you will need a allow policy for the domain yahoo.com and a deny policy on domain *');?><br />
		<?php echo Yii::t('servers', 'If you want to send to all domains except yahoo, then a deny policy on yahoo domain is enough.');?><br />
		<?php echo Yii::t('servers', 'If you want a policy for all yahoo emails, including yahoo.co.uk, yahoo.com.br, etc you can simply enter "yahoo" as policy domain.');?><br />
		<?php echo Yii::t('servers', 'The sign * acts as a policy wildcard matching any domain. A domain of domain*.com or *domain.com has no effect.');?><br />
	</div>
	<div class="row">
		<div id="policies-list">
			<?php if (!empty($policies)) { ?>
			<?php $i = 0; foreach ($policies as $policyModel) { ?>
				<div class="form-group col-lg-6">
					<div class="row">
						<div class="col-lg-5">
							<label class="required control-label"><?php echo Yii::t('servers', 'Domain name');?> <span class="required">*</span></label>
							<?php echo CHtml::textField($policyModel->modelName . '['.$i.'][domain]', $policyModel->domain, $policyModel->getHtmlOptions('domain'));?>
						</div>
						<div class="col-lg-5">
							<label class="required control-label"><?php echo Yii::t('servers', 'Policy');?> <span class="required">*</span></label>
							<?php echo CHtml::dropDownList($policyModel->modelName . '['.$i.'][policy]', $policyModel->policy, $policyModel->getPoliciesList(), $policyModel->getHtmlOptions('policy'));?>
						</div>
						<div class="col-lg-2">
							<label class="control-label">&nbsp;</label>
							<a href="javascript:;" class="btn green remove-policy"><?php echo Yii::t('app', 'Remove');?></a>
						</div>
					</div>
				</div>
			<?php ++$i; } ?>
			<?php } ?>
		</div>
	</div>
</div>

<div id="policies-template" style="display: none;" data-count="<?php echo !empty($policies) ? count($policies) : 0;?>">
	<div class="form-group col-lg-6">
		<div class="row">
			<div class="col-lg-5">
				<label class="required control-label"><?php echo Yii::t('servers', 'Domain name');?> <span class="required">*</span></label>
				<?php echo CHtml::textField($policy->modelName . '[__#__][domain]', null, $policy->getHtmlOptions('domain', array('disabled' => true)));?>
			</div>
			<div class="col-lg-5">
				<label class="required control-label"><?php echo Yii::t('servers', 'Policy');?> <span class="required">*</span></label>
				<?php echo CHtml::dropDownList($policy->modelName . '[__#__][policy]', null, $policy->getPoliciesList(), $policy->getHtmlOptions('policy', array('disabled' => true)));?>
			</div>
			<div class="col-lg-2">
				<label class="control-label">&nbsp;</label>
				<a href="javascript:;" class="btn green remove-policy"><?php echo Yii::t('app', 'Remove');?></a>
			</div>
		</div>
	</div>
</div>