<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Payment_gatewaysController
 * 
 * Handles the actions for payment gateways related tasks
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.4
 */
 
class Payment_gatewaysController extends Controller
{
    /**  
     * Display available gateways
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;  
        $model = new PaymentGatewaysList();
        
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('payment_gateways', 'Payment gateways'), 
            'pageHeading'       => Yii::t('payment_gateways', 'Payment gateways'),
            'pageBreadcrumbs'   => array(
                Yii::t('payment_gateways', 'Payment gateways'),
            ),
        ));
        
        $this->render('index', compact('model'));
    }

}