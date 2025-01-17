<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * MiscController
 * 
 * Handles the actions for miscellaneous tasks
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3
 */
 
class MiscController extends Controller
{
    
    public function init()
    {
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('misc.js')));
        parent::init();
    }
    
    public function actionIndex()
    {
        $this->redirect(array('misc/application_log'));
    }
    
    /**
     * Emergency actions
     */
    public function actionEmergency_actions()
    {
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('app', 'Emergency actions'), 
            'pageHeading'       => Yii::t('app', 'Emergency actions'),
            'pageBreadcrumbs'   => array(
                Yii::t('app', 'Emergency actions'),
            ),
        ));
        
        $this->render('emergency-actions');
    }
    
    /**
     * Remove sending pid
     */
    public function actionRemove_sending_pid()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('misc/emergency_actions'));
        }
        Yii::app()->options->remove('system.cron.send_campaigns.lock');
        Yii::app()->options->set('system.cron.send_campaigns.campaigns_offset', 0);
        return $this->renderJson();
    }
    
    /**
     * Remove bounces pid
     */
    public function actionRemove_bounce_pid()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('misc/emergency_actions'));
        }
        Yii::app()->options->remove('system.cron.process_bounce_servers.pid');
        return $this->renderJson();
    }
    
    /**
     * Remove fbl pid
     */
    public function actionRemove_fbl_pid()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('misc/emergency_actions'));
        }
        Yii::app()->options->remove('system.cron.process_feedback_loop_servers.pid');
        return $this->renderJson();
    }
    
    /**
     * Reset campaigns
     */
    public function actionReset_campaigns()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('misc/emergency_actions'));
        }
        Campaign::model()->updateAll(array('status' => Campaign::STATUS_SENDING), 'status = :status', array(':status' => Campaign::STATUS_PROCESSING));
        return $this->renderJson();
    }
    
    /**
     * Reset bounce servers
     */
    public function actionReset_bounce_servers()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('misc/emergency_actions'));
        }
        BounceServer::model()->updateAll(array('status' => BounceServer::STATUS_ACTIVE), 'status = :status', array(':status' => BounceServer::STATUS_CRON_RUNNING));
        return $this->renderJson();
    }
    
    /**
     * Reset fbl servers
     */
    public function actionReset_fbl_servers()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('misc/emergency_actions'));
        }
        FeedbackLoopServer::model()->updateAll(array('status' => FeedbackLoopServer::STATUS_ACTIVE), 'status = :status', array(':status' => FeedbackLoopServer::STATUS_CRON_RUNNING));
        return $this->renderJson();
    }
    
    /**
     * Application log
     */
    public function actionApplication_log()
    {
        $request = Yii::app()->request;
        
        if ($request->isPostRequest && $request->getPost('delete') == 1) {
            if (is_file($file = Yii::app()->runtimePath . '/application.log')) {
                @unlink($file);
                Yii::app()->notify->addSuccess(Yii::t('app', 'The application log file has been successfully deleted!'));
            }
        }
        
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('app', 'Application log'), 
            'pageHeading'       => Yii::t('app', 'Application log'),
            'pageBreadcrumbs'   => array(
                Yii::t('app', 'Application log'),
            ),
        ));
        
        $applicationLog = FileSystemHelper::getFileContents(Yii::app()->runtimePath . '/application.log');
        $this->render('application-log', compact('applicationLog'));
    }
    
    /**
     * Campaign delivery logs
     */
    public function actionCampaigns_delivery_logs($archive = null)
    {
        $request   = Yii::app()->request;
        $className = $archive ? 'CampaignDeliveryLogArchive' : 'CampaignDeliveryLog';
        $log       = new $className('search');
        $log->unsetAttributes();
        
        $log->attributes = (array)$request->getQuery($log->modelName, array());

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('misc', 'View campaigns delivery logs'),
            'pageHeading'       => Yii::t('misc', 'View campaigns delivery logs'),
            'pageBreadcrumbs'   => array(
                Yii::t('misc', 'Campaigns delivery logs'),
            )
        ));
        
        $this->render('campaigns-delivery-logs', compact('log', 'archive'));
    }
    
    /**
     * Campaign bounce logs
     */
    public function actionCampaigns_bounce_logs()
    {
        $request = Yii::app()->request;
        $log     = new CampaignBounceLog('search');
        $log->unsetAttributes();
        
        $log->attributes = (array)$request->getQuery($log->modelName, array());

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('misc', 'View campaigns bounce logs'),
            'pageHeading'       => Yii::t('misc', 'View campaigns bounce logs'),
            'pageBreadcrumbs'   => array(
                Yii::t('misc', 'Campaigns bounce logs'),
            )
        ));
        
        $this->render('campaigns-bounce-logs', compact('log'));
    }
    
    /**
     * Delivery servers usage logs
     */
    public function actionDelivery_servers_usage_logs()
    {
        $request = Yii::app()->request;
        $log     = new DeliveryServerUsageLog('search');
        $log->unsetAttributes();
        
        $log->attributes = (array)$request->getQuery($log->modelName, array());

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('misc', 'View delivery servers usage logs'),
            'pageHeading'       => Yii::t('misc', 'View delivery servers usage logs'),
            'pageBreadcrumbs'   => array(
                Yii::t('misc', 'Delivery servers usage logs'),
            )
        ));
        
        $this->render('delivery-servers-usage-logs', compact('log'));
    }
    
    /**
     * Delete temporary errors from campaigns delivery logs
     */
    public function actionDelete_delivery_temporary_errors()
    {
        set_time_limit(0);
        ini_set('memory_limit', -1);
        
        $criteria = new CDbCriteria();
        $criteria->select = 'campaign_id';
        $criteria->compare('status', Campaign::STATUS_SENDING);
        $campaigns = Campaign::model()->findAll($criteria);
        
        foreach ($campaigns as $campaign) {
            CampaignDeliveryLog::model()->deleteAllByAttributes(array(
                'campaign_id' => $campaign->campaign_id, 
                'status'      => CampaignDeliveryLog::STATUS_TEMPORARY_ERROR
            ));
        }

        Yii::app()->notify->addSuccess(Yii::t('misc', 'Delivery temporary errors were successfully deleted!'));
        $this->redirect(array('misc/campaigns_delivery_logs'));
    }
    
    /**
     * Guest fail attempts
     */
    public function actionGuest_fail_attempts()
    {
        $request = Yii::app()->request;
        $attempt = new GuestFailAttempt('search');
        $attempt->unsetAttributes();
        
        $attempt->attributes = (array)$request->getQuery($attempt->modelName, array());

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('guest_fail_attempt', 'View guest fail attempts'),
            'pageHeading'       => Yii::t('guest_fail_attempt', 'View guest fail attempts'),
            'pageBreadcrumbs'   => array(
                Yii::t('guest_fail_attempt', 'Guest fail attempts'),
            )
        ));
        
        $this->render('guest-fail-attempts', compact('attempt'));
    }
    
    /**
     * Cron jobs display list
     */
    public function actionCron_jobs_list()
    {
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('cronjobs', 'View cron jobs list'),
            'pageHeading'       => Yii::t('cronjobs', 'View cron jobs list'),
            'pageBreadcrumbs'   => array(
                Yii::t('cronjobs', 'Cron jobs list'),
            )
        ));
        
        $this->render('cron-jobs-list');
    }
    
    /**
     * Display informations about the current php version
     */
    public function actionPhpinfo()
    {
        if (Yii::app()->request->getQuery('show')) {
            if (CommonHelper::functionExists('phpinfo')) {
                phpinfo();
            }
            Yii::app()->end();
        }
        
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('misc', 'View PHP info'),
            'pageHeading'       => Yii::t('misc', 'View PHP info'),
            'pageBreadcrumbs'   => array(
                Yii::t('misc', 'PHP info'),
            )
        ));
        
        $this->render('php-info');
    }

}