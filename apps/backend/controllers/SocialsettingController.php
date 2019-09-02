<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * SocialsettingController
 *
 * Handles the actions for social api related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.9
 */

class SocialsettingController extends Controller
{
    public function init()
    {
        $this->onBeforeAction = array($this, '_registerJuiBs');
        parent::init();
    }

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
     * List Api details
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
        $message = new Socialsetting('search');

		//print_r($message);
        $message->unsetAttributes();
        $message->attributes = (array)$request->getQuery($message->modelName, array());

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Social API Setting'),
            'pageHeading'     => Yii::t('messages', 'Social API Setting'),
            'pageBreadcrumbs' => array(
                Yii::t('settings', 'Settings') => $this->createUrl('settings/index'),
				Yii::t('smssetting', 'Social setting')   => $this->createUrl('socialsetting/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('message'));
    }

    /**
     * Create a new message
     */
    public function actionCreate()
    {
        $message = new Smssetting();
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($message->modelName, array()))) {
            $message->attributes = $attributes;
            //$message->message    = Yii::app()->ioFilter->purify(Yii::app()->params['POST'][$message->modelName]['message']);

            if (!$message->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'model'     => $message,
            )));

            if ($collection->success) {
                $this->redirect(array('smssetting/index'));
            }
        }

       // $message->fieldDecorator->onHtmlOptionsSetup = array($this, '_setEditorOptions');

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Create new Sms API Setting'),
            'pageHeading'     => Yii::t('messages', 'Create new Sms API Setting'),
            'pageBreadcrumbs' => array(
				Yii::t('settings', 'Settings') => $this->createUrl('settings/index'), 
				Yii::t('smssetting', 'Sms setting')   => $this->createUrl('smssetting/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('message'));
    }

    /**
     * Update existing message
     */
    public function actionUpdate($id)
    {
		
        $message = Socialsetting::model()->findByPk((int)$id);

        if (empty($message)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
      
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($message->modelName, array()))) {
            $message->attributes = $attributes;
            //$message->message    = Yii::app()->ioFilter->purify(Yii::app()->params['POST'][$message->modelName]['message']);

            if (!$message->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'model'     => $message,
            )));

            if ($collection->success) {
                $this->redirect(array('socialsetting/index'));
            }
        }

        //$message->fieldDecorator->onHtmlOptionsSetup = array($this, '_setEditorOptions');

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Update Sms API Setting'),
            'pageHeading'     => Yii::t('messages', 'Update Sms API Setting'),
            'pageBreadcrumbs' => array(
                Yii::t('settings', 'Settings') => $this->createUrl('settings/index'),
                Yii::t('smssetting', 'Sms setting')   => $this->createUrl('smssetting/index'),
                Yii::t('app', 'Update'),
            )
        ));

        $this->render('form', compact('message'));
    }

    /**
     * View message
     */
    public function actionView($id)
    {
        $message = Socialsetting::model()->findByPk((int)$id);

        if (empty($message)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'View Social API Setting'),
            'pageHeading'     => Yii::t('messages', 'View Social API Setting'),
            'pageBreadcrumbs' => array(
                Yii::t('settings', 'Settings') => $this->createUrl('settings/index'),
                Yii::t('socialsetting', 'Social API setting')   => $this->createUrl('socialsetting/index'),
                Yii::t('app', 'View'),
            )
        ));

        $this->render('view', compact('message'));
    }

    /**
     * Delete existing customer message
     */
    public function actionDelete($id)
    {
        $message = Smssetting::model()->findByPk((int)$id);

        if (empty($message)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $message->delete();

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $redirect = $request->getPost('returnUrl', array('smssetting/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $message,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }


    /**
     * Callback to register Jquery ui bootstrap only for certain actions
     */
    public function _registerJuiBs($event)
    {
        if (in_array($event->params['action']->id, array('create', 'update'))) {
            $this->getData('pageStyles')->mergeWith(array(
                array('src' => Yii::app()->apps->getBaseUrl('assets/css/jui-bs/jquery-ui-1.10.3.custom.css'), 'priority' => -1001),
            ));
        }
    }
}
