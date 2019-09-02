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

class Sms_templateController extends Controller
{
    public function init()
    {
        $this->getData('pageStyles')->add(array('src' => AssetsUrl::js('datetimepicker/css/bootstrap-datetimepicker.min.css')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('datetimepicker/js/bootstrap-datetimepicker.min.js')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('campaigns.js')));
		$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('dashboard.js')));
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
		$customer_id = (int)Yii::app()->customer->getId();
		
        $smstemplate = new SmsTemplate('search');
        $smstemplate->unsetAttributes();
		
        $smstemplate->attributes  = (array)$request->getQuery($smstemplate->modelName, array());
		$smstemplate->customer_id = $customer_id;
		

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms_template', 'Sms Template'),
            'pageHeading'       => Yii::t('sms_template', 'Sms Template'),
            'pageBreadcrumbs'   => array(
                //Yii::t('campaigns', 'Sms') => $this->createUrl('sms/index'),
                Yii::t('app', 'SMS Template')
            )
        ));		

        $this->render('index', compact('smstemplate'));
    }
	
	public function actionCreate(){
		$smstemplate   = new SmsTemplate();
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
		$customer_id = (int)Yii::app()->customer->getId();

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($smstemplate->modelName, array()))) {
			$attributes['customer_id'] = $customer_id;
            $smstemplate->attributes = $attributes;
			
            if (!$smstemplate->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }
			
			CommonHelper::setActivityLogs('SMS Template Create '.str_replace(array('{{','}}'),'',$smstemplate->tableName()),$smstemplate->template_id,$smstemplate->tableName(),'SMS Template Create',$customer_id);

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'smstemplate'  => $smstemplate,
            )));

            if ($collection->success) {
                $this->redirect(array('sms_template/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('smstemplates', 'Create new user'),
            'pageHeading'       => Yii::t('smstemplates', 'Create new Sms Template'),
            'pageBreadcrumbs'   => array(
                Yii::t('smstemplate', 'smstemplate') => $this->createUrl('sms_template/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('smstemplate'));
	}
	
	public function actionUpdate($id){
		$smstemplate = SmsTemplate::model()->findByPk((int)$id);
		$customer_id = (int)Yii::app()->customer->getId();

        if (empty($smstemplate)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($smstemplate->modelName, array()))) {
			$attributes['customer_id'] = $customer_id;
            $smstemplate->attributes = $attributes;

            if (!$smstemplate->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }
			
			CommonHelper::setActivityLogs('SMS Template Update '.str_replace(array('{{','}}'),'',$smstemplate->tableName()),$id,$smstemplate->tableName(),'SMS Template Update',$customer_id);
			
            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'smstemplate'  => $smstemplate,
            )));

            if ($collection->success) {
                $this->redirect(array('sms_template/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms_template', 'Update Sms Template'),
            'pageHeading'       => Yii::t('sms_template', 'Update Sms Template'),
            'pageBreadcrumbs'   => array(
                //Yii::t('customers', 'Customers') => $this->createUrl('customers/index'),
                Yii::t('app', 'Update Sms Template'),
            )
        ));

        $this->render('form', compact('smstemplate'));
	}
	
	public function actionDelete($id){
		$smstemplate = SmsTemplate::model()->findByPk((int)$id);

        if (empty($smstemplate)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $smstemplate->delete();
		
		CommonHelper::setActivityLogs('SMS Template Delete '.str_replace(array('{{','}}'),'',$smstemplate->tableName()),$id,$smstemplate->tableName(),'SMS Template Delete',(int)Yii::app()->customer->getId());

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
            'model'      => $smstemplate,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
	}
}
