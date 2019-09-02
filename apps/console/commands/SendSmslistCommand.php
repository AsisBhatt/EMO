<?php defined('MW_PATH') || exit('No direct script access allowed');
/**
 * SendSmslistCommand
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */
 
class SendSmslistCommand extends ConsoleCommand 
{
    public function actionIndex() 
    {
		$criteria->select = 't.sms_campaign_id,t.customer_id,t.campaign_list,t.campaign_text';
		$criteria->addCondition('t.campaign_status="PENDING"');
		$criteria->addCondition('t.send_at = NOW()');
		$criteria->order  = 't.sms_campaign_id ASC';
		$sms_campaigns = SmsCampaign::model()->findAll($criteria);
		
		$subscriber = new ListSubscriber();
		$criteria = new CDbCriteria();
		$criteria->select = 't.list_id, t.subscriber_id, t.subscriber_uid, t.ip_address, t.status, t.date_added';
		$criteria->group = 't.subscriber_id';
		$criteria->compare('t.list_id', $slist_id);
		$criteria->compare('t.status', 'confirmed');
		$criteria->order = 't.subscriber_id DESC';
		$subscribers = $subscriber->findAll($criteria);	
		
		$columns = array();
		$rows = array();
		$criteria = new CDbCriteria();
		$criteria->compare('t.list_id', $slist_id);
		$criteria->order = 't.sort_order ASC';
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
		
		$count_list_subscriber = Yii::app()->db->createCommand("SELECT count(subscriber_uid) FROM uic_list_subscriber WHERE status='confirmed' AND list_id ='".$slist_id."'")->queryScalar();
		
		$divide = ceil($count_list_subscriber/$step);
		for($no=0; $no<$divide; $no++)
		{
			$start_no = $no*$step;
			
			$receive_sms_array = Yii::app()->db->createCommand("SELECT ulfv.value, lis.subscriber_id FROM uic_list as li INNER JOIN uic_list_subscriber as lis on li.list_id = lis.list_id inner join uic_list_field as uif on li.list_id = uif.list_id inner join uic_list_field_value as ulfv on lis.subscriber_id = ulfv.subscriber_id and uif.field_id = ulfv.field_id where li.list_id = '".$slist_id."' and uif.label = 'mobile' and lis.status='confirmed' and ulfv.value != '' LIMIT ".$start_no.",".$step)->queryAll();
			
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
				$replacecodes   = array($fname, $lname, $company_info['name']);
				$newmessage = str_replace($shortcodes, $replacecodes, $messageorg);
				
				if($rec['value'] != '')
				{
					if(!$customer->getIsSmsOverQuota()){
						$status = $sms->sendsms('+1'.$rec['value'], $newmessage);							
						if($status){
							$count_rec++;
						}
					}
				}
			}
		}
    }
}
