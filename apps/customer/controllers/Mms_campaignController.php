<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CampaignsController
 *
 * Handles the actions for campaigns related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class Mms_campaignController extends Controller
{
    public function init()
    {
        $this->getData('pageStyles')->add(array('src' => AssetsUrl::js('datetimepicker/css/bootstrap-datetimepicker.min.css')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('datetimepicker/js/bootstrap-datetimepicker.min.js')));
		
		$languageCode = LanguageHelper::getAppLanguageCode();
        if (Yii::app()->language != Yii::app()->sourceLanguage && is_file(AssetsPath::js($languageFile = 'datetimepicker/js/locales/bootstrap-datetimepicker.'.$languageCode.'.js'))) {
            $this->getData('pageScripts')->add(array('src' => AssetsUrl::js($languageFile)));
        }
		
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('campaigns.js')));
        $this->getData('pageStyles')->add(array('src' => AssetsUrl::css('wizard.css')));
        parent::init();
    }

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
		$filters = array(
            //'postOnly + delete, slug',
        );
        return CMap::mergeArray($filters, parent::filters());
    }

    /**
     * List available campaigns
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
		$customer = Yii::app()->customer->getModel();
		
        $mmscampaign = new SmsCampaign('search');
        $mmscampaign->unsetAttributes();
		
        $mmscampaign->attributes  = (array)$request->getQuery($mmscampaign->modelName, array());
		$mmscampaign->customer_id = $customer->customer_id;
		$mmscampaign->campaign_type = 'MMS';

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms_campaign', 'Mms Template'),
            'pageHeading'       => Yii::t('mms_campaign', 'Mms Campaign'),
            'pageBreadcrumbs'   => array(
                Yii::t('app', 'Mms Campaign')
            )
        ));		

        $this->render('index', compact('mmscampaign'));
    }
	
	public function actionCheckschedule()
	{
		$options = Yii::app()->options;
		$sms = new Sms();
		$criteria = new CDbCriteria();
		$criteria->select = 't.sms_campaign_id,t.customer_id,t.campaign_name,t.send_at,t.list_id,t.campaign_text';
		$criteria->addCondition('t.campaign_status="PENDING"');
		$criteria->addCondition('t.send_at <= NOW()');
		$criteria->order  = 't.sms_campaign_id ASC';
		$sms_campaigns = SmsCampaign::model()->findAll($criteria);
		
		$count_rec = 0;
		$count_rec_not_sent = 0;
		$step = 4;
		if(is_array($sms_campaigns) && count($sms_campaigns)){
			foreach($sms_campaigns as $smscampaign){
				//get customer details and customer model function.
				$customer = Customer::model()->findByPk((int)$smscampaign->customer_id);
				$customer_company = CustomerCompany::model()->findByAttributes(array('customer_id' => $smscampaign->customer_id));
				
				//get firstname and last name
				$subscriber = new ListSubscriber();
				$criteria = new CDbCriteria();
				$criteria->select = 't.list_id, t.subscriber_id, t.status';
				$criteria->group = 't.subscriber_id';
				$criteria->compare('t.list_id', $smscampaign->list_id);
				$criteria->compare('t.status', 'confirmed');
				$criteria->order = 't.subscriber_id DESC';
				$subscribers = $subscriber->findAll($criteria);	
				
				$columns = array();
				$rows = array();
				$criteria = new CDbCriteria();
				$criteria->select ='t.label , t.field_id';
				$criteria->compare('t.list_id', $smscampaign->list_id);
				$criteria->order = 't.sort_order DESC';
				$fields = ListField::model()->findAll($criteria);

				foreach ($subscribers as $index => $subscriber) {
					$subscriberRow = array('columns' => array());

					$subscriberRow['columns'][] =  $subscriber->subscriber_id;
					$subscriberRow['columns'][] = $subscriber->status;

					foreach ($fields as $field) {
						$criteria = new CDbCriteria();
						$criteria->select = 't.value';
						$criteria->compare('field_id', $field->field_id);
						$criteria->compare('subscriber_id', $subscriber->subscriber_id);
						$values = ListFieldValue::model()->findAll($criteria);

						$value = array();
						foreach ($values as $val) {
							$value[] = $val->value;
						}

						$subscriberRow['columns'][] = CHtml::encode(implode(', ', $value));
					}

					//if (count($subscriberRow['columns']) == count($columns)) {
						$rows[$subscriber->subscriber_id] = $subscriberRow;
					//}

				}
				
				$count_list_subscriber = Yii::app()->db->createCommand("SELECT count(subscriber_uid) FROM uic_list_subscriber WHERE status='confirmed' AND list_id ='".$smscampaign->list_id."'")->queryScalar();
				
				$divide = ceil($count_list_subscriber/$step);
				
				for($no=0; $no<$divide; $no++)
				{
					$start_no = $no*$step;
					
					$receive_sms_array = Yii::app()->db->createCommand("SELECT ulfv.value, lis.subscriber_id, lis.subscriber_uid FROM uic_list as li INNER JOIN uic_list_subscriber as lis on li.list_id = lis.list_id inner join uic_list_field as uif on li.list_id = uif.list_id inner join uic_list_field_value as ulfv on lis.subscriber_id = ulfv.subscriber_id and uif.field_id = ulfv.field_id where li.list_id = '".$smscampaign->list_id."' and uif.label = 'mobile' and lis.status='confirmed' LIMIT ".$start_no.",".$step)->queryAll();
					
					if($customer->getGroupOption('smssending.sms_quota', -1) != -1){
						
						$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
						
						if(count($receive_sms_array) >= $rem_quota){
							$output = array_slice($receive_sms_array, 0, $rem_quota);
							$count_rec_not_sent = (count($receive_sms_array) - count($output));
							if(is_array($output) && count($output)){
								$receive_sms_array = $output;
							}
						}
					}
					foreach ($receive_sms_array  as $key=>$rec)
					{
						$fname = $rows[$rec['subscriber_id']]['columns'][3];
						$lname = $rows[$rec['subscriber_id']]['columns'][4];
						
						$shortcodes = array("{{firstname}}", "{{lastname}}", "{{company_name}}");
						$replacecodes   = array($fname, $lname, $customer_company->name);
						$newmessage = str_replace($shortcodes, $replacecodes, $smscampaign->campaign_text);

						if($rec['value'] != ''){							
							if(!$customer->getIsSmsOverQuota())
							{
								$status = $sms->shceduleSms($rec['value'], $newmessage, $smscampaign->customer_id,$rec['subscriber_uid']);
								if($status){
									$count_rec++;
								}else{
									$count_rec_not_sent++;
								}
							}else{
								$count_rec_not_sent++;
							}
						}
					}
				}

				$emailBody = '<h2 style="border-collapse: collapse; font-family: Arial,sans-serif; font-size:20px;">SMS Campaign Report</h2>
				<table align="left" cellpadding="2" cellspacing="0" width="100%" bgcolor="#ffffff" style="border-collapse: collapse; font-family: Arial,sans-serif; font-size:14px; text-align:left;border:1px solid #666666;" >
				<tr>
					<th style="border:1px solid #666666; padding:8px;">Your SMS Campaign Name</th>
					<td style="border:1px solid #666666; padding:8px;">'.$smscampaign->campaign_name.'</td>
				</tr>
				<tr>
					<th style="border:1px solid #666666; padding:8px;">Campaign Start Time</th>
					<td style="border:1px solid #666666; padding:8px;">'.$smscampaign->send_at.'</td>
				</tr>
				<tr>
					<th style="border:1px solid #666666; padding:8px;">Campaign Finish Time</th>
					<td style="border:1px solid #666666; padding:8px;">'.date('Y-m-d H:i:s').'</td>
				</tr>
				<tr>
					<th style="border:1px solid #666666; padding:8px;">Campaign Send SMS Successfully Count</th>
					<td style="border:1px solid #666666; padding:8px;">'.$count_rec.'</td>
				</tr>
				<tr>
					<th style="border:1px solid #666666; padding:8px;">Campaign Not Send SMS Count</th>
					<td style="border:1px solid #666666; padding:8px;">'.$count_rec_not_sent.'</td>
				</tr>
				</table>';
				
				$email = new TransactionalEmail();
				$email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
				$email->to_name      = $customer->getFullName();
				$email->to_email     = $customer->email;
				$email->from_name    = $options->get('system.common.site_name', 'Marketing website');
				$email->subject      = 'SMS Campaign Complete';
				$email->body         = $emailBody;
				$email->save();
				
				$sms_campaign_id = $smscampaign->saveSendrecord($count_rec,$count_rec_not_sent,$smscampaign->sms_campaign_id);
			}
		}		
	}
	
	public function actionCreate()
	{
		$mmscampaign   = new SmsCampaign();
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
		$customer_id = (int)Yii::app()->customer->getId();
		
		$list = new Lists();
        $criteria = new CDbCriteria();
		$criteria->select = 't.list_id, t.customer_id, t.name, t.display_name, t.status , t.date_added';
        $criteria->compare('t.customer_id', $customer_id);
		//$criteria->compare('customer_id', 'active');
		$criteria->compare('t.status', 'active');
		$criteria->order = 't.list_id DESC';
		$lists = $list->findAll($criteria);	

		$mylists =array();
		foreach ($lists as $index => $list) {
			$mylists[$list->list_id] = $list->name;
			
		}

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($smscampaign->modelName, array()))) {
			print_r($_POST);exit;
			$smsschedule = $request->getPost('smsschedule');
			$smscampaign->customer_id = $customer_id;
			$smscampaign->campaign_name = $attributes['campaign_name'];
			$smscampaign->campaign_text = $attributes['campaign_text'];
			$smscampaign->list_id = $attributes['list_id'];
			
			if($attributes['send_at'] != ''){
				if($smsschedule == 1){
					$smscampaign->send_at = date('Y-m-d H:i:s A',strtotime($attributes['sendAt']));
				}else{
					$smscampaign->send_at = date('Y-m-d H:i:s A',strtotime($attributes['sendAt'].' + 2 minute'));
				}
			}
			$smscampaign->campaign_status = 'PENDING';
			$smscampaign->save();
			
			//$smscampaign->attributes = $attributes;
            if (!$smscampaign->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
				if($smsschedule == 1){
					$notify->addSuccess(Yii::t('app', 'Your Sms Schedule Set has been successfully!'));
				}else{
					$notify->addSuccess(Yii::t('app', 'Your Send Sms Start Now!'));
				}
                
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'smscampaign'  => $smscampaign,
            )));

            if ($collection->success) {
                $this->redirect(array('sms_campaign/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('smscampaign', 'Create new Mms Campaign'),
            'pageHeading'       => Yii::t('mmscampaign', 'Create new Mms Campaign'),
            'pageBreadcrumbs'   => array(
                Yii::t('mmscampaign', 'mmscampaign') => $this->createUrl('mms_campaign/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('mmscampaign', 'mylists'));
	}
	
	public function actionUpdate($id){
		$smscampaign = SmsCampaign::model()->findByPk((int)$id);
		$customer_id = (int)Yii::app()->customer->getId();

        if (empty($smscampaign)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
		
		$list = new Lists();
        $criteria = new CDbCriteria();
		$criteria->select = 't.list_id, t.customer_id, t.name, t.display_name, t.status , t.date_added';
        $criteria->compare('t.customer_id', $customer_id);
		//$criteria->compare('customer_id', 'active');
		$criteria->compare('t.status', 'active');
		$criteria->order = 't.list_id DESC';
		$lists = $list->findAll($criteria);	

		$mylists =array();
		foreach ($lists as $index => $list) {
			$mylists[$list->list_id] = $list->name;
			
		}

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($smscampaign->modelName, array()))) {
			
			$attributes['customer_id'] = $customer_id;
			$smscampaign->customer_id = $customer_id;
			$smscampaign->campaign_name = $attributes['campaign_name'];
			$smscampaign->campaign_text = $attributes['campaign_text'];
			$smscampaign->list_id = $attributes['list_id'];
			$smscampaign->save();
            //$smscampaign->attributes = $attributes;
            if (!$smscampaign->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'smscampaign'  => $smscampaign,
            )));

            if ($collection->success) {
                $this->redirect(array('sms_campaign/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms_template', 'Update Sms Template'),
            'pageHeading'       => Yii::t('sms_template', 'Update Sms Template'),
            'pageBreadcrumbs'   => array(
                //Yii::t('customers', 'Customers') => $this->createUrl('customers/index'),
                Yii::t('app', 'Update Sms Template'),
            )
        ));

        $this->render('form', compact('smscampaign','mylists'));
	}
	
	public function actionDelete($id){
		$smscampaign = SmsCampaign::model()->findByPk((int)$id);

        if (empty($smscampaign)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $smscampaign->delete();

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('sms_template/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $smscampaign,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
	}
	
	public function actionSync_datetime()
    {
        $customer   = Yii::app()->customer->getModel();
        $request    = Yii::app()->request;

        $timeZoneDateTime   = date('Y-m-d H:i:s', strtotime($request->getQuery('date', date('Y-m-d H:i:s'))));
        $timeZoneTimestamp  = strtotime($timeZoneDateTime);
        $localeDateTime     = Yii::app()->dateFormatter->formatDateTime($timeZoneTimestamp, 'short', 'short');

        // since the date is already in customer timezone we need to convert it back to utc
        $sourceTimeZone      = new DateTimeZone($customer->timezone);
        $destinationTimeZone = new DateTimeZone(Yii::app()->timeZone);
        $dateTime            = new DateTime($timeZoneDateTime, $sourceTimeZone);
        $dateTime->setTimezone($destinationTimeZone);
        $utcDateTime = $dateTime->format('Y-m-d H:i:s');

        return $this->renderJson(array(
            'localeDateTime'  => $localeDateTime,
            'utcDateTime'     => $utcDateTime,
        ));
    }
}
