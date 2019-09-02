<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CurrenciesController
 *
 * Handles the actions for currencies related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.5
 */

class Activity_logsController extends Controller
{
    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
        $filters = array(
            'postOnly + delete',
        );

        return CMap::mergeArray($filters, parent::filters());
    }

    /**
     * List all available currencies
     */
    public function actionIndex()
    {
        $request   = Yii::app()->request;
        $activity_logs  = new ActivityLogs('search');
        $activity_logs->unsetAttributes();

        $activity_logs->attributes = (array)$request->getQuery($activity_logs->modelName, array());

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('activity_logs', 'View ActivityLogs'),
            'pageHeading'       => Yii::t('currencies', 'View ActivityLogs'),
            'pageBreadcrumbs'   => array(
                //Yii::t('currencies', 'Currencies') => $this->createUrl('currencies/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('activity_logs'));
    }

    /**
     * Create a new currency
     */
    public function actionCreate()
    {
        $currency = new Currency();
        $request  = Yii::app()->request;
        $notify   = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($currency->modelName, array()))) {
            $currency->attributes = $attributes;
            if (!$currency->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'currency'  => $currency,
            )));

            if ($collection->success) {
                $this->redirect(array('currencies/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('currencies', 'Create new currency'),
            'pageHeading'       => Yii::t('currencies', 'Create new currency'),
            'pageBreadcrumbs'   => array(
                Yii::t('currencies', 'Currencies') => $this->createUrl('currencies/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('currency'));
    }

    /**
     * Update existing currency
     */
    public function actionView_more()
    {
		$request  = Yii::app()->request;
		$log_id = $request->getPost('log_id');
        $activity_logs = ActivityLogs::model()->findByPk((int)$log_id);

		$table_name = $activity_logs->getTablenameInInt('',$activity_logs->log_table_name_id);
		if(strpos($table_name, " ") !== false)
		{
			$table_name = str_replace(" ", "", $table_name);
		}
		
		$get_model = $table_name::model()->findByPk($activity_logs->log_table_id);
		//$customer_model = Customer::model()->findByPk($activity_logs->user_login_id);
		
		$activity_logs_array['model_data'] = $get_model->attributes;
		if(!empty($activity_logs_array['model_data']) && is_array($activity_logs_array['model_data'])){
			
			if($activity_logs_array['model_data']['customer_id'] != ''){
				$customer_model = Customer::model()->findByPk($activity_logs_array['model_data']['customer_id']);
				$get_model->customer_id  =  $customer_model->fullName;
			}
			
			$activity_logs_array['model_data'] = $get_model->attributes;
			echo json_encode($activity_logs_array);die();
		}else{
			$activity_logs_array['ERROR'] = 'No Record Found.';
			echo json_encode($activity_logs_array);die();
		}
    }

    /**
     * Delete existing currency
     */
    public function actionDelete($id)
    {
        $currency = Currency::model()->findByPk((int)$id);

        if (empty($currency)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        if ($currency->isRemovable) {
            $currency->delete();
        }

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('currencies/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $currency,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }

}
