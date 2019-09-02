<?php
//$slist_id= 5;
			$slist_id= $attributes[mobile];
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
					$rows[] = $subscriberRow;
				//}

			}
			
			
//$mobile = $attributes['mobile'];
			//$message = urlencode($attributes['message']) ;
			
			/*if($customer->getGroupOption('smssending.sms_quota', -1) != -1){
				$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
				
				if(count($all_reciepients) >= $rem_quota){
					$output = array_slice($all_reciepients, 0, $rem_quota);
					$count_rec_not_sent = (count($all_reciepients) - count($output));
					if(is_array($output) && count($output)){
						$all_reciepients = $output;
					}
				}
			}*/
			
			
			/*$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
			//echo $rem_quota;exit;

			if(count($rows) >= $rem_quota){
				$output = array_slice($rows, 0, $rem_quota);
				$count_rec_not_sent = (count($rows) - count($output));
				if(is_array($output) && count($output)){
					$rows = $output;
				}
			}*/
			
			/*foreach($rows as $rec){
				$fname = @$rec['columns'][3];
				$lname = @$rec['columns'][4];
				$mobile = @$rec['columns'][5];
				
				$shortcodes = array("{{firstname}}", "{{lastname}}", "{{company_name}}");
				$replacecodes   = array($fname, $lname, $company_info['name']);
				$newmessage = str_replace($shortcodes, $replacecodes, $messageorg);
				
				if($mobile){
					if(!$customer->getIsSmsOverQuota()){
						$status = $sms->sendsms($mobile, $newmessage);
						//$status= $sendsms->postSms($apiurl,$authkey,$sender,$route,$country, $mobile, $message);
						if($status){
							$count_rec++;
						}
					}
				}
			}*/			
?>