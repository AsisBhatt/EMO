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

class Sms_campaignController extends Controller
{
    public function init()
    {
        $this->getData('pageStyles')->add(array('src' => AssetsUrl::js('datetimepicker/css/bootstrap-datetimepicker.min.css')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('datetimepicker/js/bootstrap-datetimepicker.min.js')));
		
		$languageCode = LanguageHelper::getAppLanguageCode();
        if (Yii::app()->language != Yii::app()->sourceLanguage && is_file(AssetsPath::js($languageFile = 'datetimepicker/js/locales/bootstrap-datetimepicker.'.$languageCode.'.js'))) {
            $this->getData('pageScripts')->add(array('src' => AssetsUrl::js($languageFile)));
        }
		
        //$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('campaigns.js')));
		$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('sms_campaigns.js')));
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
		$type = $request->getQuery('type');
		
        $smscampaign = new SmsCampaign('search');
        $smscampaign->unsetAttributes();
		
        $smscampaign->attributes  = (array)$request->getQuery($smscampaign->modelName, array());
		$smscampaign->customer_id = $customer->customer_id;
		$smscampaign->is_deleted = 1;
		if($type == 'sms'){
			$smscampaign->campaign_type = 'SMS';
			
			$page_title = Yii::t('sms_campaign', 'SMS Campaign');
			$page_meta_title = Yii::t('sms_campaign', 'SMS Campaign');
			$app_title = Yii::t('app', 'Sms Campaign');
		}else if($type == 'mms'){
			$smscampaign->campaign_type = 'MMS';
			
			$page_title = Yii::t('sms_campaign', 'MMS Campaign');
			$page_meta_title = Yii::t('sms_campaign', 'MMS Campaign');
			$app_title = Yii::t('app', 'MMS Campaign');
		}
		

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. $page_meta_title,
            'pageHeading'       => $page_title,
            'pageBreadcrumbs'   => array(
                $app_title
            )
        ));		

        $this->render('index', compact('smscampaign'));
    }
	
	/*public function actionCheckschedule()
	{
		$options = Yii::app()->options;
		//$smscampaignquota = new SmsCampaignQuota();
		$sms = new Sms();
		$criteria = new CDbCriteria();
		$criteria->select = 't.sms_campaign_id,t.customer_id,t.campaign_name,t.send_at,t.list_id,t.campaign_text,t.campaign_media,campaign_status';
		$criteria->addCondition('t.campaign_type="SMS"');
		$criteria->addCondition('t.campaign_status="PENDING"');
		$criteria->addCondition('t.send_at <= NOW()');
		$criteria->order  = 't.sms_campaign_id ASC';
		$sms_campaigns = SmsCampaign::model()->findAll($criteria);
		//print_r($sms_campaigns);exit;
		$sms_success_log = array();
		$sms_campaign_error_log = array();
		$count_rec = 0;
		$count_rec_not_sent = 0;
		$step = 100;
		if(is_array($sms_campaigns) && count($sms_campaigns)){
			foreach($sms_campaigns as $smscampaign){
				//echo $smscampaign->campaign_status;exit;
				//$sms_campaign_update = SmsCampaign::model()->findByPk($smscampaign->sms_campaign_id);
				//get customer details and customer model function.
				$customer = Customer::model()->findByPk((int)$smscampaign->customer_id);
				
				if($customer->status == 'active'){
					
					if($smscampaign->campaign_status == 'PENDING'){
						$smscampaign->campaign_status = 'PROCESSING';
						$smscampaign->save();
					}
					if($smscampaign->campaign_status == 'PROCESSING'){
						
						
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
						// echo '<pre>';
						// print_r($rows);
						// echo '</pre>';exit;
						
						$count_list_subscriber = Yii::app()->db->createCommand("SELECT count(subscriber_uid) FROM uic_list_subscriber WHERE status='confirmed' AND list_id ='".$smscampaign->list_id."'")->queryScalar();
						
						$divide = ceil($count_list_subscriber/$step);
						
						for($no=0; $no<$divide; $no++)
						{
							$start_no = $no*$step;
							
							$receive_sms_array = Yii::app()->db->createCommand("SELECT ulfv.value, lis.subscriber_id, lis.subscriber_uid FROM uic_list as li INNER JOIN uic_list_subscriber as lis on li.list_id = lis.list_id inner join uic_list_field as uif on li.list_id = uif.list_id inner join uic_list_field_value as ulfv on lis.subscriber_id = ulfv.subscriber_id and uif.field_id = ulfv.field_id where li.list_id = '".$smscampaign->list_id."' and uif.label = 'MOBILE' and lis.status='confirmed' LIMIT ".$start_no.",".$step)->queryAll();
							//GROUP BY ulfv.value
							
							/*if($customer->getGroupOption('smssending.sms_quota', -1) != -1){
								
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
								if($smscampaign->campaign_status == 'PROCESSING'){
									if($rec['value'] != ''){
										//echo $rec['value'].'<br/>';
										
											$sms_success_log[] = array($smscampaign->campaign_status);
											if(!$customer->getIsSmsOverQuota())
											{
												$status = $sms->shceduleSms($rec['value'], $smscampaign->campaign_media,$newmessage, $smscampaign->customer_id);
												
												if($status == 'sent'){
													$count_rec++;
												}else{
													$sms_campaign_error_log['error'][] = $status;
													$count_rec_not_sent++;
												}
											}else{
												$sms_campaign_error_log['error']['validity_exiry'][] = 'Your Plan Validity Over Please Contact Your admin.';
												$count_rec_not_sent++;
											}
									}else{
										$sms_campaign_error_log['error']['number_blank'][] = 'Mobile Number is Not available.';
										$count_rec_not_sent++;
									}
								}else if($smscampaign->campaign_status == 'STOP'){
										$sms_success_log[$rec['value']][] = array($smscampaign->campaign_status);
										$sms_campaign_error_log['error']['campaign_stop'][] = 'Campaign has been Stop by Merchant.';
										$count_rec_not_sent++;
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
							<th style="border:1px solid #666666; padding:8px;">Campaign Send SMS Successfully Count</th>
							<td style="border:1px solid #666666; padding:8px;">'.$count_rec.'</td>
						</tr>
						<tr>
							<th style="border:1px solid #666666; padding:8px;">Campaign Not Send SMS Count</th>
							<td style="border:1px solid #666666; padding:8px;">'.$count_rec_not_sent.'</td>
						</tr>
						<tr>
							<th style="border:1px solid #666666; padding:8px;">Customer Username</th>
							<td style="border:1px solid #666666; padding:8px;">'.$customer->email.'</td>
						</tr>
						<tr>
							<th style="border:1px solid #666666; padding:8px;">Send Count Cost</th>
							<td style="border:1px solid #666666; padding:8px;">'.($count_rec * 0.0075).'</td>
						</tr>
						</table>';
						
						$adminemail = new TransactionalEmail();
						$adminemail->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
						$adminemail->to_name      = 'Admin';
						$adminemail->to_email     = 'ashish.b@arityinfoway.com';
						$adminemail->from_name    = $options->get('system.common.site_name', 'Marketing website');
						$adminemail->subject      = 'SMS Campaign Bill Info';
						$adminemail->body         = $adminemailBody;
						$adminemail->save();
						
						$sms_campaign_id = $smscampaign->saveSendrecord($count_rec,$count_rec_not_sent,$smscampaign->sms_campaign_id);
					}
				}else{
					$sms_campaign_error_log['error']['status_inactive'] = 'Customer Status is Inactive.';
					
				}
				//$smscampaignquota->updateSendrecord($smscampaign->sms_campaign_id,$count_rec,$count_rec_not_sent);
			}
			
			$this->setData(array(
				//'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. $page_meta_title,
				'pageHeading'       => 'SMS Check',
				'pageBreadcrumbs'   => array(
					//Yii::t('smscampaign', 'smscampaign') => $this->createUrl('sms_campaign/index'),
					Yii::t('app', 'Create new'),
				)
			));
			$this->render('check_sms', compact('sms_success_log','sms_campaign_error_log'));
		}		
	}*/
	
	public function actionCreate()
	{
		$smscampaign   = new SmsCampaign();
		$smscampaign->scenario = 'step-name';
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
		$customer = Yii::app()->customer->getModel();
		$page_title = '';
		$type = '';
		if($request->getQuery('type') != '' && $request->getQuery('type') == 'sms'){
			$type = 'SMS';
			$page_title .= Yii::t('smscampaign', 'Create new SMS Campaign');
			$page_meta_title = Yii::t('smscampaign', 'Create new SMS Campaign');
		}else if($request->getQuery('type') != '' && $request->getQuery('type') == 'mms'){
			$type = 'MMS';
			$page_title .= Yii::t('smscampaign', 'Create new MMS Campaign');
			$page_meta_title = Yii::t('smscampaign', 'Create new MMS Campaign');
		}
		$customer_id = $customer->customer_id;
		
		//Get Current Flowrote number From Company Information
		$customer_company = CustomerCompany::model()->findByAttributes(array('customer_id' => $customer_id));
		
		//Get Stop Count From SMS Reply Table
		$sms_rply_stop_count = Yii::app()->db->createCommand("SELECT COUNT(sms_rply_id) FROM uic_sms_rply WHERE customer_id='".$customer->customer_id."' AND sms_rply_to_number='".$customer_company->flowroute_sms_num."' AND sms_rply_body LIKE 'STOP'")->queryScalar();

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($smscampaign->modelName, array()))) {
			//get pending list subscriber count.
			$get_pending_count = $this->getPendingListCount($customer_id);
			
			//get remaining quota.
			$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) == -1 ? 'UNLIMITED' : ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark()));
			
			if($sms_rply_stop_count < 100){
				if($get_pending_count < $rem_quota || $rem_quota == 'UNLIMITED'){
					//$smsschedule = $request->getPost('smsschedule');
					$smscampaign->customer_id = $customer_id;
					$smscampaign->campaign_name = $attributes['campaign_name'];
					$smscampaign->campaign_type = $type;
					$smscampaign->campaign_status = 'DRAFTS';
					$smscampaign->send_at = date('Y-m-d H:i:s');
					$smscampaign->save();
					
					if (!$smscampaign->save()) {
						CommonHelper::setActivityLogs($type.' Campaign Create Not Save with error '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),0,$smscampaign->tableName(),$type.' Campaign Create',$customer_id);
						$notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
					} else {
						CommonHelper::setActivityLogs($type.' Campaign Create '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),$smscampaign->sms_campaign_id,$smscampaign->tableName(),$type.' Campaign Create',$customer_id);
						$notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
						/*if($smsschedule == 1){
							$notify->addSuccess(Yii::t('app', 'Your Sms Schedule Set has been successfully!'));
						}else{
							$notify->addSuccess(Yii::t('app', 'Your Send Sms Start Now!'));
						}*/
						
					}

					Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
						'controller'=> $this,
						'success'   => $notify->hasSuccess,
						'smscampaign'  => $smscampaign,
					)));
					
					if ($collection->success) {
						$this->redirect(array('sms_campaign/setup','sms_campaign_id' => $smscampaign->sms_campaign_id));
					}
				}else{
					CommonHelper::setActivityLogs($type.' Campaign Create Merchant Quota is Over '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),0,$smscampaign->tableName(),$type.' Campaign Create',$customer_id);
					$type = (isset($_GET['type']) && $_GET['type'] == 'sms' ? 'sms' : 'mms');
					$notify->addError(Yii::t('app', 'Your Quota limit is exceeded! either remove your old campaign or upgrade your plan or please check your remaining quota and SMS list Subscriber count. if any query please contact support. <a href="https://support.beelift.com/" target="__blanck">Click</a>'));
					$this->redirect(array('sms_campaign/index/type/'.$type));
				}
			}else{
				CommonHelper::setActivityLogs($type.' Campaign Create Merchant Stop Count More than 100 '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),0,$smscampaign->tableName(),$type.' Campaign Create',$customer_id);
				Yii::app()->notify->addError(Yii::t('customers', 'Your Stop Count More than 100 So Your Beelift Number is Suspended! Please Contact Your Support Team.'));
			}
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. $page_meta_title,
            'pageHeading'       => $page_title,
            'pageBreadcrumbs'   => array(
                Yii::t('smscampaign', 'smscampaign') => $this->createUrl('sms_campaign/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('step-name', compact('smscampaign'));
	}
	
	public function actionSetup($sms_campaign_id){
		$smscampaign = SmsCampaign::model()->findByPk((int)$sms_campaign_id);
		$smscampaign->scenario = 'step-setup';
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
		$base_url = 'https://'.Yii::app()->getRequest()->serverName;
		$target_dir = "../myuploads/";
		$image_types = array('image/jpg','image/jpeg','image/bmp','image/png','image/gif');
		$customer = Yii::app()->customer->getModel();
		$customer_id = (int)Yii::app()->customer->getId();
		
		$criteria = new CDbCriteria();
		$criteria->compare('t.customer_id', $customer_id);
		$imagegallery = ImageGallery::model()->findAll($criteria);
		
		$list = new Lists();
        $criteria = new CDbCriteria();
		$criteria->select = 't.list_id, t.customer_id, t.name, t.display_name, t.status , t.date_added';
        //$criteria->compare('t.customer_id', $customer_id);
		$criteria->addInCondition('t.customer_id', array((int)$customer_id,"0"));
		//$criteria->compare('customer_id', 'active');
		$criteria->compare('t.status', 'active');
		$criteria->order = 't.list_id DESC';
		$lists = $list->findAll($criteria);	
		$list_count = count($lists);
		$mylists =array();
		foreach ($lists as $index => $list) {
			$mylists[$list->list_id] = $list->name;
		}
		
		if ($request->isPostRequest && ($attributes = (array)$request->getPost($smscampaign->modelName, array()))) {
			//get pending list subscriber count.
			$get_pending_count = $this->getPendingListCount($customer_id);
			
			//Current list count.
			$count_list = ($this->getListcount($attributes['list_id']) + $get_pending_count);
			
			//get remaining quota.
			$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
			
			//$getlast_remQuota = Yii::app()->db->createCommand("SELECT MIN(remaining_quota) as remaining_quota FROM uic_sms_campaign_quota WHERE quota_status='RESERVE' AND customer_id='".$customer_id."'")->queryRow();
			
			$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
			//$rem_quota = ($getlast_remQuota['remaining_quota'] != '' ? $getlast_remQuota['remaining_quota'] : );
			
			
			//if($count_list < $rem_quota){
				$select_media = $request->getPost('select_media');
				
				$media_url = '';
				if($select_media == 'FRM_LOCAL'){
					$media_size =  round($_FILES['SmsCampaign']['size']['campaign_media']/1072);
					$file_type = $_FILES['SmsCampaign']['type']['campaign_media'];
					
					$upload_filename = $_FILES["SmsCampaign"]["name"]['campaign_media'];
					$upload_filename = str_replace(" ","",$upload_filename);
					
					$uploaded=0;
					$target_file = $target_dir . basename($upload_filename);
					$target_file = $target_dir . $upload_filename;
					
					if (copy($_FILES["SmsCampaign"]["tmp_name"]['campaign_media'], $target_file)) {
						$uploaded=1;
						$filename = $_FILES["SmsCampaign"]["name"]['campaign_media'];
						$filename = str_replace(" ","",$filename);
					}
					
					if(in_array($file_type,$image_types)){
						
						if($media_size > 650){
							Yii::import('customer.extensions.image.Image');
							$image = new Image($target_file);
							$image->quality(70);
							$image->save();
							
							$upload_image_size = filesize($image->file);
							$image_size = $this->sizeFilter($upload_image_size);
						}
					}
					
					$file_size = (isset($image_size) && $image_size != '' ? $image_size : $media_size);
					
					$media_url = $base_url.'/myuploads/'.$filename;
				}else if($select_media == 'FRM_GALLERY'){
					$media_url = $request->getPost('choose_file');
				}
				
				if($select_media == 'FRM_GALLERY' || (int)$file_size < 730){
					
					if($customer->getGroupOption('smssending.sms_quota', -1) != -1)
					{
						
						if($rem_quota > 0){
							
							$remaining_quota = ($rem_quota - $count_list);
							
							$smscampaign->campaign_text = $attributes['campaign_text'];
							$smscampaign->campaign_media = $media_url;
							$smscampaign->list_id = $attributes['list_id'];
							$smscampaign->campaign_status = 'DRAFTS';
							$smscampaign->save();
							
							/*$campaign_quota = new SmsCampaignQuota();
							$campaign_quota->customer_id = $customer_id;
							$campaign_quota->sms_campaign_id = $sms_campaign_id;
							$campaign_quota->reserve_quota = $count_list;
							$campaign_quota->remaining_quota = $remaining_quota;
							$campaign_quota->total_quota = $customer->getGroupOption('smssending.sms_quota', -1);
							$campaign_quota->quota_status = 'RESERVE';
							$campaign_quota->quota_created = date("Y-m-d H:i:s");
							$campaign_quota->save();*/
							
						}else{
							$notify->addError(Yii::t('app', 'Your Quota is Over Please contact youe admin!'));
						}
					}else{
						$smscampaign->campaign_text = $attributes['campaign_text'];
						$smscampaign->campaign_media = $media_url;
						$smscampaign->list_id = $attributes['list_id'];
						$smscampaign->campaign_status = 'PENDING-CONFORMATION';
						$smscampaign->save();
						
					}
					
					CommonHelper::setActivityLogs($smscampaign->campaign_type.' Campaign Setup '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),$smscampaign->sms_campaign_id,$smscampaign->tableName(),$smscampaign->campaign_type.' Campaign Create',$customer_id);
					
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
						$this->redirect(array('sms_campaign/conformation','sms_campaign_id' => $smscampaign->sms_campaign_id));
					}
				}else{
					CommonHelper::setActivityLogs($smscampaign->campaign_type.' Campaign Setup File Size Very High '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),$smscampaign->sms_campaign_id,$smscampaign->tableName(),$smscampaign->campaign_type.' Campaign Create',$customer_id);
					$notify->addError(Yii::t('app', 'Your File Size Very High!'));
				}
			/*}else{
				$type = ($smscampaign->campaign_type == 'SMS' ? 'sms' : 'mms');
				$smscampaign->delete();
				$notify->addError(Yii::t('app', 'Your Quota limit is exceeded! either remove your old campaign or upgrade your plan or please check your remaining quota and SMS list Subscriber count. if any query please contact support. <a href="https://support.beelift.com/" target="__blanck">Click</a>'));
				$this->redirect(array('sms_campaign/index/type/'.$type));
			}*/
		}
		
		if($smscampaign->campaign_type != '' && $smscampaign->campaign_type == 'MMS'){
			$page_meta_title = Yii::t('smscampaign', 'Create new MMS Campaign');
			$page_title = Yii::t('smscampaign', 'Create new MMS Campaign');
			$app_title = Yii::t('app', 'MMS Campaign Setup');
		}else if($smscampaign->campaign_type != '' && $smscampaign->campaign_type == 'SMS'){
			$page_meta_title = Yii::t('smscampaign', 'Create new SMS Campaign');
			$page_title = Yii::t('smscampaign', 'Create new SMS Campaign');
			$app_title = Yii::t('app', 'SMS Campaign Setup');
		}
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. $page_meta_title,
            'pageHeading'       => $page_title,
            'pageBreadcrumbs'   => array(
                Yii::t('smscampaign', 'smscampaign') => $this->createUrl('sms_campaign/index/type/'.$smscampaign->campaign_type),
                $app_title,
            )
        ));
		
		$this->render('step-setup', compact('smscampaign', 'mylists','imagegallery'));
	}
	
	public function actionConformation($sms_campaign_id){
		$smscampaign = SmsCampaign::model()->findByPk((int)$sms_campaign_id);
		$smscampaign->scenario = 'step-conformation';
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
		$customer_id = (int)Yii::app()->customer->getId();
		
		if ($request->isPostRequest && ($attributes = (array)$request->getPost($smscampaign->modelName, array()))) {
			if($smsschedule == 1){
				$smscampaign->send_at = date('Y-m-d H:i:s',strtotime($attributes['send_at']));
			}else{
				$smscampaign->send_at = date('Y-m-d H:i:s',strtotime('+ 1 minute'));
			}
			$smscampaign->campaign_status = 'PENDING';
			$smscampaign->save();
			
			CommonHelper::setActivityLogs($smscampaign->campaign_type.' Campaign Conformatioin '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),$smscampaign->sms_campaign_id,$smscampaign->tableName(),$smscampaign->campaign_type.' Campaign Create',$customer_id);
			
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
                $this->redirect(array('sms_campaign/index/type/'.strtolower($smscampaign->campaign_type)));
            }
		}
		
		if($smscampaign->campaign_type != '' && $smscampaign->campaign_type == 'MMS'){
			$page_meta_title = Yii::t('smscampaign', 'Create new MMS Campaign');
			$page_title = Yii::t('smscampaign', 'Create new MMS Campaign');
			$app_title = Yii::t('app', 'MMS Campaign Setup');
		}else if($smscampaign->campaign_type != '' && $smscampaign->campaign_type == 'SMS'){
			$page_meta_title = Yii::t('smscampaign', 'Create new SMS Campaign');
			$page_title = Yii::t('smscampaign', 'Create new SMS Campaign');
			$app_title = Yii::t('app', 'SMS Campaign Setup');
		}
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. $page_meta_title,
            'pageHeading'       => $page_title,
            'pageBreadcrumbs'   => array(
                Yii::t('smscampaign', 'smscampaign') => $this->createUrl('sms_campaign/index/type/'.$smscampaign->campaign_type),
                $app_title,
            )
        ));
		
		$this->render('step-confirm', compact('smscampaign'));
	}
	
	public function actionUpdate($sms_campaign_id){
		$smscampaign = SmsCampaign::model()->findByPk((int)$sms_campaign_id);
		
		$page_title = ($smscampaign->campaign_type == 'MMS' ? Yii::t('sms_template', 'Update MMS Template') : Yii::t('sms_template', 'Update SMS Template'));
		
		$page_meta_title = ($smscampaign->campaign_type == 'MMS' ? Yii::t('sms_template', 'Update MMS Template') : Yii::t('sms_template', 'Update SMS Template'));
		
		$app_title = ($smscampaign->campaign_type == 'MMS' ? Yii::t('app', 'Update MMS Template') : Yii::t('app', 'Update SMS Template'));
		
		$customer_id = (int)Yii::app()->customer->getId();

        if (empty($smscampaign)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($smscampaign->modelName, array()))) {
			
			//$attributes['customer_id'] = $customer_id;
			$smscampaign->customer_id = $customer_id;
			$smscampaign->campaign_name = $attributes['campaign_name'];
			$smscampaign->campaign_type = $smscampaign->campaign_type;
			$smscampaign->campaign_status = 'DRAFTS';
			$smscampaign->save();
			//$smscampaign->campaign_text = $attributes['campaign_text'];
			//$smscampaign->list_id = $attributes['list_id'];
			/*if($attributes['send_at'] != ''){
				if($smsschedule == 1){
					$smscampaign->send_at = date('Y-m-d H:i:s',strtotime($attributes['send_at']));
				}else{
					$smscampaign->send_at = date('Y-m-d H:i:s',strtotime($attributes['send_at'].' + 2 minute'));
				}
			}
			$smscampaign->campaign_status = 'PENDING';*/
			
            //$smscampaign->attributes = $attributes;
            if (!$smscampaign->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }
			
			CommonHelper::setActivityLogs($smscampaign->campaign_type.' Campaign Update '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),$smscampaign->sms_campaign_id,$smscampaign->tableName(),$smscampaign->campaign_type.' Campaign Update',$customer_id);

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'smscampaign'  => $smscampaign,
            )));

            if ($collection->success) {
                $this->redirect(array('sms_campaign/setup','sms_campaign_id' => $smscampaign->sms_campaign_id));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. $page_meta_title,
            'pageHeading'       => $page_title,
            'pageBreadcrumbs'   => array(
                //Yii::t('customers', 'Customers') => $this->createUrl('customers/index'),
                $app_title,
            )
        ));

        $this->render('step-name', compact('smscampaign'));
	}
	
	public function actionDelete($id){
		$smscampaign = SmsCampaign::model()->findByPk((int)$id);
		//$sms_campaign_quota = SmsCampaignQuota::model()->findByAttributes(array('sms_campaign_id' => (int)$id));
		

        if (empty($smscampaign)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
		$smscampaign->campaign_status = 'DRAFTS';
		$smscampaign->is_deleted = 0;
		$smscampaign->save();
		
		CommonHelper::setActivityLogs($smscampaign->campaign_type.' Campaign Delete '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),$id,$smscampaign->tableName(),$smscampaign->campaign_type.' Campaign Delete',(int)Yii::app()->customer->getId());

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
	
	public function actionStop($id){
		$smscampaign = SmsCampaign::model()->findByPk((int)$id);
		
		if (empty($smscampaign)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
		$type = ($smscampaign->campaign_type == 'SMS' ? 'sms' : 'mms');
		
		$smscampaign->campaign_status = 'STOP';
		$smscampaign->save();
		
		CommonHelper::setActivityLogs($smscampaign->campaign_type.' Campaign Stop '.str_replace(array('{{','}}'),'',$smscampaign->tableName()),$id,$smscampaign->tableName(),$smscampaign->campaign_type.' Campaign Stop',(int)Yii::app()->customer->getId());
		
		$request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully updated!'));
            $redirect = $request->getPost('returnUrl', array('sms_campaign/index/type/'.$type));
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
            'utcDateTime'     => $utcDateTime,
			'localeDateTime'  => $localeDateTime,
        ));
    }
	
	protected function sizeFilter( $bytes )
	{
		$label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
		for( $i = 0; $bytes >= 1024 && $i < ( count( $label ) -1 ); $bytes /= 1024, $i++ );
		return round($bytes, 2);
	}
	
	protected function getListcount($list_id)
	{
		$count_list_sub = Yii::app()->db->createCommand("SELECT ulfv.value FROM uic_list as li INNER JOIN uic_list_subscriber as lis on li.list_id = lis.list_id inner join uic_list_field as uif on li.list_id = uif.list_id inner join uic_list_field_value as ulfv on lis.subscriber_id = ulfv.subscriber_id and uif.field_id = ulfv.field_id where li.list_id = '".$list_id."' and uif.label = 'Mobile' and lis.status='confirmed' AND ulfv.value != '' GROUP BY ulfv.value")->queryAll();
		return count($count_list_sub);
	}
	
	protected function getPendingListCount($customer_id){
		$pending_campaign_array = Yii::app()->db->createCommand("SELECT list_id FROM uic_sms_campaign WHERE campaign_status='PENDING' AND customer_id='".$customer_id."' ")->queryAll();
		$count_subscriber = array();
		foreach($pending_campaign_array as $pending_campaign){
			$count_list_sub = Yii::app()->db->createCommand("SELECT ulfv.value FROM uic_list as li INNER JOIN uic_list_subscriber as lis on li.list_id = lis.list_id inner join uic_list_field as uif on li.list_id = uif.list_id inner join uic_list_field_value as ulfv on lis.subscriber_id = ulfv.subscriber_id and uif.field_id = ulfv.field_id where li.list_id = '".$pending_campaign['list_id']."' and uif.label = 'Mobile' and lis.status='confirmed' AND ulfv.value != '' GROUP BY ulfv.value")->queryAll();
		 $count_subscriber[] = count($count_list_sub);
		}
		return array_sum($count_subscriber);
	}
}
