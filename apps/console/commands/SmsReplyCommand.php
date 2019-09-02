<?php defined('MW_PATH') || exit('No direct script access allowed');
/**
 * SmsReplyCommand
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */
 
class SmsReplyCommand extends ConsoleCommand 
{
    public function actionIndex() 
    {
		$subscriber_model = new ListSubscriber();
		
		$start_date = date('Y-m-d');
		//$start_date = date('Y-m-d', strtotime('2017-09-23'));
		//echo 'https://api.flowroute.com/v2/messages?start_date='.$start_date;exit;
		$ch = curl_init('https://api.flowroute.com/v2/messages?start_date='.$start_date);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_USERPWD,'15747933:IwBScTE38brdx0r95iZG6sonxAaZptWf');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json')
		);
		$result = curl_exec($ch);		
		$sms_rec_obj = json_decode($result);
		
		if(!empty($sms_rec_obj->data)){
			foreach($sms_rec_obj->data as $receive_obj => $receive){
				if($receive->id != ''){
					$attributes_array = (array)$receive->attributes;
					
					$receive_sms_array = Yii::app()->db->createCommand("SELECT * FROM uic_sms_rply WHERE sms_rply_id = '".$receive->id."'")->queryRow();
					
					if(!is_array($receive_sms_array)){
						if($attributes_array['direction'] == 'inbound'){
							
							if(strstr(strtoupper($attributes_array['body']),'STOP')){
								$customer_array = CustomerCompany::model()->findByAttributes(array('flowroute_sms_num' => $attributes_array['to']));
								
								$stop_number_array = Yii::app()->db->createCommand("SELECT lival.subscriber_id 'uic_list_field_value', lisu.list_id 'uic_list_subscriber' FROM uic_list_field_value lival, uic_list li, uic_list_subscriber lisu WHERE lival.subscriber_id = lisu.subscriber_id  AND li.list_id = lisu.list_id AND li.customer_id = '".$customer_array->customer_id."' AND lival.value = '".$attributes_array['from']."'")->queryAll();
								
								$subscriber_model->getStop($stop_number_array, $customer_array->customer_id);
							}
						}
						
						if($attributes_array['direction'] == 'inbound'){
							$smsrply_model = new SmsRply();
							$smsrply_model->sms_rply_id = $receive->id;
							$smsrply_model->customer_id = $customer_array->customer_id;
							$smsrply_model->sms_rply_time = date('Y-m-d h:m:s',strtotime($attributes_array['timestamp']));
							$smsrply_model->sms_rply_direction = $attributes_array['direction'];
							$smsrply_model->sms_rply_to_number = $attributes_array['to'];
							$smsrply_model->sms_rply_from_number = $attributes_array['from'];
							$smsrply_model->sms_rply_cost = $attributes_array['amount_display'];
							$smsrply_model->sms_rply_body = $attributes_array['body'];
							$smsrply_model->sms_rply_created = date('Y-m-d h:m:s');
							$smsrply_model->save();
						}
					}
				}
			}
		}
		
		return 0;
    }
}
