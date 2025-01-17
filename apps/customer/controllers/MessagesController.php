<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * MessagesController
 *
 * Handles the actions for messages related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.9
 */

class MessagesController extends Controller
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
     * Show available customer messages
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
        $message = new CustomerMessage('search');
        $message->unsetAttributes();
        $message->attributes = (array)$request->getQuery($message->modelName, array());
        $message->customer_id = (int)Yii::app()->customer->getId();

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | ' . Yii::t('messages', 'Messages'),
            'pageHeading'     => Yii::t('messages', 'Messages'),
            'pageBreadcrumbs' => array(
                Yii::t('messages', 'Messages') => $this->createUrl('messages/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('message'));
    }

    /**
     * View customer message
     */
    public function actionView($message_uid)
    {
        $message = CustomerMessage::model()->findByAttributes(array(
            'customer_id' => (int)Yii::app()->customer->getId(),
            'message_uid' => $message_uid
        ));

        if (empty($message)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        if ($message->isUnseen) {
            $message->saveStatus(CustomerMessage::STATUS_SEEN);
        }

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | ' . Yii::t('messages', 'Messages'),
            'pageHeading'     => Yii::t('messages', 'Messages'),
            'pageBreadcrumbs' => array(
                Yii::t('messages', 'Messages') => $this->createUrl('messages/index'),
                Yii::t('app', 'View')
            )
        ));

        $this->render('view', compact('message'));
    }

    /**
     * Delete existing message
     */
    public function actionDelete($message_uid)
    {
        $message = CustomerMessage::model()->findByAttributes(array(
            'customer_id' => (int)Yii::app()->customer->getId(),
            'message_uid' => $message_uid
        ));

        if (empty($message)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $message->delete();

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $redirect = $request->getPost('returnUrl', array('messages/index'));
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
    * Mark all messages as seen for a certain customer
    **/
    public function actionMark_all_as_seen()
    {
        CustomerMessage::markAllAsSeenForCustomer((int)Yii::app()->customer->getId());
        Yii::app()->notify->addSuccess(Yii::t('messages', 'All messages were marked as seen!'));
        Yii::app()->request->redirect('index');
    }

    /**
     * Show available customer messages for header
     */
    public function actionHeader()
    {
        $request  = Yii::app()->request;
        
        if (!$request->isAjaxRequest) {
            $this->redirect(array('dashboard/index'));
        }
        
        $criteria = new CDbCriteria();
        $criteria->compare('customer_id', (int)Yii::app()->customer->getId());
        $criteria->compare('status', CustomerMessage::STATUS_UNSEEN);
        $criteria->order = 'message_id DESC';
        $criteria->limit = 100;

        $messages = CustomerMessage::model()->findAll($criteria);
        $counter  = count($messages);

        return $this->renderJson(array(
            'counter' => $counter,
            'header'  => Yii::t('messages', 'You have {n} unread messages!', $counter),
            'html'    => $this->renderPartial('_header', compact('messages'), true),
        ));
    }

}
