<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Customer_login_logsController
 *
 * Handles the actions for customer messages related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.2
 */

class Customer_login_logsController extends Controller
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
     * List customer login logs
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
        $model   = new CustomerLoginLog('search');

        $model->unsetAttributes();
        $model->attributes = (array)$request->getQuery($model->modelName, array());

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'View login logs'),
            'pageHeading'     => Yii::t('customers', 'View login logs'),
            'pageBreadcrumbs' => array(
                Yii::t('customers', 'Customers')  => $this->createUrl('customers/index'),
                Yii::t('customers', 'Login logs') => $this->createUrl('customer_login_logs/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('model'));
    }

    /**
     * Delete existing customer login log
     */
    public function actionDelete($id)
    {
        $model = CustomerLoginLog::model()->findByPk((int)$id);

        if (empty($model)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request = Yii::app()->request;
   
        $model->delete();

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $redirect = $request->getPost('returnUrl', array('customer_login_logs/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $model,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }
}
