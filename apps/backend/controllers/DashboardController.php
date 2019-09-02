<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * DashboardController
 *
 * Handles the actions for dashboard related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class DashboardController extends Controller
{
    public function init()
    {
        $apps = Yii::app()->apps;
        $this->getData('pageScripts')->mergeWith(array(
            array('src' => $apps->getBaseUrl('assets/js/flot/jquery.flot.min.js')),
            array('src' => $apps->getBaseUrl('assets/js/flot/jquery.flot.resize.min.js')),
            array('src' => $apps->getBaseUrl('assets/js/flot/jquery.flot.categories.min.js')),
			array('src' => $apps->getBaseUrl('customer/assets/js/datetimepicker/css/bootstrap-datetimepicker.min.css')),
			array('src' => $apps->getBaseUrl('customer/assets/js/datetimepicker/js/bootstrap-datetimepicker.min.js')),
            array('src' => AssetsUrl::js('dashboard.js'))
        ));
        parent::init();
    }

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
        return CMap::mergeArray(array(
            'postOnly + delete_log, delete_logs',
        ), parent::filters());
    }

    /**
     * Display dashboard informations
     */
    public function actionIndex()
    {
         //die(Yii::app()->notify->show());
		 
		$options = Yii::app()->options;
        $notify  = Yii::app()->notify;
		
		$oDbConnection = Yii::app()->db; 
		$sens_sms_count = $oDbConnection->createCommand('SELECT count(sms_id) FROM uic_sms WHERE status="Sent"')->queryScalar();
		
		$notsend_sms_count = $oDbConnection->createCommand('SELECT count(sms_id) FROM uic_sms WHERE status!="Sent"')->queryScalar();
		
		$first_date = date('Y-m-d',strtotime('first day of this month'));
		$last_date = date('Y-m-d',strtotime('last day of this month'));
		
		$str_whr = '';
		if (Yii::app()->request->isPostRequest) 
		{
			$date_start = date('Y-m-d',strtotime(Yii::app()->request->getPost('frm_date')));
			$date_end = date('Y-m-d',strtotime(Yii::app()->request->getPost('to_date')));
			
			if($date_start !='' && $date_end != ''){
				$str_whr .= " date_added BETWEEN '".$date_start."' AND '".$date_end."'";
			}
		}else{
			$str_whr .= " date_added BETWEEN '".$first_date."' AND '".$last_date."'";
		}
		
		$query_command = Yii::app()->db->createCommand("SELECT date_added as date, count(sms_id) as sms_count, status FROM uic_sms WHERE ".$str_whr. " AND status='sent' Group By DATE(date_added) UNION SELECT date_added as date, count(sms_id) as sms_count, status FROM uic_sms WHERE ".$str_whr." AND status <> 'sent' Group By DATE(date_added)")->queryAll();
		
		$date_array = array();
		foreach($query_command as $sms_data){
			$date_array['date'][] = date('Y-m-d',strtotime($sms_data['date']));
			$date_array['send_count'][]= ($sms_data['status'] == 'sent' ? (int)$sms_data['sms_count'] : 0);
			$date_array['notsend_count'][]= ($sms_data['status'] != 'sent' ? (int)$sms_data['sms_count'] : 0);
		}
        
        if (file_exists(Yii::getPathOfAlias('root.install')) && is_dir($dir = Yii::getPathOfAlias('root.install'))) {
            $notify->addWarning(Yii::t('app', 'Please remove the install directory({dir}) from your application!', array(
                '{dir}' => $dir,
            )));
        }

        // since 1.3.6.3
        if ($options->get('system.installer.freshinstallextensionscheck', 0) == 0) {
            $options->set('system.installer.freshinstallextensionscheck', 1);
            
            $notify->clearAll()->addInfo(Yii::t('extensions', 'Conducting extensions checks for the fresh install...'));
            
            $manager    = Yii::app()->extensionsManager;
            $extensions = $manager->getCoreExtensions();
            $errors     = array();
            foreach ($extensions as $id => $instance) {
                if ($manager->extensionMustUpdate($id) && !$manager->updateExtension($id)) {
                    $errors[] = Yii::t('extensions', 'The extension "{name}" has failed to update!', array(
                        '{name}' => CHtml::encode($instance->name),
                    ));
                    $errors = CMap::mergeArray($errors, (array)$manager->getErrors());
                    $manager->resetErrors();
                }
            }
            
            if (!empty($errors)) {
                $notify->addError($errors);
            } else {
                $notify->addSuccess(Yii::t('extensions', 'All extension checks were conducted successfully.'));
            }
            
            // enable the tour extension which has been added in 1.3.6.3
            if (Yii::app()->extensionsManager->enableExtension('tour')) {
                Yii::app()->extensionsManager->getExtensionInstance('tour')->setOption('enabled', 'yes');
            }
            
            $this->redirect(array('dashboard/index'));
        }
        //

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'Dashboard'),
            'pageHeading'       => Yii::t('dashboard', 'Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Dashboard'),
            ),
        ));

        //$checkVersionUpdate = Yii::app()->options->get('system.common.check_version_update', 'yes') == 'yes';
        //$this->render('index', compact('checkVersionUpdate'));
        $this->render('index', compact(0,'date_array','sens_sms_count', 'notsend_sms_count'));
    }

    /**
     * Ajax only action to get one year subscribers growth
     */
    public function actionGlance()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }

        $customers          = Customer::model()->count();
        $lists              = Lists::model()->count();
        $subscribers        = ListSubscriber::model()->count(array('select' => 'COUNT(DISTINCT(email)) AS counter'));
        $allSubscribers     = ListSubscriber::model()->count();
        $deliveryServers    = DeliveryServer::model()->count();
        $campaigns          = Campaign::model()->count();
        $segments           = ListSegment::model()->count();

        $customers          = Yii::app()->format->formatNumber($customers);
        $lists              = Yii::app()->format->formatNumber($lists);
        $subscribers        = Yii::app()->format->formatNumber($subscribers);
        $allSubscribers     = Yii::app()->format->formatNumber($allSubscribers);
        $deliveryServers    = Yii::app()->format->formatNumber($deliveryServers);
        $campaigns          = Yii::app()->format->formatNumber($campaigns);
        $segments           = Yii::app()->format->formatNumber($segments);

        return $this->renderJson(compact(
            'customers',
            'lists',
            'subscribers',
            'allSubscribers',
            'deliveryServers',
            'campaigns',
            'segments'
        ));
    }

    /**
     * Ajax only action to get activity messages
     */
    public function actionChatter()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }

        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) as date_added';
        $criteria->condition = 'DATE(t.date_added) >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
        $criteria->group     = 'DATE(t.date_added)';
        $criteria->order     = 't.date_added DESC';
        $criteria->limit     = 7;
        $models = CustomerActionLog::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $_item = array(
                'date'  => $model->dateTimeFormatter->formatLocalizedDate($model->date_added),
                'items' => array(),
            );
            $criteria = new CDbCriteria();
            $criteria->select    = 't.log_id, t.customer_id, t.message, t.date_added';
            $criteria->condition = 'DATE(t.date_added) = :date';
            $criteria->params    = array(':date' => $model->date_added);
            $criteria->limit     = 10;
            $criteria->order     = 't.date_added DESC';
            $criteria->with      = array(
                'customer' => array(
                    'select'   => 'customer.customer_id, customer.first_name, customer.last_name',
                    'together' => true,
                    'joinType' => 'INNER JOIN',
                ),
            );
            $records = CustomerActionLog::model()->findAll($criteria);
            foreach ($records as $record) {
                $customer = $record->customer;
                $time     = $record->dateTimeFormatter->formatLocalizedTime($record->date_added);
                $_item['items'][] = array(
                    'deleteUrl'    => $this->createUrl('dashboard/delete_log', array('id' => $record->log_id)),
                    'time'         => $time,
                    'customerName' => $customer->getFullName(),
                    'customerUrl'  => $this->createUrl('customers/update', array('id' => $customer->customer_id)),
                    'message'      => strip_tags($record->message),
                );
            }
            $items[] = $_item;
        }

        return $this->renderJson($items);
    }

    /**
     * Ajax only action to get subscribers growth
     */
    public function actionSubscribers_growth()
    {
        set_time_limit(0);

        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }

        $cacheKey = md5(__FILE__ . __METHOD__);
        if ($items = Yii::app()->cache->get($cacheKey)) {
            return $this->renderJson(array(
                'label' => Yii::t('app', '{n} months growth', 3),
                'data'  => $items,
                'color' => '#3c8dbc'
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) AS date_added';
        $criteria->condition = 'DATE(t.date_added) >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 3 MONTH)), INTERVAL 1 DAY)';
        $criteria->group     = 'MONTH(t.date_added)';
        $criteria->order     = 't.date_added ASC';
        $criteria->limit     = 3;

        $models = ListSubscriber::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->select    = 'COUNT(DISTINCT(email)) as counter';
            $criteria->condition = 'YEAR(date_added) = YEAR(:year) AND MONTH(date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $monthName  = date('M', strtotime($model->date_added));
            $count      = ListSubscriber::model()->count($criteria);
            $items[]    = array(Yii::t('app', $monthName) . ' ' . date('Y', strtotime($model->date_added)), $count);
        }

        Yii::app()->cache->set($cacheKey, $items, 3600);

        return $this->renderJson(array(
            'label' => Yii::t('app', '{n} months growth', 3),
            'data'  => $items,
            'color' => '#3c8dbc'
        ));
    }

    /**
     * Ajax only action to get lists growth
     */
    public function actionLists_growth()
    {
        set_time_limit(0);

        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }

        $cacheKey = md5(__FILE__ . __METHOD__);
        if ($items = Yii::app()->cache->get($cacheKey)) {
            return $this->renderJson(array(
                'label' => Yii::t('app', '{n} months growth', 3),
                'data'  => $items,
                'color' => '#3c8dbc'
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) AS date_added';
        $criteria->condition = 'DATE(t.date_added) >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 3 MONTH)), INTERVAL 1 DAY)';
        $criteria->group     = 'MONTH(t.date_added)';
        $criteria->order     = 't.date_added ASC';
        $criteria->limit     = 3;
        $models = Lists::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'YEAR(date_added) = YEAR(:year) AND MONTH(date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $monthName  = date('M', strtotime($model->date_added));
            $count      = Lists::model()->count($criteria);
            $items[]    = array(Yii::t('app', $monthName) . ' ' . date('Y', strtotime($model->date_added)), $count);
        }

        Yii::app()->cache->set($cacheKey, $items, 3600);

        return $this->renderJson(array(
            'label' => Yii::t('app', '{n} months growth', 3),
            'data'  => $items,
            'color' => '#3c8dbc'
        ));
    }

    /**
     * Ajax only action to get campaigns growth
     */
    public function actionCampaigns_growth()
    {
        set_time_limit(0);

        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }

        $cacheKey = md5(__FILE__ . __METHOD__);
        if ($items = Yii::app()->cache->get($cacheKey)) {
            return $this->renderJson(array(
                'label' => Yii::t('app', '{n} months growth', 3),
                'data'  => $items,
                'color' => '#3c8dbc'
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) AS date_added';
        $criteria->condition = 'DATE(t.date_added) >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 3 MONTH)), INTERVAL 1 DAY)';
        $criteria->group     = 'MONTH(t.date_added)';
        $criteria->order     = 't.date_added ASC';
        $criteria->limit     = 3;

        $models = Campaign::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'YEAR(date_added) = YEAR(:year) AND MONTH(date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $monthName  = date('M', strtotime($model->date_added));
            $count      = Campaign::model()->count($criteria);
            $items[]    = array(Yii::t('app', $monthName) . ' ' . date('Y', strtotime($model->date_added)), $count);
        }

        Yii::app()->cache->set($cacheKey, $items, 3600);

        return $this->renderJson(array(
            'label' => Yii::t('app', '{n} months growth', 3),
            'data'  => $items,
            'color' => '#3c8dbc'
        ));
    }

    /**
     * Ajax only action to get delivery/bounce growth
     */
    public function actionDelivery_bounce_growth()
    {
        set_time_limit(0);

        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }

        $cacheKey = md5(__FILE__ . __METHOD__);
        if ($lines = Yii::app()->cache->get($cacheKey)) {
            return $this->renderJson($lines);
        }

        $lines = array();

        // Delivery
        $cdlModel = !CampaignDeliveryLog::getArchiveEnabled() ? CampaignDeliveryLog::model() : CampaignDeliveryLogArchive::model();
        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) AS date_added';
        $criteria->condition = 'DATE(t.date_added) >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 3 MONTH)), INTERVAL 1 DAY)';
        $criteria->group     = 'MONTH(t.date_added)';
        $criteria->order     = 't.date_added ASC';
        $criteria->limit     = 3;
        $models = $cdlModel->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'YEAR(date_added) = YEAR(:year) AND MONTH(date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $monthName  = date('M', strtotime($model->date_added));
            $count      = $cdlModel->count($criteria);
            $items[]    = array(Yii::t('app', $monthName) . ' ' . date('Y', strtotime($model->date_added)), $count);
        }

        $lines[] = array(
            'label' => Yii::t('app', 'Delivery, {n} months growth', 3),
            'data'  => $items,
            'color' => '#3c8dbc'
        );

        // Bounces
        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) AS date_added';
        $criteria->condition = 'DATE(t.date_added) >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 3 MONTH)), INTERVAL 1 DAY)';
        $criteria->group     = 'MONTH(t.date_added)';
        $criteria->order     = 't.date_added ASC';
        $criteria->limit     = 3;
        $models = CampaignBounceLog::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'YEAR(date_added) = YEAR(:year) AND MONTH(date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $monthName  = date('M', strtotime($model->date_added));
            $count      = CampaignBounceLog::model()->count($criteria);
            $items[]    = array(Yii::t('app', $monthName) . ' ' . date('Y', strtotime($model->date_added)), $count);
        }

        $lines[] = array(
            'label' => Yii::t('app', 'Bounce, {n} months growth', 3),
            'data'  => $items,
            'color' => '#ff0000'
        );

        Yii::app()->cache->set($cacheKey, $lines, 3600);

        return $this->renderJson($lines);
    }

    /**
     * Ajax only action to get unsubscribes growth
     */
    public function actionUnsubscribe_growth()
    {
        set_time_limit(0);

        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }

        $cacheKey = md5(__FILE__ . __METHOD__);
        if ($items = Yii::app()->cache->get($cacheKey)) {
            return $this->renderJson(array(
                'label' => Yii::t('app', '{n} months growth', 3),
                'data'  => $items,
                'color' => '#3c8dbc'
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) AS date_added';
        $criteria->condition = 'DATE(t.date_added) >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 3 MONTH)), INTERVAL 1 DAY)';
        $criteria->group     = 'MONTH(t.date_added)';
        $criteria->order     = 't.date_added ASC';
        $criteria->limit     = 3;
        $models = CampaignTrackUnsubscribe::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'YEAR(date_added) = YEAR(:year) AND MONTH(date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $monthName  = date('M', strtotime($model->date_added));
            $count      = CampaignTrackUnsubscribe::model()->count($criteria);
            $items[]    = array(Yii::t('app', $monthName) . ' ' . date('Y', strtotime($model->date_added)), $count);
        }

        Yii::app()->cache->set($cacheKey, $items, 3600);

        return $this->renderJson(array(
            'label' => Yii::t('app', '{n} months growth', 3),
            'data'  => $items,
            'color' => '#3c8dbc'
        ));
    }

    /**
     * Delete a single action log
     */
    public function actionDelete_log($id)
    {
        $model = CustomerActionLog::model()->findByAttributes(array(
            'log_id' => $id,
        ));

        if ($model === null) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $model->delete();

        $request = Yii::app()->request;
        $notify = Yii::app()->notify;

        if (!$request->isAjaxRequest) {
            $notify->addSuccess(Yii::t('app', 'Your item has been successfully deleted!'));
            $this->redirect($request->getPost('returnUrl', array('dashboard/index')));
        }
    }

    /**
     * Delete all action logs
     */
    public function actionDelete_logs()
    {
        CustomerActionLog::model()->deleteAll();

        $request = Yii::app()->request;
        $notify = Yii::app()->notify;

        if (!$request->isAjaxRequest) {
            $notify->addSuccess(Yii::t('app', 'Your items have been successfully deleted!'));
            $this->redirect($request->getPost('returnUrl', array('dashboard/index')));
        }
    }

    public function actionCheck_update()
    {
        ignore_user_abort(true);

        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }

        $options = Yii::app()->options;
        if ($options->get('system.common.enable_version_update_check', 'yes') == 'no') {
            Yii::app()->end();
        }

        $now        = time();
        $lastCheck  = (int)$options->get('system.common.version_update.last_check', 0);
        $interval   = 60 * 60 * 24; // once at 24 hours should be enough

        if ($lastCheck + $interval > $now) {
            Yii::app()->end();
        }

        $options->set('system.common.version_update.last_check', $now);

		
        /*
		$response = AppInitHelper::simpleCurlGet('http://www.mailwizz.com/api/site/version');
        if (empty($response) || $response['status'] == 'error') {
            Yii::app()->end();
        }

        $json = CJSON::decode($response['message']);
        if (empty($json['current_version'])) {
            Yii::app()->end();
        }
		*/

		$json['current_version'] = '';		
		
        $dbVersion = $options->get('system.common.version', '1.0');
        if (version_compare($json['current_version'], $dbVersion, '>')) {
            $options->set('system.common.version_update.current_version', $json['current_version']);
        }

        Yii::app()->end();
    }
	
	public function actionEmaildashboard(){
		$options = Yii::app()->options;
        $notify  = Yii::app()->notify;
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'Email Dashboard'),
			'pageHeading'       => Yii::t('dashboard', 'Email Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Email Dashboard'),
            ),
        ));
		
		$this->render('email_dashboard', compact());
	}
	
	public function actionSmsdashboard(){
		
		$options = Yii::app()->options;
        $notify  = Yii::app()->notify;
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'SMS Dashboard'),
			'pageHeading'       => Yii::t('dashboard', 'SMS Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'SMS Dashboard'),
            ),
        ));
		
		$this->render('sms_dashboard', compact());
	}
	
	public function actionSocialdashboard(){
		$options = Yii::app()->options;
        $notify  = Yii::app()->notify;
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'Social Media Dashboard'),
			'pageHeading'       => Yii::t('dashboard', 'Social Media Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Social Media Dashboard'),
            ),
        ));
		
		$this->render('social_dashboard', compact());
	}

}
