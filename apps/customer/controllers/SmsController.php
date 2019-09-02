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

class SmsController extends Controller
{
    public function init()
    {
        $this->getData('pageStyles')->add(array('src' => AssetsUrl::js('datetimepicker/css/bootstrap-datetimepicker.min.css')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('datetimepicker/js/bootstrap-datetimepicker.min.js')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('campaigns.js')));
		$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('dashboard.js')));
		$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('Chart.min.js')));
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
        $sms = new Sms('search');
        $sms->unsetAttributes();

		//print_r($sms);
        $sms->attributes  = (array)$request->getQuery($sms->modelName, array());
        $sms->customer_id = (int)Yii::app()->customer->getId();
		if(isset($_GET['mms'])){
			$sms->type =  "MMS";
		}else{
			$sms->type =  "SMS";
		}
		$page_heading = '';
		if(isset($_GET['mms'])){
			$page_heading .= Yii::t('campaigns', 'Sent mms');
		}else{
			$page_heading .= Yii::t('campaigns', 'Sent sms');
		}

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('campaigns', 'Sent sms'),
            'pageHeading'       => $page_heading,
            'pageBreadcrumbs'   => array(
                Yii::t('campaigns', 'Sms') => $this->createUrl('sms/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('index', compact('sms'));
    }
	
	public function actionReceive()
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
								
								$subscriber_model->getStop($stop_number_array);
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
		
		$this->redirect(array('sms/smsrplylist'));
	}
	public function actionsmsRplyList(){
		
		$request = Yii::app()->request;
		$notify  = Yii::app()->notify;
		$customer = Yii::app()->customer->getModel();
		
		$smsrply = new SmsRply('search');
		$smsrply->unsetAttributes();
		
		$smsrply->attributes = (array)$request->getQuery($smsrply->modelName, array());
		$smsrply->sms_rply_to_number  = $customer->company->flowroute_sms_num;
		$smsrply->customer_id  = $customer->customer_id;
		
		$this->setData(array(
			'pageMetaTitle'		=> $this->data->pageMetaTitle .' | '. Yii::t('sms', 'SmsRply'),
			'pageHeading'		=> Yii::t('sms', 'SmsReplay'),
			'pageBreadcrumbs'	=> array(
				//Yii::t('smssetting','smssetting') => $this->createUrl('smssetting/index'),
				Yii::t('app', 'Sms Rply'),
			)
		));
		
		$this->render('smsrplylist',compact('smsrply'));
	}
	
	public function actionReplysubscriber($id)
	{
		$model = SmsRply::model()->findByAttributes(array('sms_rply_id' => $id));
		
		$this->render('smsrplylist',array('model' => $model));
	}
		
   /*--------------------*/
   public function actionSendsms()
    {
		
		$request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
		
		//get pendign Campaign Subscriber Count.
		$camp_subsciber_count =  $this->getPendingListCount($customer->customer_id);
		$customer_company = CustomerCompany::model()->findByAttributes(array('customer_id' => $customer->customer_id));
		
		$sms_rply_stop_count = Yii::app()->db->createCommand("SELECT COUNT(sms_rply_id) FROM uic_sms_rply WHERE customer_id='".$customer->customer_id."' AND sms_rply_to_number='".$customer_company->flowroute_sms_num."' AND sms_rply_body LIKE 'STOP'")->queryScalar();
		
		//get total Remaining SMS Quota.
		
		
		if((int)$customer->getGroupOption('smssending.sms_quota', -1) != -1){
			$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
			$total_remain_quota = ($rem_quota - $camp_subsciber_count);
		}else{
			$rem_quota = 'UNLIMITED';
			$total_remain_quota = 'UNLIMITED';
		}
		
		//$getlast_remQuota = Yii::app()->db->createCommand("SELECT MIN(remaining_quota) as remaining_quota FROM uic_sms_campaign_quota WHERE quota_status='RESERVE' AND customer_id='".$customer->customer_id."'")->queryRow();
		
		$sms    = new Sms();
		
		//$cid= $customer->customer_id; 
		//$customer_id= (int)Yii::app()->customer->getId();
		//print_r($customer);	

        //$list = new Lists('search');
       // $list->unsetAttributes();
       // $list->attributes = (array)$request->getQuery($list->modelName, array());
       // $list->customer_id = (int)Yii::app()->customer->getId();		

		
        if (Yii::app()->request->isPostRequest && $attributes = Yii::app()->request->getPost($sms->modelName)) {
			// if (!$customer->getIsSmsOverQuota()){
				
				$mobile = $attributes['mobile'];
				$message = urlencode($attributes['message']) ;
				$messageorg = $attributes['message'] ;
				
				//$sendsms = Yii::app()->smsSend;
				$count_rec= 0;
				$all_reciepients = explode (',', $mobile);
				
				/*if($customer->getGroupOption('smssending.sms_quota', -1) != -1){
					
					if(count($all_reciepients) > 2){
						
						$output = array_slice($all_reciepients, 0, 2);
						$count_rec_not_sent = (count($all_reciepients) - count($output));
						if(is_array($output) && count($output)){
							$all_reciepients = $output;
						}
					}
				}*/
				//echo count($all_reciepients);
				//echo $total_remain_quota;exit;
				if($sms_rply_stop_count < 100){
					if(count($all_reciepients) <= $total_remain_quota || $total_remain_quota == 'UNLIMITED'){
						foreach ($all_reciepients  as $key=>$rec){
							
							$status_array = Yii::app()->db->createCommand("SELECT lisu.status FROM uic_list_field_value lival, uic_list li, uic_list_subscriber lisu WHERE lival.subscriber_id = lisu.subscriber_id  AND li.list_id = lisu.list_id AND li.customer_id = '".$customer->customer_id."' AND lival.value = '".$rec."' GROUP BY lival.subscriber_id")->queryRow();
							
							if($status_array['status'] != 'stop'){
								if(!$customer->getIsSmsOverQuota()){
									$status = $sms->sendsms($rec, $messageorg);
									if($status){
										$sms_id = explode("_",$status);
										CommonHelper::setActivityLogs('Sendsms '.str_replace(array('{{','}}'),'',$sms->tableName()),$sms_id[1],$sms->tableName(),'Sendsms',$customer->customer_id);
										$count_rec++;
									}else{
										CommonHelper::setActivityLogs('Sendsms Fail Check Flowroute '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendsms',$customer->customer_id);
										$count_rec_not_sent++;
									}
								}else{
									CommonHelper::setActivityLogs('Sendsms Fail Merchant Validity is Over '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendsms',$customer->customer_id);
									$count_rec_not_sent = count($all_reciepients);
									Yii::app()->notify->addError(Yii::t('customers', 'Your Plan Validity is over please contact support.'));
								}
							}else{
								CommonHelper::setActivityLogs('Sendsms Fail Merchant Subscriber Status is Stop '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendsms',$customer->customer_id);
								Yii::app()->notify->addError(Yii::t('customers', 'Your Mobile has been Stop! Please check your subscriber list and Mobile number.'));
							}
							
						}
					}else{
						CommonHelper::setActivityLogs('Sendsms Fail Merchant SMS Quota is Over '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendsms',$customer->customer_id);
						Yii::app()->notify->addError(Yii::t('customers', 'Your SMS Quota is Over! Plese Contact Your Support.'));
					}
					
					
					if((int)$count_rec > 0){
						Yii::app()->notify->addSuccess(Yii::t('customers', ' Sms sent successfully to '.$count_rec.' reciepient!'));
					}
					if((int)$count_rec_not_sent > 0){
						Yii::app()->notify->addError(Yii::t('customers', ' Sms not sent  '.$count_rec_not_sent.' reciepient!'));
					}
				}else{
					CommonHelper::setActivityLogs('Sendsms Fail Merchant Stop Count More than 100 '.str_replace(array('{{','}}'),'',$sms->tableName()),$sms->sms_id,$sms->tableName(),'Sendmms',$customer->customer_id);
					Yii::app()->notify->addError(Yii::t('customers', 'Your Stop Count More than 100 So Your Beelift Number is Suspended! Please Contact Your Support Team.'));
				}

				
			// }
			/*else{
				// Yii::app()->notify->addError(Yii::t('customers', 'Sms could not sent ! Plese contact your admin.'));
			// }
			
            /*
			Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => Yii::app()->notify->hasSuccess,
                'customer'  => $customer,
            )));
			*/
			

			/*
			$sms = Yii::app()->smsSend;
			$r= $sms->postSms($apiurl,$authkey,$sender,$route,$country, $mobile, $message);
			if($r){
				Yii::app()->notify->addSuccess(Yii::t('customers', 'Sms sent successfully !'));			
			}else{
				Yii::app()->notify->addSuccess(Yii::t('customers', 'Sms could not sent !'));			
			};
			*/
			
        }
        
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Send sms'),
            'pageHeading'       => Yii::t('customers', 'Send sms'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/sendsms'),
                Yii::t('app', 'Send sms')
            )
        ));
        
        $this->render('sendsms', compact('sms','total_remain_quota'));
    }
	
	public function actionSendmms()
	{
		
		$request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
		$base_url = 'https://'.Yii::app()->getRequest()->serverName;
		$target_dir = "../myuploads/";
		$image_types = array('image/jpg','image/jpeg','image/bmp','image/png','image/gif');
		$criteria = new CDbCriteria();
		$criteria->compare('t.customer_id', $customer_id);
		$imagegallery = ImageGallery::model()->findAll($criteria);
		
		$customer_company = CustomerCompany::model()->findByAttributes(array('customer_id' => $customer->customer_id));
		
		$sms_rply_stop_count = Yii::app()->db->createCommand("SELECT COUNT(sms_rply_id) FROM uic_sms_rply WHERE customer_id='".$customer->customer_id."' AND sms_rply_to_number='".$customer_company->flowroute_sms_num."' AND sms_rply_body LIKE 'STOP'")->queryScalar();
		
		//get pendign Campaign Subscriber Count.
		$camp_subsciber_count =  $this->getPendingListCount($customer->customer_id);
		
		//get total Remaining SMS Quota.
		/*$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());*/
		
		if((int)$customer->getGroupOption('smssending.sms_quota', -1) != -1){
			$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
			$total_remain_quota = ($rem_quota - $camp_subsciber_count);
		}else{
			$rem_quota = 'UNLIMITED';
			$total_remain_quota = 'UNLIMITED';
		}
		
		$sms = new Sms();
		
		if (Yii::app()->request->isPostRequest && $attributes = Yii::app()->request->getPost($sms->modelName)) 
		{
			$mobile = $attributes['mobile'];
			$message = urlencode($attributes['message']) ;
			$messageorg = $attributes['message'] ;
			$all_reciepients = explode (',', $mobile);
			//echo $_POST['choose_file'];exit;
			if($_POST['select_media'] == 'FRM_GALLERY'){
				$ch = curl_init($_POST['choose_file']);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, TRUE);
				curl_setopt($ch, CURLOPT_NOBODY, TRUE);
				$data = curl_exec($ch);
				$g_image_size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
				$g_image_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
				
				$g_image_size = $this->sizeFilter($g_image_size);
				curl_close($ch);
			}
			
			$media_size =  ($_POST['select_media'] == 'FRM_GALLERY' ? $g_image_size : round($_FILES['Sms']['size']['media']/1072));
			$file_type = ($_POST['select_media'] == 'FRM_GALLERY' ? $g_image_type : $_FILES['Sms']['type']['media']);
			
			if($_POST['select_media'] == 'FRM_LOCAL'){
				//upload media .
				$upload_filename = $_FILES["Sms"]["name"]['media'];
				$upload_filename = str_replace(" ","",$upload_filename);
				
				$uploaded=0;
				$target_file = $target_dir . basename($upload_filename);
				$target_file = $target_dir . $upload_filename;
				
				if (copy($_FILES["Sms"]["tmp_name"]['media'], $target_file)) {
					$uploaded=1;
					$filename = $_FILES["Sms"]["name"]['media'];
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
			}
			
			$file_size = ($image_size != '' ? $image_size : $media_size);
			
			$media_url = ($_POST['select_media'] == 'FRM_GALLERY' ? trim($_POST['choose_file']) : $base_url.'/myuploads/'.$filename);
			if($sms_rply_stop_count < 100){
				if((int)$file_size < 730 || $_POST['select_media'] == 'FRM_GALLERY')
				{
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
					
					if(count($all_reciepients) <= $total_remain_quota || $total_remain_quota == 'UNLIMITED'){
						foreach ($all_reciepients  as $key=>$rec){
							
							$status_array = Yii::app()->db->createCommand("SELECT lisu.status FROM uic_list_field_value lival, uic_list li, uic_list_subscriber lisu WHERE lival.subscriber_id = lisu.subscriber_id  AND li.list_id = lisu.list_id AND li.customer_id = '".$customer->customer_id."' AND lival.value = '".$rec."' GROUP BY lival.subscriber_id")->queryRow();
							if($status_array['status'] != 'stop'){
								if(!$customer->getIsSmsOverQuota()){
									$status = $sms->sendmms($rec, $media_url, $messageorg);
									if($status){
										$sms_id = explode("_",$status);
										CommonHelper::setActivityLogs('Sendmms '.str_replace(array('{{','}}'),'',$sms->tableName()),$sms_id[1],$sms->tableName(),'Sendmms',$customer->customer_id);
										$count_rec++;
									}else{
										CommonHelper::setActivityLogs('Sendmms Fail Check Flowroute '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendmms',$customer->customer_id);
										$count_rec_not_sent++;
									}
									
								}else{
									CommonHelper::setActivityLogs('Sendmms Fail Merchant Validity is Over '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendmms',$customer->customer_id);
									$count_rec_not_sent = count($all_reciepients);
									Yii::app()->notify->addError(Yii::t('customers', 'Your Plan Validity is Over Please Contact You Support.'));
								}
							}else{
								CommonHelper::setActivityLogs('Sendmms Fail Merchant Subscriber Status is Stop '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendmms',$customer->customer_id);
								Yii::app()->notify->addError(Yii::t('customers', 'Your Mobile has been Stop! Please check your subscriber list and Mobile number.'));
							}
							
						}
					}else{
						CommonHelper::setActivityLogs('Sendmms Fail Merchant SMS Quota is Over '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendmms',$customer->customer_id);
						Yii::app()->notify->addError(Yii::t('customers', 'Your SMS Quota is Over! Plese Contact Your Support.'));
					}
					
					if((int)$count_rec > 0){
						Yii::app()->notify->addSuccess(Yii::t('customers', ' Mms sent successfully to '.$count_rec.' reciepient!'));
					}
					if((int)$count_rec_not_sent > 0){
						Yii::app()->notify->addError(Yii::t('customers', ' Mms not sent  '.$count_rec_not_sent.' reciepient!'));
					}
				}else{
					CommonHelper::setActivityLogs('Sendmms Fail Image File Size too Large '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendmms',$customer->customer_id);
					Yii::app()->notify->addError(Yii::t('customers', $file_type.' Size is too Large. (File Size Should be less then 730kB)'));
				}
			}else{
				CommonHelper::setActivityLogs('Sendmms Fail Stop Count More than 100 '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Sendmms',$customer->customer_id);
				Yii::app()->notify->addError(Yii::t('customers', 'Your Stop Count More than 100 So Your Beelift Number is Suspended! Please Contact Your Support Team.'));
			}
			
			/*$data = [
			  
			  'caption' => $attributes['text'],
			];*/
		}
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Send MMS'),
            'pageHeading'       => Yii::t('customers', 'Send MMS'),
            'pageBreadcrumbs'   => array(
                //Yii::t('customers', 'Account') => $this->createUrl('account/sendsms'),
                Yii::t('app', 'Send MMS')
            )
        ));
		
		$this->render('sendmms', compact('sms','imagegallery','total_remain_quota'));
	}
	
	public function actionReplysms()
	{
		$request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
		$sms    = new Sms();
		
		$sms_rply_stop_count = Yii::app()->db->createCommand("SELECT COUNT(sms_rply_id) FROM uic_sms_rply WHERE customer_id='".$customer->customer_id."' AND sms_rply_to_number='".$customer_company->flowroute_sms_num."' AND sms_rply_body LIKE 'STOP'")->queryScalar();
		
		if (Yii::app()->request->isPostRequest) 
		{
			$mobile =  $request->getPost('mobile');
			$messageorg =  $request->getPost('message');
			
			$count_rec= 0;
			$response = array();
			if($sms_rply_stop_count < 100){
				if($customer->getGroupOption('smssending.sms_quota', -1) != -1){
					$rem_quota = ((int)$customer->getGroupOption('smssending.sms_quota', -1) - $customer->countUsageSmsFromQuotaMark());
					
					if($rem_quota >= 1){
						if(!$customer->getIsSmsOverQuota()){
							$status = $sms->sendsms($mobile, $messageorg);
							if($status){
								$sms_id = explode("_",$status);
								CommonHelper::setActivityLogs('Replysms '.str_replace(array('{{','}}'),'',$sms->tableName()),$sms_id[1],$sms->tableName(),'Replysms',$customer->customer_id);
								$response['SUCCESS'] = 'SMS Replied Successfully.';
							}
						}else{
							CommonHelper::setActivityLogs('Replysms Merchant Validity is Over '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Replysms',$customer->customer_id);
							$response['ERROR']= 'Your Validity is Over Please Contact Admin.';
						}
					}else{
						CommonHelper::setActivityLogs('Replysms Merchant Quota Is Over '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Replysms',$customer->customer_id);
						$response['ERROR']= 'Quota is Over Please Contact Admin.';
					}
				}else if($customer->getGroupOption('smssending.sms_quota', -1) == -1){
					if(!$customer->getIsSmsOverQuota()){
						$status = $sms->sendsms($mobile, $messageorg);
						if($status){
							$sms_id = explode("_",$status);
							CommonHelper::setActivityLogs('Replysms '.str_replace(array('{{','}}'),'',$sms->tableName()),$sms_id[1],$sms->tableName(),'Replysms',$customer->customer_id);
							$response['SUCCESS'] = 'SMS Replied Successfully.';
						}
					}else{
						CommonHelper::setActivityLogs('Replysms Merchant Validity is Over '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Replysms',$customer->customer_id);
						$response['ERROR']= 'Your Validity is Over Please Contact Admin.';
					}
				}else{
					CommonHelper::setActivityLogs('Replysms Merchant has been not assign any plan '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Replysms',$customer->customer_id);
					$response['ERROR']= 'Please Contact Your Admin, You have not any Merchant Payment Plan assign it.';
				}
			}else{
				CommonHelper::setActivityLogs('Replysms Merchant Stop Counter more than 100 '.str_replace(array('{{','}}'),'',$sms->tableName()),0,$sms->tableName(),'Replysms',$customer->customer_id);
				$response['ERROR']= 'Your Stop Count More than 100 So Your Beelift Number is Suspended! Please Contact Your Support Team.';
			}
			
			echo json_encode($response);die();
		}
		
	}
	
	
	public function actionSendsmslist()
    {
		$request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
		
		$sms    = new Sms();
		$cid = (int)Yii::app()->customer->getId();
		$company_info = CustomerCompany::model()->findByAttributes(array('customer_id' => (int)$cid));
		
		$list = new Lists();
        $criteria = new CDbCriteria();
		$criteria->select = 't.list_id, t.customer_id, t.name, t.display_name, t.status , t.date_added';
        $criteria->compare('t.customer_id', $cid);
		//$criteria->compare('customer_id', 'active');
		$criteria->compare('t.status', 'active');
		$criteria->order = 't.list_id DESC';
		$lists = $list->findAll($criteria);	

		$mylists =array();
		foreach ($lists as $index => $list) {
			$mylists[$list->list_id] = $list->name;
		}

		$customer = Yii::app()->customer->getModel();
		
		$step = 300;
		$count_rec= 0;
		$count_rec_not_sent = 0;
        if (Yii::app()->request->isPostRequest && $attributes = Yii::app()->request->getPost($sms->modelName)) {
			$messageorg = $attributes['message'] ;
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
			
			if($count_rec > 0){
				Yii::app()->notify->addSuccess(Yii::t('customers', ' Sms sent successfully to '.$count_rec.' reciepient!'));
			}
			if($count_rec_not_sent > 0){
				Yii::app()->notify->addError(Yii::t('customers', ' Sms not sent  '.$count_rec_not_sent.' reciepient!'));
			}

        }
       
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Send sms to List'),
            'pageHeading'       => Yii::t('customers', 'Send sms to List'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/sendsms'),
                Yii::t('app', 'Send sms to List')
            )
        ));
        
        $this->render('sendsmslist', compact('sms','customer', 'mylists'));
    }
	
	public function actionManagement(){
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms', 'Management'),
            'pageHeading'       => Yii::t('sms', 'Management'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'SmsDashboard') => $this->createUrl('dashboard/smsdashboard'),
                Yii::t('app', 'Management')
            )
        ));

        $this->render('management');
	}
	
	
	public function actionFinancial(){
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms', 'Financial'),
            'pageHeading'       => Yii::t('sms', 'Financial'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'SmsDashboard') => $this->createUrl('dashboard/smsdashboard'),
                Yii::t('app', 'Financial')
            )
        ));

        $this->render('financial');
	}
	
	public function actionReports()
	{
		$request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
		
		$oDbConnection = Yii::app()->db; 
		$sens_sms_count = $oDbConnection->createCommand('SELECT count(sms_id) FROM uic_sms WHERE customer_id ='.$customer->customer_id.' AND status="Sent"')->queryScalar();
		
		$notsend_sms_count = $oDbConnection->createCommand('SELECT count(sms_id) FROM uic_sms WHERE customer_id ='.$customer->customer_id.' AND status!="Sent"')->queryScalar();
		
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
		
		$query_command = Yii::app()->db->createCommand("SELECT date_added as date, count(sms_id) as sms_count, status FROM uic_sms WHERE ".$str_whr. " AND status='sent' Group By DATE(date_added) UNION SELECT date_added as date, count(sms_id) as sms_count, status FROM uic_sms WHERE ".$str_whr." AND status <> 'sent' Group By DATE(date_added)")->queryAll();
		
		$date_array = array();
		foreach($query_command as $sms_data){
			$date_array['date'][] = date('Y-m-d',strtotime($sms_data['date']));
			$date_array['send_count'][]= ($sms_data['status'] == 'sent' ? (int)$sms_data['sms_count'] : 0);
			$date_array['notsend_count'][]= ($sms_data['status'] != 'sent' ? (int)$sms_data['sms_count'] : 0);
		}

		$send_sms = Yii::app()->db->createCommand("SELECT * FROM uic_sms WHERE".$str_whr)->queryAll();

		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms', 'Reports'),
            'pageHeading'       => Yii::t('sms', 'Sms Reports'),
            'pageBreadcrumbs'   => array(
                //Yii::t('dashboard', 'SocialmediaDashboard') => $this->createUrl('dashboard/socialdashboard'),
                Yii::t('app', 'Reports')
            )
        ));

        $this->render('reports',compact('date_array','sens_sms_count', 'notsend_sms_count', 'rem_quota','send_sms'));
	}
	
	public function actionSmsbarchart()
	{
		$request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
		
		$str_whr = '';
		if (Yii::app()->request->isPostRequest) 
		{
			$date_start = date('Y-m-d',strtotime($request->getPost('frm_date')));
			$date_end = date('Y-m-d',strtotime($request->getPost('to_date')));
			
			if($date_start !='' && $date_end != ''){
				$str_whr .= " date_added BETWEEN '".$date_start."' AND '".$date_end."' AND ";
			}
		}
		$str_whr .= " status='Sent' AND customer_id = '".$customer->customer_id."'";
		$query_command = Yii::app()->db->createCommand("SELECT date_added as date, count(sms_id) as sms_count FROM uic_sms WHERE ".$str_whr." Group By DATE(date_added)")->queryAll();
		echo CJSON::encode($query_command);
		
	}
	
	public function actionSubscriberstop(){
		$request    = Yii::app()->request;
		$notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
		$stop_number = $request->getQuery('stop_number');
		$subscriber_model = new ListSubscriber();

		$stop_number = Yii::app()->db->createCommand("SELECT lival.subscriber_id 'uic_list_field_value', lisu.list_id 'uic_list_subscriber' FROM uic_list_field_value lival, uic_list li, uic_list_subscriber lisu WHERE lival.subscriber_id = lisu.subscriber_id  AND li.list_id = lisu.list_id AND lisu.status = 'confirmed' AND li.customer_id = '".$customer->customer_id."' AND lival.value = '".$stop_number."' GROUP BY lival.subscriber_id")->queryAll();
		
		$subscriber_model->getStop($stop_number);
		$notify->addSuccess(Yii::t('app', 'This subscriber is successfully stop!'));
		/*if(empty($stop_number) && !count($stop_number)){
			//SmsRply::model()->updateAll(array('sms_rply_status'=>0),' sms_rply_from_number!="'.$stop_number.'"');
			$notify->addError(Yii::t('app', 'This subscriber is not in your list! Please check your list.'));
		}else{
			
			$notify->addSuccess(Yii::t('app', 'This subscriber is successfully stop!'));
		}*/
		
		
		$this->redirect(array('sms/smsrplylist'));
	}
	
	public function actionSubscriberactive(){
		$request    = Yii::app()->request;
		$notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
		$stop_number = $request->getQuery('active_number');
		$subscriber_model = new ListSubscriber();
		
		$stop_number = Yii::app()->db->createCommand("SELECT lival.subscriber_id 'uic_list_field_value', lisu.list_id 'uic_list_subscriber' FROM uic_list_field_value lival, uic_list li, uic_list_subscriber lisu WHERE lival.subscriber_id = lisu.subscriber_id  AND li.list_id = lisu.list_id AND lisu.status = 'stop' AND li.customer_id = '".$customer->customer_id."' AND lival.value = '".$stop_number."'")->queryAll();
		
		$subscriber_model->getActive($stop_number);
		
		$notify->addSuccess(Yii::t('app', 'This subscriber is successfully active!'));
		
		$this->redirect(array('sms/smsrplylist'));
	}	
	
	public function actionEztext(){
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms', 'Ez Text'),
            'pageHeading'       => Yii::t('sms', 'Ez Text'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'SmsDashboard') => $this->createUrl('dashboard/smsdashboard'),
                Yii::t('app', 'Ez Text')
            )
        ));

        $this->render('ez_text');
	}
	
	public function actionTexttopay(){
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms', 'Text to pay'),
            'pageHeading'       => Yii::t('sms', 'Text to pay'),
            'pageBreadcrumbs'   => array(
                //Yii::t('dashboard', 'SmsDashboard') => $this->createUrl('dashboard/smsdashboard'),
                Yii::t('app', 'Text to pay')
            )
        ));

        $this->render('text_to_pay');
	}
	
	public function actionStopcounter()
	{
		$request = Yii::app()->request;
		$notify  = Yii::app()->notify;
		$customer = Yii::app()->customer->getModel();
		
		$smsrply = new SmsRply('search');
		$smsrply->unsetAttributes();
		
		$smsrply->attributes = (array)$request->getQuery($smsrply->modelName, array());
		$smsrply->sms_rply_to_number  = $customer->company->flowroute_sms_num;
		$smsrply->customer_id  = $customer->customer_id;
		$smsrply->sms_rply_body = 'Stop';
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms', 'Stop Report'),
            'pageHeading'       => Yii::t('sms', 'Stop Report'),
            'pageBreadcrumbs'   => array(
                //Yii::t('dashboard', 'SmsDashboard') => $this->createUrl('dashboard/smsdashboard'),
                Yii::t('app', 'Stop Report')
            )
        ));

        $this->render('stop_report',compact('smsrply'));
	}
	
	protected function sizeFilter( $bytes )
	{
		$label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
		for( $i = 0; $bytes >= 1024 && $i < ( count( $label ) -1 ); $bytes /= 1024, $i++ );
		return round($bytes, 2);
	}
	
	protected function getPendingListCount($customer_id){
		$pending_campaign_array = Yii::app()->db->createCommand("SELECT list_id FROM uic_sms_campaign WHERE campaign_status='PENDING' AND customer_id='".$customer_id."'")->queryAll();
		$count_subscriber = array();
		foreach($pending_campaign_array as $pending_campaign){
			$count_list_sub = Yii::app()->db->createCommand("SELECT ulfv.value FROM uic_list as li INNER JOIN uic_list_subscriber as lis on li.list_id = lis.list_id inner join uic_list_field as uif on li.list_id = uif.list_id inner join uic_list_field_value as ulfv on lis.subscriber_id = ulfv.subscriber_id and uif.field_id = ulfv.field_id where li.list_id = '".$pending_campaign['list_id']."' and uif.label = 'Mobile' and lis.status='confirmed' AND ulfv.value != '' GROUP BY ulfv.value")->queryAll();
		 $count_subscriber[] = count($count_list_sub);
		}
		return array_sum($count_subscriber);
	}
}
