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
 
class MmsScheduleCommand extends ConsoleCommand 
{
    public function actionIndex() 
    {
		$options = Yii::app()->options;
		$sms = new Sms();
		$criteria = new CDbCriteria();
		$criteria->select = 't.sms_campaign_id,t.customer_id,t.campaign_name,t.send_at,t.list_id,t.campaign_text,t.campaign_media,campaign_status';
		$criteria->addCondition('t.campaign_type="MMS"');
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
				
				if($customer->status == 'active'){
					
					if($smscampaign->campaign_status == 'PENDING'){
						$smscampaign->campaign_status = 'PROCESSING';
						$smscampaign->save();
					}
					
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
						
						$receive_sms_array = Yii::app()->db->createCommand("SELECT ulfv.value, lis.subscriber_id, lis.subscriber_uid FROM uic_list as li INNER JOIN uic_list_subscriber as lis on li.list_id = lis.list_id inner join uic_list_field as uif on li.list_id = uif.list_id inner join uic_list_field_value as ulfv on lis.subscriber_id = ulfv.subscriber_id and uif.field_id = ulfv.field_id where li.list_id = '".$smscampaign->list_id."' and uif.label = 'Mobile' and lis.status='confirmed' GROUP BY ulfv.value LIMIT ".$start_no.",".$step)->queryAll();
						
						// if($customer->getGroupOption('smssending.sms_quota', -1) != -1){
							
							// $rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
							
							// if(count($receive_sms_array) >= $rem_quota){
								// $output = array_slice($receive_sms_array, 0, $rem_quota);
								// $count_rec_not_sent = (count($receive_sms_array) - count($output));
								// if(is_array($output) && count($output)){
									// $receive_sms_array = $output;
								// }
							// }
						// }
						foreach ($receive_sms_array  as $key=>$rec)
						{
							$fname = $rows[$rec['subscriber_id']]['columns'][3];
							$lname = $rows[$rec['subscriber_id']]['columns'][2];
							
							$shortcodes = array("{{firstname}}", "{{lastname}}", "{{company_name}}");
							$replacecodes   = array($fname, $lname, $customer_company->name);
							$newmessage = str_replace($shortcodes, $replacecodes, $smscampaign->campaign_text);
							
							if($rec['value'] != ''){
								if($smscampaign->campaign_status == 'PROCESSING'){
									if(!$customer->getIsSmsOverQuota())
									{
										$status = $sms->shceduleMms($rec['value'], $smscampaign->campaign_media,$newmessage, $smscampaign->customer_id);
										
										$status = explode("_",$status);
										if($status[0] == 'sent'){
											CommonHelper::setActivityLogs('MMS Campaign '.str_replace(array('{{','}}'),'',$sms->tableName()),$status[1],$sms->tableName(),'MMS Campaign',$smscampaign->customer_id);
											$count_rec++;
										}else{
											CommonHelper::setActivityLogs('MMS Campaign '.$status[0].' '.str_replace(array('{{','}}'),'',$sms->tableName()),$status[1],$sms->tableName(),'MMS Campaign',$smscampaign->customer_id);
											$count_rec_not_sent++;
										}
									}else{
										CommonHelper::setActivityLogs('MMS Campaign Merchant Validity is Over'.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'MMS Campaign',$smscampaign->customer_id);
										$count_rec_not_sent++;
									}
								}else {
									CommonHelper::setActivityLogs('MMS Campaign Status is Stop'.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'MMS Campaign',$smscampaign->customer_id);
									$count_rec_not_sent++;
								}
							}else{
								CommonHelper::setActivityLogs('MMS Campaign Mobile Number is Not available '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'MMS Campaign',$smscampaign->customer_id);
								$count_rec_not_sent++;
							}
						}
					}
					
					if($count_rec == $count_rec_not_sent || $count_rec > $count_rec_not_sent){
						$smscampaign->campaign_status = 'SENT';
						$smscampaign->save();
					}else if($count_rec < $count_rec_not_sent){
						$smscampaign->campaign_status = 'DRAFTS';
						$smscampaign->save();
					}
					
					$emailBody = '<h2 style="border-collapse: collapse; font-family: Arial,sans-serif; font-size:20px;">MMS Campaign Report</h2>
					<table align="left" cellpadding="2" cellspacing="0" width="100%" bgcolor="#ffffff" style="border-collapse: collapse; font-family: Arial,sans-serif; font-size:14px; text-align:left;border:1px solid #666666;" >
					<tr>
						<th style="border:1px solid #666666; padding:8px;">Your MMS Campaign Name</th>
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
						<th style="border:1px solid #666666; padding:8px;">Campaign Send MMS Successfully Count</th>
						<td style="border:1px solid #666666; padding:8px;">'.$count_rec.'</td>
					</tr>
					<tr>
						<th style="border:1px solid #666666; padding:8px;">Campaign Not Send MMS Count</th>
						<td style="border:1px solid #666666; padding:8px;">'.$count_rec_not_sent.'</td>
					</tr>
					</table>';
					
					$email = new TransactionalEmail();
					$email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
					$email->to_name      = $customer->getFullName();
					$email->to_email     = $customer->email;
					$email->from_name    = $options->get('system.common.site_name', 'Marketing website');
					$email->subject      = 'MMS Campaign Complete';
					$email->body         = $emailBody;
					$email->save();
					
					$adminemailBody = '<h2 style="border-collapse: collapse; font-family: Arial,sans-serif; font-size:20px;">MMS Campaign Report</h2>
					<table align="left" cellpadding="2" cellspacing="0" width="100%" bgcolor="#ffffff" style="border-collapse: collapse; font-family: Arial,sans-serif; font-size:14px; text-align:left;border:1px solid #666666;" >
					<tr>
						<th style="border:1px solid #666666; padding:8px;">Your MMS Campaign Name</th>
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
						<th style="border:1px solid #666666; padding:8px;">Campaign Send MMS Successfully Count</th>
						<td style="border:1px solid #666666; padding:8px;">'.$count_rec.'</td>
					</tr>
					<tr>
						<th style="border:1px solid #666666; padding:8px;">Campaign Not Send MMS Count</th>
						<td style="border:1px solid #666666; padding:8px;">'.$count_rec_not_sent.'</td>
					</tr>
					<tr>
						<th style="border:1px solid #666666; padding:8px;">Customer Username</th>
						<td style="border:1px solid #666666; padding:8px;">'.$customer->email.'</td>
					</tr>
					<tr>
						<th style="border:1px solid #666666; padding:8px;">Send Count Cost</th>
						<td style="border:1px solid #666666; padding:8px;"> USD '.($count_rec * 0.0190).'</td>
					</tr>
					</table>';
					
					$adminemail = new TransactionalEmail();
					$adminemail->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
					$adminemail->to_name      = 'Admin';
					$adminemail->to_email     = 'info@edatapay.com';
					$adminemail->from_name    = $options->get('system.common.site_name', 'Marketing website');
					$adminemail->subject      = 'MMS Campaign Bill Info';
					$adminemail->body         = $adminemailBody;
					$adminemail->save();
					
					$smscampaign->saveSendrecord($count_rec,$count_rec_not_sent,$smscampaign->sms_campaign_id);
				}else{
					CommonHelper::setActivityLogs('MMS Campaign Merchant is Not Active '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'MMS Campaign',$smscampaign->customer_id);
				}
			}
		}
    }
}
