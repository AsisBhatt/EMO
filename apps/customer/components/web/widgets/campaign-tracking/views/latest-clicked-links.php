<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
?>

<div class="col-lg-4">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?php echo Yii::t('campaign_reports', 'Latest clicked links');?></h3>
        </div>
        <div class="panel-body" style="height:380px; overflow-y: scroll;">
            <ul class="list-group">
                <?php foreach ($models as $model) { ?>
                <li class="list-group-item panel-title">
                    <div class="pull-left">
                        <?php echo $model->url->getDisplayGridDestination(30);?>
                    </div>
                    <div class="pull-right">
                        <?php echo Yii::t('campaign_reports', 'by {email} at {date}', array(
                            '{email}'   => $model->subscriber->email,
                            '{date}'    => $model->dateAdded,
                        ));?>
                    </div>
                    <div class="clearfix"><!-- --></div>
                </li>
                <?php } ?>
            </ul>
        </div>
        <div class="panel-footer">
            <?php if ($this->showDetailLinks) { ?>
			<a href="<?php echo $this->controller->createUrl('campaign_reports/click', array('campaign_uid' => $campaign->campaign_uid));?>" class="btn green"><?php echo Yii::t('campaign_reports', 'View all clicks');?></a>
			<a href="<?php echo $this->controller->createUrl('campaign_reports/click', array('campaign_uid' => $campaign->campaign_uid, 'show' => 'latest'));?>" class="btn green"><?php echo Yii::t('campaign_reports', 'View latest clicks');?></a>
            <?php } ?>
            <div class="clearfix"><!-- --></div>
        </div>
    </div>
</div>