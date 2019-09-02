<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3
 */

?>
<div class="portlet-title">
	<div class="caption">
		<span class="glyphicon glyphicon-plus-sign"></span>
		<span class="caption-subject font-dark sbold uppercase">
			<?php echo Yii::t('servers', 'Additional headers');?>
		</span>
	</div>
	<div class="actions">
		<div class="btn-group btn-group-devided">
			<a href="javascript:;" class="btn btn-transparent grey-salsa btn-outline btn-circle btn-sm btn-add-header"><?php echo Yii::t('servers', 'Add new header');?></a>
		</div>
	</div>
</div>
<div class="portlet-body">
	<div class="alert alert-success margin-bottom-15">
		<?php echo Yii::t('servers', 'If your delivery server needs extra headers in order to make the delivery, you can add them here.');?><br />
		<?php echo Yii::t('servers', 'If a header is not in the correct format or if it is part of the restricted headers, it will not be added.');?><br />
		<?php echo Yii::t('servers', 'Use this with caution and only if you know what you are doing, wrong headers can make your email delivery fail.');?><br />
		<?php echo Yii::t('servers', 'Following dynamic tags will be parsed depending on context:');?> <em><strong>[CAMPAIGN_UID], [SUBSCRIBER_UID], [SUBSCRIBER_EMAIL]</strong></em>
	</div>
	<div class="row">
		<div id="headers-list">
			<?php $i = 0; foreach ($server->additional_headers as $header) { ?>
				<div class="form-group col-lg-6">
					<div class="row">
						<div class="col-lg-5">
							<label class="required control-label"><?php echo Yii::t('servers', 'Header name');?> <span class="required">*</span></label>
							<?php echo CHtml::textField($server->modelName . '[additional_headers]['.$i.'][name]', $header['name'], $server->getHtmlOptions('additional_headers', array('placeholder' => Yii::t('servers', 'X-Header-Name'))));?>
						</div>
						<div class="col-lg-5">
							<label class="required control-label"><?php echo Yii::t('servers', 'Header value');?> <span class="required">*</span></label>
							<?php echo CHtml::textField($server->modelName . '[additional_headers]['.$i.'][value]', $header['value'], $server->getHtmlOptions('additional_headers', array('placeholder' => Yii::t('servers', 'Header value'))));?>
						</div>
						<div class="col-lg-2">
							<label class="control-label">&nbsp;</label>
							<a href="javascript:;" class="btn green remove-header"><?php echo Yii::t('app', 'Remove');?></a>
						</div>
					</div>
				</div>
			<?php ++$i; } ?>
		</div>
	</div>
</div>

<div id="headers-template" style="display: none;" data-count="<?php echo count($server->additional_headers);?>">
    <div class="form-group col-lg-6">
		<div class="row">
			<div class="col-lg-5">
				<label class="required control-label"><?php echo Yii::t('servers', 'Header name');?> <span class="required">*</span></label>
				<?php echo CHtml::textField($server->modelName . '[additional_headers][__#__][name]', null, $server->getHtmlOptions('additional_headers', array('disabled' => true, 'placeholder' => Yii::t('servers', 'X-Header-Name'))));?>
			</div>
			<div class="col-lg-5">
				<label class="required control-label"><?php echo Yii::t('servers', 'Header value');?> <span class="required">*</span></label>
				<?php echo CHtml::textField($server->modelName . '[additional_headers][__#__][value]', null, $server->getHtmlOptions('additional_headers', array('disabled' => true, 'placeholder' => Yii::t('servers', 'Header value'))));?>
			</div>
			<div class="col-lg-2">
				<label class="control-label">&nbsp;</label>
				<a href="javascript:;" class="btn green remove-header"><?php echo Yii::t('app', 'Remove');?></a>
			</div>
		</div>
    </div>
</div>
