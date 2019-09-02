<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * LinkController
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

class LinkController extends Controller
{
    public function init()
    {
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('articles.js')));
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
        $link = new Link('search');
        $link->unsetAttributes();

        $link->attributes  = (array)$request->getQuery($link->modelName, array());
        $link->customer_id = (int)Yii::app()->customer->getId();
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('links', 'Links'),
            'pageHeading'       => Yii::t('links', 'Links'),
            'pageBreadcrumbs'   => array(
                Yii::t('links', 'Links') => $this->createUrl('link/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('link'));
    }



    /**
     * Create a new link
     */
    public function actionCreate()
    {
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
				
		$link    = new Link();
       
        if ($request->isPostRequest && ($attributes = (array)$request->getPost($link->modelName, array()))) {
            $link->attributes = $attributes;
			$link->customer_id = $customer->customer_id;

            if (!$link->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }


            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'link'   => $link,
            )));

            if ($collection->success) {
                $this->redirect(array('link/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('link', 'Create new link'),
            'pageHeading'       => Yii::t('link', 'Create new link'),
            'pageBreadcrumbs'   => array(
                Yii::t('link', 'Links') => $this->createUrl('link/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('link'));
    }

    /**
     * Update existing link
     */
    public function actionUpdate($id)
    {
        $link = Link::model()->findByPk((int)$id);

        if (empty($link)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request = Yii::app()->request;
        $notify = Yii::app()->notify;
        if ($request->isPostRequest && ($attributes = (array)$request->getPost($link->modelName, array()))) {
            $link->attributes = $attributes;
     
            if (!$link->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }
			
            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'link'   => $link,
            )));

            if ($collection->success) {
                $this->redirect(array('link/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('link', 'Update link'),
            'pageHeading'       => Yii::t('link', 'Update link'),
            'pageBreadcrumbs'   => array(
                Yii::t('link', 'Links') => $this->createUrl('link/index'),
                Yii::t('app', 'Update'),
            )
        ));

        $this->render('form', compact('link'));
    }

    /**
     * Delete an existing link
     */
    public function actionDelete($id)
    {
        //die('ok');
		$link = Link::model()->findByPk((int)$id);

        if (empty($link)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $link->delete();

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('link/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $link,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }

    /**
     * Generate the slug for an link based on the link title
     */
    public function actionSlug()
    {
        $request = Yii::app()->request;

        if (!$request->isAjaxRequest) {
            $this->redirect(array('link/index'));
        }

        $link = new Link();
        $link->link_id = (int)$request->getPost('link_id');
        $link->slug = $request->getPost('string');

        $category = new ArticleCategory();
        $category->slug = $link->slug;

        $link->slug = $category->generateSlug();
        $link->slug = $link->generateSlug();

        return $this->renderJson(array('result' => 'success', 'slug' => $link->slug));
    }

   
}
