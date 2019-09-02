<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */
 
?>
<div id="ea-box-wrapper" data-success="<?php echo Yii::t('app', 'Your action completed successfully');?>" data-error="<?php echo Yii::t('app', 'Your action completed with errors');?>">
    <div class="portlet-title">
        <div class="caption">
			<span class="fa fa-warning"></span>
			<span class="caption-subject font-dark sbold uppercase">
                 <?php echo $pageHeading;?>
            </span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="alert alert-danger margin-bottom-15">
            <h4 class="block"><?php echo Yii::t('app', 'Please use with caution!');?></h4>
            <p><?php echo Yii::t('app', 'Please use bellow options only if you know what you are doing. The way your application works and behaves depends on these actions.');?></p>
			</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h5 class="panel-title"><?php echo Yii::t('app', 'Delivery PID');?></h5>
					</div>
					<div class="panel-body">
						<span>
							<?php echo Yii::t('app', 'Remove the PID for send-campaigns cron command!');?><br />
						</span>
						<a class="btn red-mint margin-top-15 remove-sending-pid" href="<?php echo $this->createUrl('misc/remove_sending_pid');?>" data-confirm="<?php echo Yii::t('app', 'Are you sure you need to run this action?');?>"><?php echo Yii::t('app', 'I understand, do it!');?></a>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo Yii::t('app', 'Bounce PID');?></h3>
					</div>
					<div class="panel-body">
						<span>
							<?php echo Yii::t('app', 'Remove the PID for bounce-handler cron command!');?><br />
						</span>
						<a class="btn red-mint margin-top-15 remove-bounce-pid" href="<?php echo $this->createUrl('misc/remove_bounce_pid');?>" data-confirm="<?php echo Yii::t('app', 'Are you sure you need to run this action?');?>"><?php echo Yii::t('app', 'I understand, do it!');?></a>					
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo Yii::t('app', 'Feedback loop PID');?></h3>
					</div>
					<div class="panel-body">
						<span>
							<?php echo Yii::t('app', 'Remove the PID for feedback-loop-handler cron command!');?><br />
						</span>
						<a class="btn red-mint margin-top-15 remove-fbl-pid" href="<?php echo $this->createUrl('misc/remove_fbl_pid');?>" data-confirm="<?php echo Yii::t('app', 'Are you sure you need to run this action?');?>"><?php echo Yii::t('app', 'I understand, do it!');?></a>                      
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo Yii::t('app', 'Campaign status');?></h3>
					</div>
					<div class="panel-body">
						<span>
							<?php echo Yii::t('app', 'Change the status of stuck campaigns from processing to sending!');?><br />
						</span>
						<a class="btn red-mint margin-top-15 reset-campaigns" href="<?php echo $this->createUrl('misc/reset_campaigns');?>" data-confirm="<?php echo Yii::t('app', 'Are you sure you need to run this action?');?>"><?php echo Yii::t('app', 'I understand, do it!');?></a>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo Yii::t('app', 'Bounce servers');?></h3>
					</div>
					<div class="panel-body">
						<span>
							<?php echo Yii::t('app', 'Change the status of stuck bounce servers from cron-running to active!');?><br />
						</span>
						<a class="btn red-mint margin-top-15 reset-bounce-servers" href="<?php echo $this->createUrl('misc/reset_bounce_servers');?>" data-confirm="<?php echo Yii::t('app', 'Are you sure you need to run this action?');?>"><?php echo Yii::t('app', 'I understand, do it!');?></a>
					</div>
				</div>
			</div>
			<div class="col-lg-4">
				<div class="panel panel-danger">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo Yii::t('app', 'Feedback loop servers');?></h3>
					</div>
					<div class="panel-body">
						<span>
							<?php echo Yii::t('app', 'Change the status of stuck feedback loop servers from cron-running to active!');?><br />
						</span>
						<a class="btn red-mint margin-top-15 reset-fbl-servers" href="<?php echo $this->createUrl('misc/reset_fbl_servers');?>" data-confirm="<?php echo Yii::t('app', 'Are you sure you need to run this action?');?>"><?php echo Yii::t('app', 'I understand, do it!');?></a>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>