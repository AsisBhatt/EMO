<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * ExtensionsController
 *
 * Handles the actions for extensions related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class ExtensionsController extends Controller
{
    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
        $filters = array(
            'postOnly + delete', // we only allow deletion via POST request
        );

        return CMap::mergeArray($filters, parent::filters());
    }

    /**
     * List all available extensions
     */
    public function actionIndex()
    {
        $model = new ExtensionHandlerForm('upload');

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('extensions', 'View extensions'),
            'pageHeading'       => Yii::t('extensions', 'View extensions'),
            'pageBreadcrumbs'   => array(
                Yii::t('extensions', 'Extensions') => $this->createUrl('extensions/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('index', compact('model'));
    }

    /**
     * Upload a new extensions
     */
    public function actionUpload()
    {
        $request = Yii::app()->request;
        $notify = Yii::app()->notify;
        $model = new ExtensionHandlerForm('upload');

        if ($request->isPostRequest && $request->getPost($model->modelName)) {
            $model->archive = CUploadedFile::getInstance($model, 'archive');
            if (!$model->upload()) {
               $notify->addError($model->shortErrors->getAllAsString());
            } else {
               $notify->addSuccess(Yii::t('extensions', 'Your extension has been successfully uploaded!'));
            }
            $this->redirect(array('extensions/index'));
          }

          $notify->addError(Yii::t('extensions', 'Please select an extension archive for upload!'));
          $this->redirect(array('extensions/index'));
    }

    /**
     * Enable extension
     */
    public function actionEnable($id)
    {
        $notify = Yii::app()->notify->clearAll();
        $manager = Yii::app()->extensionsManager;

        if (!$manager->enableExtension($id)) {
            $notify->addError($manager->getErrors());
        } else {
            $message = Yii::t('extensions', 'The extension "{name}" has been successfully enabled!', array(
                '{name}' => CHtml::encode($manager->getExtensionInstance($id)->name),
            ));
            $notify->addSuccess($message);
        }

        $this->redirect(array('extensions/index'));
    }

    /**
     * Disable extension
     */
    public function actionDisable($id)
    {
        $notify  = Yii::app()->notify->clearAll();
        $manager = Yii::app()->extensionsManager;

        if (!$manager->disableExtension($id)) {
            $notify->addError($manager->getErrors());
        } else {
            $message = Yii::t('extensions', 'The extension "{name}" has been successfully disabled!', array(
                '{name}' => CHtml::encode($manager->getExtensionInstance($id)->name),
            ));
            $notify->addSuccess($message);
        }

        $this->redirect(array('extensions/index'));
    }

    /**
     * Update extension
     */
    public function actionUpdate($id)
    {
        $notify  = Yii::app()->notify->clearAll();
        $manager = Yii::app()->extensionsManager;

        if (!$manager->updateExtension($id)) {
            $notify->addError($manager->getErrors());
        } else {
            $message = Yii::t('extensions', 'The extension "{name}" has been successfully updated!', array(
                '{name}' => CHtml::encode($manager->getExtensionInstance($id)->name),
            ));
            $notify->addSuccess($message);
        }

        $this->redirect(array('extensions/index'));
    }

    /**
     * Delete extension
     */
    public function actionDelete($id)
    {
        $notify     = Yii::app()->notify->clearAll();
        $manager    = Yii::app()->extensionsManager;
        $request    = Yii::app()->request;

        if (!$manager->deleteExtension($id)) {
            $notify->addError($manager->getErrors());
        } else {
            $message = Yii::t('extensions', 'The extension "{name}" has been successfully deleted!', array(
                '{name}' => CHtml::encode($manager->getExtensionInstance($id)->name),
            ));
            $notify->addSuccess($message);
        }

        $redirect = null;
        if (!$request->isAjaxRequest) {
            $redirect = array('extensions/index');
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }

}
