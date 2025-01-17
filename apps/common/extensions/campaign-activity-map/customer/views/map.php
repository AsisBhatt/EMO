<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */
 
?>

<div class="col-lg-12" id="activity-map-container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-left">
                <h3 class="panel-title"><?php echo Yii::t('campaign_reports', 'Activity map (click to view)');?></h3>
            </div>
            <div class="pull-right">
                <div>
                    <div class="pull-right" id="map-links">
                        <a href="javascript:;" id="enter-exit-fullscreen" class="btn btn-default btn-xs" data-enter="<?php echo Yii::t('campaign_reports', 'Enter full screen');?>" data-exit="<?php echo Yii::t('campaign_reports', 'Exit full screen');?>"><?php echo Yii::t('campaign_reports', 'Enter full screen');?></a>
                        <?php if ($context->getOption('show_opens_map')) { ?>
                        <a href="<?php echo $this->createUrl('campaigns/opens_activity_map', array('campaign_uid' => $campaign->campaign_uid));?>" id="map-opens" class="btn btn-default btn-xs"><?php echo Yii::t('campaign_reports', 'Opens');?></a>
                        <?php } ?>
                        <?php if ($context->getOption('show_clicks_map') && $campaign->option->url_tracking == CampaignOption::TEXT_YES) { ?>
                        <a href="<?php echo $this->createUrl('campaigns/clicks_activity_map', array('campaign_uid' => $campaign->campaign_uid));?>" id="map-clicks" class="btn btn-default btn-xs"><?php echo Yii::t('campaign_reports', 'Clicks');?></a>
                        <?php } ?>
                        <?php if ($context->getOption('show_unsubscribes_map')) { ?>
                        <a href="<?php echo $this->createUrl('campaigns/unsubscribes_activity_map', array('campaign_uid' => $campaign->campaign_uid));?>" id="map-unsubscribes" class="btn btn-default btn-xs"><?php echo Yii::t('campaign_reports', 'Unsubscribes');?></a>
                        <?php } ?>
                    </div>
                    <div class="pull-right">
                        <span class="btn-spinner-xs-right"></span>
                    </div>
                    <div class="clearfix"><!-- --></div>
                </div>
            </div>
            <div class="clearfix"><!-- --></div>
        </div>
        <div class="panel-body">
            
            <div id="map" 
                 data-markerclusterer='{"imagePath":"<?php echo $context->getAssetsUrl();?>/images/m"}' 
                 data-translate='{"email":"<?php echo CHtml::encode(Yii::t('campaign_reports', 'Email'));?>", "date":"<?php echo CHtml::encode(Yii::t('campaign_reports', 'Date'));?>", "ip":"<?php echo CHtml::encode(Yii::t('campaign_reports', 'Ip address'));?>", "device":"<?php echo CHtml::encode(Yii::t('campaign_reports', 'Device'));?>"}'
            ></div>
            <div class="clearfix"><!-- --></div>
        </div>
        <div class="panel-footer">
            <div class="pull-right">
                <div class="map-messages">
                    <div class="loading-message" style="display: none;">
                        <?php echo Yii::t('campaign_reports', 'Loading page number {pageNumber}...', array(
                            '{pageNumber}' => '<span class="loading-page-number"></span>',
                        ));?>
                    </div>
                    <div class="done-loading" style="display: none;">
                        <?php echo Yii::t('campaign_reports', 'Done loading records.', array());?>
                    </div>
                </div>
            </div>
            <div class="clearfix"><!-- --></div>
        </div>
    </div>
</div>