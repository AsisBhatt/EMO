<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * 
 * Controller file for service settings.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */
 
class Ip_location_services_ext_locatorhqController extends Controller
{
    // init the controller
    public function init()
    {
        parent::init();
        Yii::import('ext-ip-location-locatorhq.backend.models.*');
    }
    
    // move the view path
    public function getViewPath()
    {
        return Yii::getPathOfAlias('ext-ip-location-locatorhq.backend.views');
    }
    
    /**
     * Default action.
     */
    public function actionIndex()
    {
        $extensionInstance = Yii::app()->extensionsManager->getExtensionInstance('ip-location-locatorhq');
        
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;
        
        $model = new IpLocationLocatorhqExtModel();
        $model->populate($extensionInstance);

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($model->modelName, array()))) {
            $model->attributes = $attributes;
            if ($model->validate()) {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
                $model->save($extensionInstance);
            } else {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            }
        }
        
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('ext_ip_location_locatorhq', 'Ip location service from Locatorhq.com'),
            'pageHeading'       => Yii::t('ext_ip_location_locatorhq', 'Ip location service from Locatorhq.com'),
            'pageBreadcrumbs'   => array(
                Yii::t('ip_location', 'Ip location services') => $this->createUrl('ip_location_services/index'),
                Yii::t('ext_ip_location_locatorhq', 'Ip location service from Locatorhq.com'),
            )
        ));

        $this->render('settings', compact('model'));
    }
}