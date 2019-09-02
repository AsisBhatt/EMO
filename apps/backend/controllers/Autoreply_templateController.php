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
		$autoreply_template = new AutoreplyTemplate('search');

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('autoreply_template', 'Autoreply Template'),
            'pageHeading'       => Yii::t('autoreply_template', 'Autoreply Template'),
            'pageBreadcrumbs'   => array(
                Yii::t('autoreply_template', 'Autoreply Template'),
            ),
        ));
		
        $this->render('index', compact('autoreply_template'));
    }
	
	public function actionCreate()
	{
		$autoreply_template   = new AutoreplyTemplate();
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
		//$customer_id = (int)Yii::app()->customer->getId();
		
		if ($request->isPostRequest && ($attributes = (array)$request->getPost($autoreply_template->modelName, array()))) {
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
                $this->redirect(array('autoreply_template/index'));
            }
        }
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('autoreply_template', 'Autoreply Template Create'),
			'pageHeading'       => Yii::t('autoreply_template', 'Autoreply Template Create'),
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
                $this->redirect(array('autoreply_template/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('autoreply_template', 'Update Autoreply Template'),
            'pageHeading'       => Yii::t('autoreply_template', 'Update Autoreply Template'),
            'pageBreadcrumbs'   => array(
                //Yii::t('customers', 'Customers') => $this->createUrl('customers/index'),
                Yii::t('app', 'Update Autoreply Template'),
            )
        ));

        $this->render('form', compact('autoreply_template'));
	}
	
	public function actionDelete($id){
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
            $redirect = $request->getPost('returnUrl', array('autoreply_template/index'));
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
			
			AutoreplyTemplate::model()->updateAll(array('auto_temp_status'=>'DEACTIVE'),'auto_temp_id !="'.$autoreply_id.'"');
			$response['SUCCESS'] = 'Your Template Status has been successfully change!';
		}else{
			$response['ERROR'] = 'Your Template Status is already Active!';
		}
		echo json_encode($response);die();
	}
	
	public function actionAutoreply()
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
