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
 
class AutoReplyCommand extends ConsoleCommand 
{
    public function actionIndex() 
    {
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
					
					if($attributes_array['body'] == 'BUY' || $attributes_array['body'] == 'Buy' || $attributes_array['body'] == 'buy'){
						
						$receive_text_array = Yii::app()->db->createCommand("SELECT * FROM uic_text_reply WHERE text_from_number = '".$attributes_array['from']."' AND text_mdr_id = '".$receive->id."'")->queryRow();
						
						if(!is_array($receive_text_array) && empty($receive_text_array))
						{
							$get_auto_template = Yii::app()->db->createCommand("SELECT auto_temp_text FROM uic_autoreply_template WHERE auto_temp_status='ACTIVE'")->queryRow();
							
							$status = $sms->sendAutoReply($attributes_array['from'],$get_auto_template['auto_temp_text']);
							if($status){
								$text_reply = new TextReply();
								$text_reply->text_mdr_id = $receive->id;
								$text_reply->customer_id = 0;
								$text_reply->text_body = $get_auto_template['auto_temp_text'];
								$text_reply->text_from_number = $attributes_array['from'];
								$text_reply->text_to_number = $attributes_array['to'];
								$text_reply->text_status = 'SEND';
								$text_reply->text_created = date('Y-m-d H:i:s');
								$text_reply->save();
							}
						}
					}
				}
			}
		}	
		return 0;
    }
}
