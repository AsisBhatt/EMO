<?php defined('MW_PATH') || exit('No direct script access allowed');

/** 
 * Controller file for service process.
 * 
 * @package Unika DMS
 * @subpackage Payment Gateway Paypal
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */
 
class Payment_gateway_ext_paypalController extends Controller
{
    // the extension instance
    public $extension;
    
    /**
     * Process the IPN
     */
    public function actionIpn()
    {
        if (!Yii::app()->request->isPostRequest) {
            $this->redirect(array('price_plans/index'));
        }
        
        $postData = Yii::app()->params['POST'];
        if (!$postData->itemAt('custom')) {
            Yii::app()->end();
        }

        $transaction = PricePlanOrderTransaction::model()->findByAttributes(array(
            'payment_gateway_transaction_id' => $postData->itemAt('custom'),
            'status'                         => PricePlanOrderTransaction::STATUS_PENDING_RETRY,
        ));
        
        if (empty($transaction)) {
            Yii::app()->end();
        }
        
        $newTransaction = clone $transaction;
        $newTransaction->transaction_id                 = null;
        $newTransaction->transaction_uid                = null;
        $newTransaction->isNewRecord                    = true;
        $newTransaction->date_added                     = new CDbExpression('NOW()');
        $newTransaction->status                         = PricePlanOrderTransaction::STATUS_FAILED;
        $newTransaction->payment_gateway_response       = print_r($postData->toArray(), true);
        $newTransaction->payment_gateway_transaction_id = $postData->itemAt('txn_id');
        
        $model = $this->extension->getExtModel();

        $postData->add('cmd', '_notify-validate');
        $request = AppInitHelper::simpleCurlPost($model->getModeUrl(), $postData->toArray());
        
        if ($request['status'] != 'success') {
            $newTransaction->save(false);
            Yii::app()->end();
        }
        
        $paymentStatus  = strtolower(trim($postData->itemAt('payment_status'))); 
        $paymentPending = strpos($paymentStatus, 'pending') === 0;
        $paymentFailed  = strpos($paymentStatus, 'failed') === 0;
        $paymentSuccess = strpos($paymentStatus, 'completed') === 0;
        
        $verified  = strpos(strtolower(trim($request['message'])), 'verified') === 0;
        $sameEmail = $postData->itemAt('receiver_email') == $model->email || $postData->itemAt('business') == $model->email;
        $order     = $transaction->order;
        
        if ($order->status == PricePlanOrder::STATUS_COMPLETE) {
            $newTransaction->save(false);
            Yii::app()->end();
        }
        
        if (!$verified || !$sameEmail || $paymentFailed) {
            $order->status = PricePlanOrder::STATUS_FAILED;
            $order->save(false);
            
            $transaction->status = PricePlanOrderTransaction::STATUS_FAILED;
            $transaction->save(false);
            
            $newTransaction->save(false);
            
            Yii::app()->end();
        }
        
        if ($paymentPending) {
            $newTransaction->status = PricePlanOrderTransaction::STATUS_PENDING_RETRY;
            $newTransaction->save(false);
            Yii::app()->end();
        }
        
        $order->status = PricePlanOrder::STATUS_COMPLETE;
        $order->save(false);
        
        $transaction->status = PricePlanOrderTransaction::STATUS_SUCCESS;
        $transaction->save(false);
        
        $newTransaction->status = PricePlanOrderTransaction::STATUS_SUCCESS;
        $newTransaction->save(false);

        Yii::app()->end();
    }
}