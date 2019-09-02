<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.5
 */
 
?>

<div class="col-lg-4">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo Yii::t('campaign_reports', 'Emails sent');?></h3>
        </div>
        <div class="panel-body" style="height:380px; overflow-y:scroll;">
            <div class="circliful-graph" data-dimension="250" data-text="<?php echo $campaign->stats->getDeliverySuccessRate(true);?>%" data-info="<?php echo Yii::t('campaign_reports', 'Emails sent');?>" data-width="30" data-fontsize="38" data-percent="<?php echo ceil($campaign->stats->getDeliverySuccessRate());?>" data-fgcolor="#3c8dbc" data-bgcolor="#eee" data-border="inline" data-type="half"></div>
            <ul class="list-group">
                <li class="list-group-item panel-title">
					<span class="badge">
						<?php echo $campaign->stats->getProcessedCount(true);?>
					</span>
					<?php echo Yii::t('campaign_reports', 'Processed');?>
				</li>
                <li class="list-group-item panel-title">
					<span class="badge">
						<?php echo $campaign->stats->getDeliverySuccessCount(true);?>
					</span>
					<?php echo Yii::t('campaign_reports', 'Success');?>
				</li>
                <li class="list-group-item active panel-title">
					<span class="badge">
						<?php echo $campaign->stats->getDeliverySuccessRate(true);?>%
					</span>
					<?php echo Yii::t('campaign_reports', 'Success rate');?>
				</li>
                <li class="list-group-item panel-title">
					<span class="badge">
						<?php echo $campaign->stats->getDeliveryErrorCount(true);?>
					</span> 
					<?php echo Yii::t('campaign_reports', 'Error');?>
				</li>
                <li class="list-group-item active panel-title">
					<span class="badge">
						<?php echo $campaign->stats->getDeliveryErrorRate(true);?>%
					</span>
					<?php echo Yii::t('campaign_reports', 'Error rate');?>
				</li>
            </ul>
            <div class="clearfix"><!-- --></div>
        </div>        
    </div>
</div>