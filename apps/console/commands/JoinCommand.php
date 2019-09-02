<?php defined('MW_PATH') || exit('No direct script access allowed');
/**
 * JoinCommand
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */
 
class JoinCommand extends ConsoleCommand 
{
    public function actionIndex() 
    {
		
		$request = Yii::app()->request;
		$start_date = date('Y-m-d');
		$sms = new Sms();
		//$start_date = date('Y-m-d', strtotime('2017-12-13'));
		
		//echo 'https://api.flowroute.com/v2/messages?start_date='.$start_date;exit;
		$ch = curl_init('https://api.flowroute.com/v2.1/messages?start_date='.$start_date);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($ch, CURLOPT_USERPWD,'15747933:IwBScTE38brdx0r95iZG6sonxAaZptWf');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json')
		);
		
		$result = curl_exec($ch);		
		
		$textreply_obj = json_decode($result);
		
		if(!empty($textreply_obj->data)){
			foreach($textreply_obj->data as $receive_obj => $receive){
				if($receive->id != ''){
					$attributes_array = (array)$receive->attributes;
					
					$repeat_receive_array = Yii::app()->db->createCommand("SELECT * FROM uic_sms_rply WHERE sms_rply_id = '".$receive->id."'")->queryRow();
					
					
					$customer_info = Yii::app()->db->createCommand("SELECT * FROM uic_customer_company WHERE 	flowroute_sms_num = '".$attributes_array['to']."' ")->queryRow();
					
					
					//$list_info = Yii::app()->db->createCommand("SELECT * FROM uic_list WHERE customer_id ='".$customer_info['customer_id']."' AND  name LIKE 'Default SMS List'")->queryRow()
					
					$criteria=new CDbCriteria;
					$criteria->select='list_id';  // only select the 'list_id' column
					$criteria->condition='customer_id=:customer_id';
					$criteria->condition='name=:name';
					$criteria->params=array(':customer_id'=>$customer_info['customer_id']);
					$criteria->params=array(':name'=>'Default SMS List');
					$list_info = Lists::model()->find($criteria);
					
					$subscriber_count = $list_info->getUniqueSubscriber($list_info->list_id, $customer_info['customer_id'], $attributes_array['from']);
					
					if($attributes_array['to'] != '18882642564'){
						if(!is_array($repeat_receive_array))
						{
							if($attributes_array['direction'] == 'inbound'){
								
								if($attributes_array['body'] == 'JOIN' || $attributes_array['body'] == 'Join' || $attributes_array['body'] == 'join'){
									
									if($subscriber_count < 1){
										
										$get_auto_template = Yii::app()->db->createCommand("SELECT auto_temp_text FROM uic_autoreply_template WHERE auto_temp_status='ACTIVE' AND auto_temp_type = 'JOIN'")->queryRow();
								
										$auto_template_message = '';
										if(is_array($get_auto_template) && count($get_auto_template)){
											$auto_template_message = $get_auto_template['auto_temp_text'];
										}else{
											$auto_template_message = 'Welcome to Our Portal.';
										}
										
										//$status = $sms->sendAutoReply($attributes_array['from'],$auto_template_message, $customer_info['customer_id']);
											
										$subscriber = new ListSubscriber();
										$subscriber->list_id    = $list_info->list_id;
										$subscriber->mobile      = $attributes_array['from'];
										$subscriber->source     = ListSubscriber::SOURCE_JOIN;
										$subscriber->ip_address = $request->getServer('HTTP_MW_REMOTE_ADDR', $request->getServer('REMOTE_ADDR'));
										$subscriber->status = ListSubscriber::STATUS_CONFIRMED;
										$subscriber->save();
										
										$fields = ListField::model()->findAllByAttributes(array(
											'list_id' => $list_info->list_id,
										));
										
										foreach ($fields as $field) {
											
											if($field->tag == 'MOBILE'){
												$valueModel = new ListFieldValue();
												$valueModel->field_id = $field->field_id;
												$valueModel->subscriber_id = $subscriber->subscriber_id;
												$valueModel->value = $attributes_array['from'];
												$valueModel->save();
											}									
										}
										
										$smsrply_model = new SmsRply();
										$smsrply_model->sms_rply_id = $receive->id;
										$smsrply_model->customer_id = $customer_info['customer_id'];
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
				}
			}
		}
		return 0;
    }
}
