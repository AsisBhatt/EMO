<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Campaign_tagsController
 *
 * Handles the actions for customer campaign tags related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.9
 */

class Campaign_tagsController extends Controller
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
     * List available campaign tags
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
        $model   = new CustomerCampaignTag('search');

        $model->unsetAttributes();
        $model->attributes  = (array)$request->getQuery($model->modelName, array());
        $model->customer_id = (int)Yii::app()->customer->getId();

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('campaigns', 'View campaign tags'),
            'pageHeading'       => Yii::t('campaigns', 'View campaign tags'),
            'pageBreadcrumbs'   => array(
                Yii::t('campaigns', 'Campaigns') => $this->createUrl('campaigns/index'),
                Yii::t('campaigns', 'Tags') => $this->createUrl('campaign_tags/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('model'));
    }

    /**
     * Create a new campaign tag
     */
    public function actionCreate()
    {
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;
        $model   = new CustomerCampaignTag();

        $model->customer_id = (int)Yii::app()->customer->getId();

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($model->modelName, array()))) {
            $model->attributes  = $attributes;
            $model->customer_id = Yii::app()->customer->getId();
            if (!$model->save()) {
				$notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
				
				CommonHelper::setActivityLogs('Campaign Tags Create '.str_replace(array('{{','}}'),'',$model->tableName()),$model->tag_id,$model->tableName(),'Campaign Tags Create',(int)Yii::app()->customer->getId());
				
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'model'     => $model,
            )));

            if ($collection->success) {
                $this->redirect(array('campaign_tags/update', 'tag_uid' => $model->tag_uid));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('campaigns', 'Create new tag'),
            'pageHeading'       => Yii::t('campaigns', 'Create new campaign tag'),
            'pageBreadcrumbs'   => array(
                Yii::t('campaigns', 'Campaigns') => $this->createUrl('campaigns/index'),
                Yii::t('campaigns', 'Tags') => $this->createUrl('campaign_tags/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('model'));
    }

    /**
     * Update existing campaign tag
     */
    public function actionUpdate($tag_uid)
    {
        $model = CustomerCampaignTag::model()->findByAttributes(array(
            'tag_uid'     => $tag_uid,
            'customer_id' => (int)Yii::app()->customer->getId(),
        ));

        if (empty($model)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($model->modelName, array()))) {
            $model->attributes = $attributes;
            $model->customer_id= Yii::app()->customer->getId();
            if (!$model->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
				
				CommonHelper::setActivityLogs('Campaign Tags Update '.str_replace(array('{{','}}'),'',$model->tableName()),$model->tag_id,$model->tableName(),'Campaign Tags Update',(int)Yii::app()->customer->getId());
                
				$notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'model'     => $model,
            )));
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('campaigns', 'Update tag'),
            'pageHeading'       => Yii::t('campaigns', 'Update campaign tag'),
            'pageBreadcrumbs'   => array(
                Yii::t('campaigns', 'Campaigns') => $this->createUrl('campaigns/index'),
                Yii::t('campaigns', 'Tags') => $this->createUrl('campaign_tags/index'),
                Yii::t('app', 'Update'),
            )
        ));

        $this->render('form', compact('model'));
    }

    /**
     * Delete existing campaign tag
     */
    public function actionDelete($tag_uid)
    {
        $model = CustomerCampaignTag::model()->findByAttributes(array(
            'tag_uid'     => $tag_uid,
            'customer_id' => (int)Yii::app()->customer->getId(),
        ));

        if (empty($model)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
		
		CommonHelper::setActivityLogs('Campaign Tags Delete '.str_replace(array('{{','}}'),'',$model->tableName()),$model->tag_id,$model->tableName(),'Campaign Tags Delete',(int)Yii::app()->customer->getId());

        $model->delete();

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('campaign_tags/index'));
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
