<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * PlanController
 *
 * Handles the actions for price plans related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */

class PlanController extends Controller
{
    public function init()
    {
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
     * List all available  plans
     */
    public function actionIndex()
    {
        $request    = Yii::app()->request;
        $plan  = new Plan('search');
        $plan->unsetAttributes();

        $plan->attributes = (array)$request->getQuery($plan->modelName, array());

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('plan', 'View plans'),
            'pageHeading'       => Yii::t('plan', 'View plans'),
            'pageBreadcrumbs'   => array(
                Yii::t('plan', 'Plan') => $this->createUrl('plan/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('plan'));
    }

    /**
     * Create a new  plan
     */
    public function actionCreate()
    {
        $plan  = new Plan();
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($plan->modelName, array()))) {
            $plan->attributes = $attributes;
            if (isset(Yii::app()->params['POST'][$plan->modelName]['description'])) {
                $plan->description = Yii::app()->ioFilter->purify(Yii::app()->params['POST'][$plan->modelName]['description']);
            }
            if (!$plan->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'Plan' => $plan,
            )));

            if ($collection->success) {
                $this->redirect(array('plan/index'));
            }
        }

        $plan->fieldDecorator->onHtmlOptionsSetup = array($this, '_addEditorOptions');

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('plan', 'Create new plan'),
            'pageHeading'       => Yii::t('plan', 'Create new plan'),
            'pageBreadcrumbs'   => array(
                Yii::t('plan', 'Plan') => $this->createUrl('plan/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('plan'));
    }

    /**
     * Update existing plan
     */
    public function actionUpdate($id)
    {
        $plan = Plan::model()->findByPk((int)$id);

        if (empty($plan)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($plan->modelName, array()))) {
            $plan->attributes = $attributes;
            if (isset(Yii::app()->params['POST'][$plan->modelName]['description'])) {
                $plan->description = Yii::app()->ioFilter->purify(Yii::app()->params['POST'][$plan->modelName]['description']);
            }
            if (!$plan->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'plan' => $plan,
            )));

            if ($collection->success) {
                $this->redirect(array('plan/index'));
            }
        }

        $plan->fieldDecorator->onHtmlOptionsSetup = array($this, '_addEditorOptions');

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('plan', 'Update price plan'),
            'pageHeading'       => Yii::t('plan', 'Update price plan'),
            'pageBreadcrumbs'   => array(
                Yii::t('plan', 'Plan') => $this->createUrl('plan/index'),
                Yii::t('app', 'Update'),
            )
        ));

        $this->render('form', compact('plan'));
    }

    /**
     * Delete existing price plan
     */
    public function actionDelete($id)
    {
        $plan = Plan::model()->findByPk((int)$id);

        if (empty($plan)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $plan->delete();

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('plan/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $plan,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }

    /**
     * Autocomplete for price plans
     */
    public function actionAutocomplete($term)
    {
        $request = Yii::app()->request;
        if (!$request->isAjaxRequest) {
            $this->redirect(array('plan/index'));
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'plan_id, name';
        $criteria->compare('name', $term, true);
        $criteria->limit = 10;

        $models = Plan::model()->findAll($criteria);
        $results = array();

        foreach ($models as $model) {
            $results[] = array(
                'plan_id' => $model->plan_id,
                'value'   => $model->name,
            );
        }

        return $this->renderJson($results);
    }

    /**
     * Callback method to setup the editor
     */
    public function _addEditorOptions(CEvent $event)
    {
        if (!in_array($event->params['attribute'], array('description'))) {
            return;
        }

        $options = array();
        if ($event->params['htmlOptions']->contains('wysiwyg_editor_options')) {
            $options = (array)$event->params['htmlOptions']->itemAt('wysiwyg_editor_options');
        }
        $options['id'] = CHtml::activeId($event->sender->owner, $event->params['attribute']);
        $event->params['htmlOptions']->add('wysiwyg_editor_options', $options);
    }
}
