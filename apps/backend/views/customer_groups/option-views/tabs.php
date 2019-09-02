<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4
 */
 
 ?>
<ul class="nav nav-tabs">
	<li class="active">
		<a href="#tab-common" data-toggle="tab"><?php echo Yii::t('settings', 'Common');?></a>
	</li>
	<li>
		<a href="#tab-servers" data-toggle="tab"><?php echo Yii::t('settings', 'Servers');?></a>
	</li>
	<li>
		<a href="#tab-domains" data-toggle="tab"><?php echo Yii::t('settings', 'Domains');?></a>
	</li>
	<li>
		<a href="#tab-lists" data-toggle="tab"><?php echo Yii::t('settings', 'Lists');?></a>
	</li>
	<li>
		<a href="#tab-campaigns" data-toggle="tab"><?php echo Yii::t('settings', 'Campaigns');?></a>
	</li>
	<li>
		<a href="#tab-qq" data-toggle="tab"><?php echo Yii::t('settings', 'Quota counters');?></a>
	</li>
	<li>
		<a href="#tab-sending" data-toggle="tab"><?php echo Yii::t('settings', 'Sending');?></a>
	</li>
	<li>
		<a href="#tab-smssending" data-toggle="tab"><?php echo Yii::t('settings', 'SmsSending');?></a>
	</li>
	<li>
		<a href="#tab-timeunit" data-toggle="tab"><?php echo Yii::t('settings', 'Validity');?></a>
	</li>
	<li>
		<a href="#tab-cdn" data-toggle="tab"><?php echo Yii::t('settings', 'CDN');?></a>
	</li>
</ul>

<div class="tab-content">
	<div class="tab-pane active" id="tab-common">
		<?php $this->renderPartial('option-views/_common', array('model' => $common, 'form' => $form));?>
	</div>
	<div class="tab-pane" id="tab-servers">
		<?php $this->renderPartial('option-views/_servers', array('model' => $servers, 'form' => $form));?>
	</div>
	<div class="tab-pane" id="tab-domains">
		<?php $this->renderPartial('option-views/_tracking-domains', array('model' => $trackingDomains, 'form' => $form));?>
		<?php $this->renderPartial('option-views/_sending-domains', array('model' => $sendingDomains, 'form' => $form));?>
	</div>
	<div class="tab-pane" id="tab-lists">
		<?php $this->renderPartial('option-views/_lists', array('model' => $lists, 'form' => $form));?>
	</div>
	<div class="tab-pane" id="tab-campaigns">
		<?php $this->renderPartial('option-views/_campaigns', array('model' => $campaigns, 'form' => $form));?>
	</div>
	<div class="tab-pane" id="tab-qq">
		<?php $this->renderPartial('option-views/_quota', array('model' => $quotaCounters, 'form' => $form));?>
	</div>
	<div class="tab-pane" id="tab-sending">
		<?php $this->renderPartial('option-views/_sending', array('model' => $sending, 'form' => $form));?>
	</div>
	<div class="tab-pane" id="tab-smssending">
		<?php $this->renderPartial('option-views/_smssending', array('model' => $smssending, 'form' => $form));?>
	</div>
	<div class="tab-pane" id="tab-timeunit">
		<?php $this->renderPartial('option-views/_timeunit', array('model' => $sending, 'form' => $form));?>
	</div>
	<div class="tab-pane" id="tab-cdn">
		<?php $this->renderPartial('option-views/_cdn', array('model' => $cdn, 'form' => $form));?>
	</div>
</div>