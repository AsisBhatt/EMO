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
            array('src' => AssetsUrl::js('dashboard.js')),
			array('src' => AssetsUrl::js('Chart.min.js'))
		));
		$baseUrl = Yii::app()->baseUrl; 
		$cs = Yii::app()->getClientScript();
		$cs->registerCssFile($baseUrl.'/assets/css/dataTables.bootstrap.min.css');
		$cs->registerScriptFile($baseUrl.'/assets/js/jquery.dataTables.min.js');
		$cs->registerScriptFile($baseUrl.'/assets/js/dataTables.bootstrap.min.js');
        parent::init();
    }

    /**
     * Display dashboard informations
     */
    public function actionIndex()
    {
		$customer       = Yii::app()->customer->getModel(); 
		$reserve_count = $this->getPendingListCount($customer->customer_id);
		
		
		//Get Current Flowrote number From Company Information
		$customer_company = CustomerCompany::model()->findByAttributes(array('customer_id' => $customer->customer_id));
		
		//Get Stop Count From SMS Reply Table
		$sms_rply_stop_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM uic_sms_rply WHERE customer_id='".$customer->customer_id."' AND sms_rply_to_number='".$customer_company->flowroute_sms_num."' AND sms_rply_body LIKE '%STOP%'")->queryScalar();
		
		
		//echo (int)$customer->getGroupOption('sending.quota', -1);
        //print_r($customer->group);exit;
		//$customer_reserve_quota = Yii::app()->createCommand("SELECT MIN(remaining_quota) as remaining FROM uic_sms_campaign_quota WHERE ")->queryRow();
		
		$oDbConnection = Yii::app()->db; 
		$sens_sms_count = $oDbConnection->createCommand("SELECT count(sms_id) FROM uic_sms WHERE customer_id ='".$customer->customer_id."' AND status LIKE '%sent%'")->queryScalar();
		
		
		$notsend_sms_count = $oDbConnection->createCommand("SELECT count(sms_id) FROM uic_sms WHERE customer_id ='".$customer->customer_id."' AND status NOT LIKE '%sent%'")->queryScalar();
		//echo 'Send Count'.$sens_sms_count.'<br/>';
		//echo $notsend_sms_count;exit;
		
		
		if((int)$customer->getGroupOption('smssending.sms_quota', -1) != -1){
			$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
		}else{
			$rem_quota = 'UNLIMITED';
		}
		
		
		$str_whr = '';
		if (Yii::app()->request->isPostRequest) 
		{
			$date_start = date('Y-m-d',strtotime($request->getPost('frm_date')));
			$date_end = date('Y-m-d',strtotime($request->getPost('to_date')));
			
			if($date_start !='' && $date_end != ''){
				$str_whr .= " date_added BETWEEN '".$date_start."' AND '".$date_end."' AND ";
			}
		}
		$str_whr .= " customer_id = '".$customer->customer_id."'";
		//echo "SELECT date_added as date, count(sms_id) as sms_count, status FROM uic_sms WHERE ".$str_whr." Group By DATE(date_added)";exit;
		//echo "SELECT date_added as date, count(sms_id) as sms_count, status FROM uic_sms WHERE ".$str_whr. " AND status LIKE '%sent%' Group By DATE(date_added) UNION SELECT date_added as date, count(sms_id) as sms_count, status FROM uic_sms WHERE ".$str_whr." AND status NOT LIKE '%sent%' Group By DATE(date_added)";exit;
		$query_command = Yii::app()->db->createCommand("SELECT date_added as date, count(sms_id) as sms_count, status FROM uic_sms WHERE ".$str_whr. " AND status LIKE '%sent%' Group By DATE(date_added) UNION SELECT date_added as date, count(sms_id) as sms_count, status FROM uic_sms WHERE ".$str_whr." AND status NOT LIKE '%sent%' Group By DATE(date_added)")->queryAll();
			
		$date_array = array();
		foreach($query_command as $sms_data){
			// echo '<pre>';
			// print_r($sms_data);
			// echo '<pre/>';
			$date_array['date'][] = date('Y-m-d',strtotime($sms_data['date']));
			$date_array['send_count'][]= (strpos(strtolower($sms_data['status']), strtolower('Sent')) ? (int)$sms_data['sms_count'] : 0);
			$date_array['notsend_count'][]= (strpos(strtolower($sms_data['status']), strtolower('Sent')) == false ? (int)$sms_data['sms_count'] : 0);
		}
		//print_r($date_array);exit;

		$send_sms = Yii::app()->db->createCommand("SELECT * FROM uic_sms WHERE".$str_whr)->queryAll();
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'Dashboard'),
            'pageHeading'       => Yii::t('dashboard', 'Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Dashboard'),
            ),
        ));

        $canSegmentList = Yii::app()->customer->getModel()->getGroupOption('lists.can_segment_lists', 'yes') == 'yes';

        $this->render('index', compact('canSegmentList','pricePlans', 'customer','reserve_count','date_array','sens_sms_count', 'notsend_sms_count', 'rem_quota','send_sms','sms_rply_stop_count'));
    }
	
	public function actionSpiltsubscriber(){
		$customer = Yii::app()->customer->getModel();        
		
		$criteria = new CDbCriteria();
		$criteria->select = 'count(field_value.value_id)';
		$criteria->addCondition('tag="EMAIL"');
		$criteria->addCondition('field_value.value !=""');
		$criteria->join="INNER JOIN uic_list_field_value as field_value ON(field_value.field_id=t.field_id)";
		$criteria->addInCondition('t.list_id', $customer->getAllListsIds());
		$email_count = ListField::model()->count($criteria);
		echo $email_count;exit;
		echo count($field_array);exit;
		
		
	}

    /**
     * Ajax only action to get one year subscribers growth
     */
    public function actionGlance()
    {
        if (!Yii::app()->request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }

        $customer    = Yii::app()->customer->getModel();
        $customer_id = (int)$customer->customer_id;

        $criteria = new CDbCriteria();
        $criteria->compare('customer_id', $customer_id);
        $criteria->addNotInCondition('status', array(Lists::STATUS_PENDING_DELETE));

        $lists = Lists::model()->count($criteria);
        $lists = Yii::app()->format->formatNumber($lists);

        $templates = CustomerEmailTemplate::model()->countByAttributes(array('customer_id' => $customer_id));
        $templates = Yii::app()->format->formatNumber($templates);

        $apiKeys = CustomerApiKey::model()->countByAttributes(array('customer_id' => $customer_id));
        $apiKeys = Yii::app()->format->formatNumber($apiKeys);

        // count unique subscribers.
        /* $criteria = new CDbCriteria();
        $criteria->select = 'COUNT(DISTINCT(t.email)) as counter';
        $criteria->addInCondition('t.list_id', $customer->getAllListsIds());
        $subscribers = Yii::app()->format->formatNumber(ListSubscriber::model()->count($criteria)); */
		// count unique subscribers.
		
        $criteria = new CDbCriteria();
        $criteria->select = 'DISTINCT(t.email),t.subscriber_id, t.mobile';
        $criteria->addInCondition('t.list_id', $customer->getAllListsIds());
        $getsubscribers = ListSubscriber::model()->findAll($criteria);
		
		$get_field_array = array();
		foreach($getsubscribers as $sub_key => $sub_val){
			$check_subscriber = Yii::app()->db->createCommand("SELECT DISTINCT(value), field_id, subscriber_id FROM uic_list_field_value WHERE subscriber_id = '".$sub_val->subscriber_id."'")->queryAll();
			
			foreach($check_subscriber as $subscriber_key => $subscriber_val){
				
				$get_field_name = Yii::app()->db->createCommand("SELECT * FROM uic_list_field WHERE field_id ='".$subscriber_val['field_id']."'")->queryRow();
				
				if($subscriber_val['field_id'] == $get_field_name['field_id']){
					$get_field_array[$subscriber_val['subscriber_id']][$get_field_name['tag']] = $subscriber_val['value'];
				}
			}
		}
		$get_field_array = array_map("unserialize", array_unique(array_map("serialize", $get_field_array)));
		foreach($get_field_array as $get_field){
			//print_r($get_field);
			if($get_field['EMAIL'] != '' && $get_field['MOBILE'] != ''){
				$get_field_array['em'][] = $get_field;
			}else if($get_field['EMAIL'] == '' && $get_field['MOBILE'] != ''){
				$get_field_array['mo'][] = $get_field;
			}
		}
		$subscribers = (count($get_field_array['em']) + count($get_field_array['mo']));
		//$subscribers = count($get_field_array);

        // count all subscribers.
        $criteria = new CDbCriteria();
        $criteria->addInCondition('t.list_id', $customer->getAllListsIds());
        $allSubscribers = Yii::app()->format->formatNumber(ListSubscriber::model()->count($criteria));
		
		//count all Email Subscriber
		$criteria = new CDbCriteria();
		$criteria->select = 'count(field_value.value_id)';
		$criteria->addCondition('tag="EMAIL"');
		$criteria->addCondition('field_value.value !=""');
		$criteria->join="INNER JOIN uic_list_field_value as field_value ON(field_value.field_id=t.field_id)";
		$criteria->addCondition('tag="EMAIL"');
		$criteria->addInCondition('t.list_id', $customer->getAllListsIds());
		$email_count = ListField::model()->count($criteria);
		
		//count all Mobile Subscriber
		$criteria = new CDbCriteria();
		$criteria->select = 'count(field_value.value_id)';
		$criteria->addCondition('tag="MOBILE"');
		$criteria->addCondition('field_value.value !=""');
		$criteria->join="INNER JOIN uic_list_field_value as field_value ON(field_value.field_id=t.field_id)";
		$criteria->addInCondition('t.list_id', $customer->getAllListsIds());
		$mobile_count = ListField::model()->count($criteria);

        // count campaigns
        $criteria = new CDbCriteria();
        $criteria->compare('customer_id', (int)$customer_id);
        $criteria->addNotInCondition('status', array(Campaign::STATUS_PENDING_DELETE));

        $campaigns = Campaign::model()->count($criteria);
        $campaigns = Yii::app()->format->formatNumber($campaigns);

        $segments = 0;
        if ($customer->getGroupOption('lists.can_segment_lists', 'yes') == 'yes') {
            // count segments
            $criteria = new CDbCriteria();
            $criteria->with = array(
                'list'  => array(
                    'select'    => false,
                    'together'  => true,
                    'joinType'  => 'INNER JOIN',
                    'condition' => 'list.customer_id = :customer_id AND list.status != :st',
                    'params'    => array(':customer_id' => $customer_id, ':st' => Lists::STATUS_PENDING_DELETE)
                ),
            );

            $segments = ListSegment::model()->count($criteria);
            $segments = Yii::app()->format->formatNumber($segments);
        }

        return $this->renderJson(compact(
            'lists',
            'templates',
            'apiKeys',
            'subscribers',
            'allSubscribers',
            'campaigns',
            'segments',
			'email_count',
			'mobile_count'
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

        $customer_id = (int)Yii::app()->customer->getId();

        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) as date_added';
        $criteria->condition = 't.customer_id = :customer_id AND DATE(t.date_added) >= DATE_SUB(NOW(), INTERVAL 7 DAY)';
        $criteria->group     = 'DATE(t.date_added)';
        $criteria->order     = 't.date_added DESC';
        $criteria->limit     = 7;
        $criteria->params    = array(':customer_id' => $customer_id);
        $models = CustomerActionLog::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $_item = array(
                'date'  => $model->dateTimeFormatter->formatLocalizedDate($model->date_added),
                'items' => array(),
            );
            $criteria = new CDbCriteria();
            $criteria->select    = 't.log_id, t.customer_id, t.message, t.date_added';
            $criteria->condition = 't.customer_id = :customer_id AND DATE(t.date_added) = :date';
            $criteria->params    = array(':customer_id' => $customer_id, ':date' => $model->date_added);
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
                    'time'         => $time,
                    'customerName' => $customer->getFullName(),
                    'customerUrl'  => $this->createUrl('account/index'),
                    'message'      => $record->message,
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

        $customer_id = (int)Yii::app()->customer->getId();
        $cacheKey    = md5(__FILE__ . __METHOD__ . $customer_id);
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

        $criteria->with = array(
            'list' => array(
                'select'    => false,
                'together'  => true,
                'joinType'  => 'INNER JOIN',
                'condition' => 'list.customer_id = :customer_id AND list.status != :st',
                'params'    => array(':customer_id' => (int)$customer_id, ':st' => Lists::STATUS_PENDING_DELETE)
            ),
        );

        $models = ListSubscriber::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->select    = 'COUNT(DISTINCT(email)) as counter';
            $criteria->condition = 'YEAR(t.date_added) = YEAR(:year) AND MONTH(t.date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $criteria->with = array(
                'list' => array(
                    'select'    => false,
                    'together'  => true,
                    'joinType'  => 'INNER JOIN',
                    'condition' => 'list.customer_id = :customer_id AND list.status != :st',
                    'params'    => array(':customer_id' => (int)$customer_id, ':st' => Lists::STATUS_PENDING_DELETE)
                ),
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

        $customer_id = (int)Yii::app()->customer->getId();
        $cacheKey    = md5(__FILE__ . __METHOD__ . $customer_id);
        if ($items = Yii::app()->cache->get($cacheKey)) {
            return $this->renderJson(array(
                'label' => Yii::t('app', '{n} months growth', 3),
                'data'  => $items,
                'color' => '#3c8dbc'
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) AS date_added';
        $criteria->condition = 't.customer_id = :customer_id AND t.status != :st AND DATE(t.date_added) >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 3 MONTH)), INTERVAL 1 DAY)';
        $criteria->group     = 'MONTH(t.date_added)';
        $criteria->order     = 't.date_added ASC';
        $criteria->limit     = 3;
        $criteria->params    = array(':customer_id' => $customer_id, ':st' => Lists::STATUS_PENDING_DELETE);

        $models = Lists::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 't.customer_id = :customer_id AND t.status != :st AND YEAR(t.date_added) = YEAR(:year) AND MONTH(t.date_added) = MONTH(:month)';
            $criteria->params = array(
                ':customer_id'  => $customer_id,
                ':st'           => Lists::STATUS_PENDING_DELETE,
                ':year'         => $model->date_added,
                ':month'        => $model->date_added,
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

        $customer_id = (int)Yii::app()->customer->getId();
        $cacheKey    = md5(__FILE__ . __METHOD__ . $customer_id);
        if ($items = Yii::app()->cache->get($cacheKey)) {
            return $this->renderJson(array(
                'label' => Yii::t('app', '{n} months growth', 3),
                'data'  => $items,
                'color' => '#3c8dbc'
            ));
        }

        $criteria = new CDbCriteria();
        $criteria->select    = 'DISTINCT(DATE(t.date_added)) AS date_added';
        $criteria->condition = 't.customer_id = :cid AND t.status != :st AND DATE(t.date_added) >= DATE_ADD(LAST_DAY(DATE_SUB(NOW(), INTERVAL 3 MONTH)), INTERVAL 1 DAY)';
        $criteria->group     = 'MONTH(t.date_added)';
        $criteria->order     = 't.date_added ASC';
        $criteria->limit     = 3;
        $criteria->params    = array(
            ':cid' => (int)$customer_id,
            ':st'  => Campaign::STATUS_PENDING_DELETE
        );

        $models = Campaign::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 't.customer_id = :cid AND t.status != :st AND YEAR(t.date_added) = YEAR(:year) AND MONTH(t.date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'  => $model->date_added,
                ':month' => $model->date_added,
                ':cid'   => (int)$customer_id,
                ':st'    => Campaign::STATUS_PENDING_DELETE
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

        $customer_id = (int)Yii::app()->customer->getId();
        $cacheKey    = md5(__FILE__ . __METHOD__ . $customer_id);
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

        $criteria->with = array(
            'subscriber' => array(
                'select'    => false,
                'together'  => true,
                'joinType'  => 'INNER JOIN',
                'with'      => array(
                    'list'  => array(
                        'select'    => false,
                        'together'  => true,
                        'joinType'  => 'INNER JOIN',
                        'condition' => 'list.customer_id = :customer_id AND list.status != :st',
                        'params'    => array(':customer_id' => (int)$customer_id, ':st' => Lists::STATUS_PENDING_DELETE),
                    ),
                )
            ),
        );
        $models = $cdlModel->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'YEAR(t.date_added) = YEAR(:year) AND MONTH(t.date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $criteria->with = array(
                'subscriber' => array(
                    'select'    => false,
                    'together'  => true,
                    'joinType'  => 'INNER JOIN',
                    'with'      => array(
                        'list'  => array(
                            'select'    => false,
                            'together'  => true,
                            'joinType'  => 'INNER JOIN',
                            'condition' => 'list.customer_id = :customer_id AND list.status != :st',
                            'params'    => array(':customer_id' => (int)$customer_id, ':st' => Lists::STATUS_PENDING_DELETE),
                        ),
                    )
                ),
            );
            $monthName  = date('M', strtotime($model->date_added));
            $count      = $cdlModel->count($criteria);
            $items[]    = array(Yii::t('app', $monthName) . ' ' . date('Y', strtotime($model->date_added)), $count);
        }

        $lines[] = array(
            'label' => Yii::t('dashboard', 'Delivery, {n} months growth', 3),
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

        $criteria->with = array(
            'subscriber' => array(
                'select'    => false,
                'together'  => true,
                'joinType'  => 'INNER JOIN',
                'with'      => array(
                    'list'  => array(
                        'select'    => false,
                        'together'  => true,
                        'joinType'  => 'INNER JOIN',
                        'condition' => 'list.customer_id = :customer_id AND list.status != :st',
                        'params'    => array(':customer_id' => (int)$customer_id, ':st' => Lists::STATUS_PENDING_DELETE),
                    ),
                )
            ),
        );
        $models = CampaignBounceLog::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'YEAR(t.date_added) = YEAR(:year) AND MONTH(t.date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $criteria->with = array(
                'subscriber' => array(
                    'select'    => false,
                    'together'  => true,
                    'joinType'  => 'INNER JOIN',
                    'with'      => array(
                        'list'  => array(
                            'select'    => false,
                            'together'  => true,
                            'joinType'  => 'INNER JOIN',
                            'condition' => 'list.customer_id = :customer_id AND list.status != :st',
                            'params'    => array(':customer_id' => (int)$customer_id, ':st' => Lists::STATUS_PENDING_DELETE),
                        ),
                    )
                ),
            );
            $monthName  = date('M', strtotime($model->date_added));
            $count      = CampaignBounceLog::model()->count($criteria);
            $items[]    = array(Yii::t('app', $monthName) . ' ' . date('Y', strtotime($model->date_added)), $count);
        }

        $lines[] = array(
            'label' => Yii::t('dashboard', 'Bounce, {n} months growth', 3),
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

        $customer_id = (int)Yii::app()->customer->getId();
        $cacheKey    = md5(__FILE__ . __METHOD__ . $customer_id);
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
        $criteria->with = array(
            'subscriber' => array(
                'select'    => false,
                'together'  => true,
                'joinType'  => 'INNER JOIN',
                'with'      => array(
                    'list'  => array(
                        'select'    => false,
                        'together'  => true,
                        'joinType'  => 'INNER JOIN',
                        'condition' => 'list.customer_id = :customer_id AND list.status != :st',
                        'params'    => array(':customer_id' => (int)$customer_id, ':st' => Lists::STATUS_PENDING_DELETE),
                    ),
                )
            ),
        );
        $models = CampaignTrackUnsubscribe::model()->findAll($criteria);

        $items = array();
        foreach ($models as $model) {
            $criteria = new CDbCriteria();
            $criteria->condition = 'YEAR(t.date_added) = YEAR(:year) AND MONTH(t.date_added) = MONTH(:month)';
            $criteria->params = array(
                ':year'   => $model->date_added,
                ':month'  => $model->date_added,
            );
            $criteria->with = array(
                'subscriber' => array(
                    'select'    => false,
                    'together'  => true,
                    'joinType'  => 'INNER JOIN',
                    'with'      => array(
                        'list'  => array(
                            'select'    => false,
                            'together'  => true,
                            'joinType'  => 'INNER JOIN',
                            'condition' => 'list.customer_id = :customer_id AND list.status != :st',
                            'params'    => array(':customer_id' => (int)$customer_id, ':st' => Lists::STATUS_PENDING_DELETE),
                        ),
                    )
                ),
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
	
	public function actionEmailDashboard(){
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'Email Dashboard'),
            'pageHeading'       => Yii::t('dashboard', 'Email Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Email Dashboard'),
            ),
        ));

        //$canSegmentList = Yii::app()->customer->getModel()->getGroupOption('lists.can_segment_lists', 'yes') == 'yes';

        $this->render('email_dashboard', compact(0));
	}
	
	public function actionSmsDashboard()
	{
		$customer   = Yii::app()->customer->getModel();
		
		$oDbConnection = Yii::app()->db; 
		$sens_sms_count = $oDbConnection->createCommand('SELECT count(sms_id) FROM uic_sms WHERE customer_id ='.$customer->customer_id.' AND status="Sent"')->queryScalar();
		
		$notsend_sms_count = $oDbConnection->createCommand('SELECT count(sms_id) FROM uic_sms WHERE customer_id ='.$customer->customer_id.' AND status!="Sent"')->queryScalar();
		
		$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'SMS Dashboard'),
            'pageHeading'       => Yii::t('dashboard', 'SMS Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'SMS Dashboard'),
            ),
        ));

        //$canSegmentList = Yii::app()->customer->getModel()->getGroupOption('lists.can_segment_lists', 'yes') == 'yes';

        $this->render('sms_dashboard', compact('sens_sms_count', 'notsend_sms_count', 'rem_quota'));
	}
	
	public function actionSocialDashboard()
	{
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'Social Dashboard'),
            'pageHeading'       => Yii::t('dashboard', 'Social Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Social Dashboard'),
            ),
        ));

        //$canSegmentList = Yii::app()->customer->getModel()->getGroupOption('lists.can_segment_lists', 'yes') == 'yes';

        $this->render('social_dashboard', compact(0));
	}
	
	public function actionUniquesubscriber(){
		$customer    = Yii::app()->customer->getModel();
		$customer_list = implode(",",$customer->getAllListsIds());
		
		// count unique subscribers.
        $criteria = new CDbCriteria();
        $criteria->select = 'DISTINCT(t.email),t.subscriber_id, t.mobile';
        $criteria->addInCondition('t.list_id', $customer->getAllListsIds());
        $subscribers = ListSubscriber::model()->findAll($criteria);
		
		$get_field_array = array();
		$temp_array = array();
		foreach($subscribers as $sub_key => $sub_val){
			$check_subscriber = Yii::app()->db->createCommand("SELECT DISTINCT(value), field_id, subscriber_id FROM uic_list_field_value WHERE subscriber_id = '".$sub_val->subscriber_id."'")->queryAll();
			//$check_subscriber = Yii::app()->db->createCommand("SELECT DISTINCT(ulfv.value), ulfv.subscriber_id FROM uic_list_field_value as ulfv INNER JOIN uic_list_field as ulf ON ulfv.field_id = ulf.field_id WHERE ulfv.value != '' AND ulfv.subscriber_id = '".$sub_val->subscriber_id."'")->queryAll();
			//print_r($check_subscriber);
			//value != '' AND
			
			
			foreach($check_subscriber as $subscriber_key => $subscriber_val){
				$get_field_name = Yii::app()->db->createCommand("SELECT * FROM uic_list_field WHERE field_id ='".$subscriber_val['field_id']."'")->queryRow();
				
				if($subscriber_val['field_id'] == $get_field_name['field_id']){
					$get_field_array[$subscriber_val['subscriber_id']][$get_field_name['tag']] = $subscriber_val['value'];
					$temp_array[$subscriber_val['subscriber_id']][$get_field_name['tag']] = $subscriber_val['value'];
					
				}
			}
		}
		$get_field_array = array_map("unserialize", array_unique(array_map("serialize", $get_field_array)));
		//print_r($get_field_array);exit;
		//print_r($get_field_array);exit;
		//print_r($get_field_array);exit;
		// $column_email = array_unique(array_column($get_field_array,'EMAIL'));
		// echo count($column_email).'<br/>';
		// echo count($get_field_array);
		// exit;		
		
		
		// $all_email = array_column($get_field_array, 'EMAIL');
		// print_r($all_email);exit;
		// $count = 0;
		// foreach($get_field_array as $get_field_key => $get_field_val){
			
			// $count++;
		// }exit;
		//echo count($get_field_array) - count(array_unique(array_column($get_field_array,'MOBILE')));exit;
		

		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'Unique Subscriber'),
            'pageHeading'       => Yii::t('dashboard', 'Unique Subscriber'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Unique Subscriber'),
            ),
        ));

        //$canSegmentList = Yii::app()->customer->getModel()->getGroupOption('lists.can_segment_lists', 'yes') == 'yes';

        $this->render('unique_subscriber', compact('get_field_array'));
	}
	
	protected function getPendingListCount($customer_id){
		$pending_campaign_array = Yii::app()->db->createCommand("SELECT list_id FROM uic_sms_campaign WHERE campaign_status='PENDING' AND customer_id='".$customer_id."'")->queryAll();
		$count_subscriber = array();
		foreach($pending_campaign_array as $pending_campaign){
			$count_list_sub = Yii::app()->db->createCommand("SELECT ulfv.value FROM uic_list as li INNER JOIN uic_list_subscriber as lis on li.list_id = lis.list_id inner join uic_list_field as uif on li.list_id = uif.list_id inner join uic_list_field_value as ulfv on lis.subscriber_id = ulfv.subscriber_id and uif.field_id = ulfv.field_id where li.list_id = '".$pending_campaign['list_id']."' and uif.label = 'Mobile' AND lis.status='confirmed' AND ulfv.value != '' GROUP BY ulfv.value")->queryAll();
		 $count_subscriber[] = count($count_list_sub);
		}
		return array_sum($count_subscriber);
	}
}
