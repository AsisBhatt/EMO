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

class Autoreply_templateController extends Controller
{
	public $enableCsrfValidation = false;
	
    public function init()
    {
        $apps = Yii::app()->apps;
        $this->getData('pageScripts')->mergeWith(array(
            array('src' => AssetsUrl::js('dashboard.js'))
        ));
        parent::init();
		
    }

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
        return CMap::mergeArray(array(
            'postOnly + delete_log, delete_logs',
        ), parent::filters());
    }
    /**
     * Display dashboard informations
     */
    public function actionIndex()
    {
		$request    = Yii::app()->request;
		$template_type = strtoupper($request->getQuery('type'));
		
		$customer = Yii::app()->customer->getModel();
		$autoreply_template = new AutoreplyTemplate('search');
		$autoreply_template->customer_id = $customer->customer_id;
		$autoreply_template->auto_temp_type = $template_type;
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('autoreply_template_'.$template_type, 'Autoreply Template '.$template_type),
            'pageHeading'       => Yii::t('autoreply_template_'.$template_type, 'Autoreply Template '.$template_type),
            'pageBreadcrumbs'   => array(
                Yii::t('autoreply_template_'.$template_type, 'Autoreply Template '.$template_type),
            ),
        ));
		
        $this->render('index', compact('autoreply_template'));
    }
	
	public function actionCreate()
	{
		$customer = Yii::app()->customer->getModel();
		$autoreply_template   = new AutoreplyTemplate();
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
		$template_type = strtoupper($request->getQuery('type'));
		
		
		if ($request->isPostRequest && ($attributes = (array)$request->getPost($autoreply_template->modelName, array()))) {
			
			if($template_type == ''){
				$notify->addError(Yii::t('app', "Dont't Mess with URL!"));
				$this->redirect(array('dashboard/index/'));
			}
			
			$autoreply_template->customer_id = $customer->customer_id;
			$autoreply_template->auto_temp_type = $template_type;
			$autoreply_template->auto_temp_text = $attributes['auto_temp_text'];
			$autoreply_template->auto_temp_status = $attributes['auto_temp_status'];
			$autoreply_template->auto_temp_date_added = date('Y-m-d H:i:s');
            //$autoreply_template->attributes = $attributes['auto_temp_text'];
			
            if (!$autoreply_template->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'autoreply_template'  => $autoreply_template,
            )));

            if ($collection->success) {
                $this->redirect(array('autoreply_template/index/type/'));
            }
        }
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('autoreply_template_'.$template_type, 'Autoreply Template Create '.$template_type),
			'pageHeading'       => Yii::t('autoreply_template_'.$template_type, 'Autoreply Template Create'),
            /*'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Email Dashboard'),
            ),*/
        ));
		
		$this->render('form', compact('autoreply_template'));
	}
	
	public function actionUpdate($id){
		$autoreply_template = AutoreplyTemplate::model()->findByPk((int)$id);
		//$customer_id = (int)Yii::app()->customer->getId();

        if (empty($autoreply_template)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($autoreply_template->modelName, array()))) {
			
			if($autoreply_template->auto_temp_type == ''){
				$this->redirect(array('dashboard/index/'));
			}
			
			//$attributes['customer_id'] = $customer_id;
            $autoreply_template->attributes = $attributes;

            if (!$autoreply_template->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'autoreply_template'  => $autoreply_template,
            )));

            if ($collection->success) {
                $this->redirect(array('autoreply_template/index/type/'.strtolower($autoreply_template->auto_temp_type)));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('autoreply_template_'.$autoreply_template->auto_temp_type, 'Update Autoreply Template '.$autoreply_template->auto_temp_type),
            'pageHeading'       => Yii::t('autoreply_template_'.$autoreply_template->auto_temp_type, 'Update Autoreply Template '.$autoreply_template->auto_temp_type),
            'pageBreadcrumbs'   => array(
                //Yii::t('customers', 'Customers') => $this->createUrl('customers/index'),
                Yii::t('app', 'Update Autoreply Template '.$autoreply_template->auto_temp_type),
            )
        ));

        $this->render('form', compact('autoreply_template'));
	}
	
	public function actionDelete($id,$type){
		
		$autoreply_template = AutoreplyTemplate::model()->findByPk((int)$id);

        if (empty($autoreply_template)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $autoreply_template->delete();

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('autoreply_template/index/type/'.$type));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $autoreply_template,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
	}
	
	public function actionUpdatestatus()
	{
		$request = Yii::app()->request;
        $notify  = Yii::app()->notify;
		$autoreply_id = $request->getPost('auto_template_id');
		
		$autoreply_template = AutoreplyTemplate::model()->findByPk((int)$autoreply_id);
		
		$response = array();
		if($autoreply_template->auto_temp_status == 'DEACTIVE'){
			$autoreply_template->auto_temp_status = 'ACTIVE';
			$autoreply_template->save();
			
			AutoreplyTemplate::model()->updateAll(array('auto_temp_status'=>'DEACTIVE'),'auto_temp_id !="'.$autoreply_id.'" AND auto_temp_type = "'.$autoreply_template->auto_temp_type.'"');
			$response['SUCCESS'] = 'Your Template Status has been successfully change!';
		}else{
			$response['ERROR'] = 'Your Template Status is already Active!';
		}
		echo json_encode($response);die();
	}
	
	public function actionAutoreply()
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
					
					$list_info = Yii::app()->db->createCommand("SELECT * FROM uic_list WHERE customer_id ='".$customer_info['customer_id']."' AND  name LIKE 'Default SMS List'")->queryRow()
					
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
										$subscriber->list_id    = $list_info['list_id'];
										$subscriber->mobile      = $attributes_array['from'];
										$subscriber->source     = ListSubscriber::SOURCE_JOIN;
										$subscriber->ip_address = $request->getServer('HTTP_MW_REMOTE_ADDR', $request->getServer('REMOTE_ADDR'));
										$subscriber->status = ListSubscriber::STATUS_CONFIRMED;
										$subscriber->save();
										
										$fields = ListField::model()->findAllByAttributes(array(
											'list_id' => $list_info['list_id'],
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
	}
	
	public function actionSocialdashboard(){
		$options = Yii::app()->options;
        $notify  = Yii::app()->notify;
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'Social Media Dashboard'),
			'pageHeading'       => Yii::t('dashboard', 'Social Media Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Social Media Dashboard'),
            ),
        ));
		
		$this->render('social_dashboard', compact());
	}

}
