<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Ip_location_servicesController
 * 
 * Handles the actions for ip location services related tasks
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.2
 */
 
class Ip_location_servicesController extends Controller
{

    /**
     * Display available services
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
        $model = new IpLocationServicesList();
        
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('ip_location', 'Ip location services'), 
            'pageHeading'       => Yii::t('ip_location', 'Ip location services'),
            'pageBreadcrumbs'   => array(
                Yii::t('ip_location', 'Ip location services'),
            ),
        ));
        
        $this->render('index', compact('model'));
    }

}