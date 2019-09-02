<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Email_blacklist_monitorsController
 *
 * Handles the actions for blacklist monitors related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.9
 */

class Email_blacklist_monitorsController extends Controller
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
     * List all blacklist monitors.
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
        $monitor = new EmailBlacklistMonitor('search');
        $monitor->unsetAttributes();

        // for filters.
        $monitor->attributes = (array)$request->getQuery($monitor->modelName, array());

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('email_blacklist', 'Blacklist monitors'),
            'pageHeading'     => Yii::t('email_blacklist', 'Blacklist monitors'),
            'pageBreadcrumbs' => array(
                Yii::t('email_blacklist', 'Blacklist monitors') => $this->createUrl('email_blacklist_monitors/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('monitor'));
    }

    /**
     * Add a new blacklist monitor
     */
    public function actionCreate()
    {
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;
        $monitor = new EmailBlacklistMonitor();

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($monitor->modelName, array()))) {
            $monitor->attributes = $attributes;
            if (!$monitor->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'monitor'   => $monitor,
            )));

            if ($collection->success) {
                $this->redirect(array('email_blacklist_monitors/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('email_blacklist', 'Blacklist monitors'),
            'pageHeading'     => Yii::t('email_blacklist', 'Create a new blacklist monitor.'),
            'pageBreadcrumbs' => array(
                Yii::t('email_blacklist', 'Blacklist monitors') => $this->createUrl('email_blacklist_monitors/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('monitor'));
    }

    /**
     * Update an existing blacklist monitor
     */
    public function actionUpdate($id)
    {
        $monitor = EmailBlacklistMonitor::model()->findByPk((int)$id);

        if (empty($monitor)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($monitor->modelName, array()))) {
            $monitor->attributes = $attributes;
            if (!$monitor->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'monitor'   => $monitor,
            )));

            if ($collection->success) {
                $this->redirect(array('email_blacklist_monitors/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('email_blacklist', 'Blacklist monitors'),
            'pageHeading'       => Yii::t('email_blacklist', 'Update blacklist monitor.'),
            'pageBreadcrumbs'   => array(
                Yii::t('email_blacklist', 'Blacklist monitors') => $this->createUrl('email_blacklist_monitors/index'),
                Yii::t('app', 'Update'),
            )
        ));

        $this->render('form', compact('monitor'));
    }

    /**
     * Delete a blacklist monitor.
     */
    public function actionDelete($id)
    {
        $monitor = EmailBlacklistMonitor::model()->findByPk((int)$id);

        if (empty($monitor)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $monitor->delete();

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('email_blacklist_monitors/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $monitor,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }
}
