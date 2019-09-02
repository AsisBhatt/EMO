<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Console application main configuration file
 *
 * This file should not be altered in any way!
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

return array(
    'basePath' => Yii::getPathOfAlias('console'),

    'preload' => array(
        'consoleSystemInit'
    ),

    'import' => array(
        'console.components.*',
        'console.components.db.*',
        'console.components.db.ar.*',
        'console.components.web.*',
        'console.components.console.*',
		'common.models.*',
        
    ),

    'commandMap' => array(
        'migrate' => array(
            'class'             => 'system.cli.commands.MigrateCommand',
            'migrationPath'     => 'console.migrations',
            'migrationTable'    => '{{migration}}',
            'connectionID'      => 'db',
        ),
        'hello' => array(
            'class' => 'console.commands.HelloCommand'
        ),
        'test' => array(
            'class' => 'console.commands.TestCommand'
        ),		
        'send-campaigns' => array(
            'class' => 'console.commands.SendCampaignsCommand'
        ),
        'bounce-handler' => array(
            'class' => 'console.commands.BounceHandlerCommand'
        ),
        'process-delivery-and-bounce-log' => array(
            'class' => 'console.commands.ProcessDeliveryAndBounceLogCommand'
        ),
        // this command is deprecated since 1.3.3.1 in favor of daily command
        'process-subscribers' => array(
            'class' => 'console.commands.ProcessSubscribersCommand'
        ),
        'option' => array(
            'class' => 'console.commands.OptionCommand'
        ),
        'feedback-loop-handler' => array(
            'class' => 'console.commands.FeedbackLoopHandlerCommand'
        ),
        'send-transactional-emails' => array(
            'class' => 'console.commands.SendTransactionalEmailsCommand'
        ),
        'daily' => array(
            'class' => 'console.commands.DailyCommand'
        ),
		'sms-reply' => array(
			'class' => 'console.commands.SmsReplyCommand'
		),
		'auto-join' => array(
			'class' => 'console.commands.AutoJoinCommand'
		),
		'sms-stop-counter' => array(
			'class' => 'console.commands.SmsStopCounterCommand'
		),
		'sms-schedule' => array(
			'class' => 'console.commands.SmsScheduleCommand'
		),
		'mms-schedule' => array(
			'class' => 'console.commands.MmsScheduleCommand'
		),		
        'update' => array(
            'class' => 'console.commands.UpdateCommand'
        ),
        'archive-campaigns-delivery-logs' => array(
            'class' => 'console.commands.ArchiveCampaignsDeliveryLogsCommand'
        ),
        'queue' => array(
            'class' => 'console.commands.RedisQueueCommand'
        ),
        'list-import' => array(
            'class' => 'console.commands.ListImportCommand'
        ),
        'list-export' => array(
            'class' => 'console.commands.ListExportCommand'
        ),
        'mailerq-handler-daemon' => array(
            'class' => 'console.commands.MailerqHandlerDaemon'
        ),
        'table-cleaner' => array(
            'class' => 'console.commands.TableCleanerCommand'
        ),
        'clear-cache' => array(
            'class' => 'console.commands.ClearCacheCommand'
        ),
        'translate' => array(
            'class' => 'console.commands.TranslateCommand'
        ),
        'email-blacklist-monitor' => array(
            'class' => 'console.commands.EmailBlacklistMonitorCommand'
        ),
        'reset-customers-quota' => array(
            'class' => 'console.commands.ResetCustomersQuotaCommand'
        ),
        'move-inactive-subscribers' => array(
            'class' => 'console.commands.MoveInactiveSubscribersCommand'
        ),
        'delete-inactive-subscribers' => array(
            'class' => 'console.commands.DeleteInactiveSubscribersCommand'
        ),
    ),

    'components' => array(
        'consoleSystemInit' => array(
            'class' => 'console.components.init.ConsoleSystemInit',
        ),
    ),
);
