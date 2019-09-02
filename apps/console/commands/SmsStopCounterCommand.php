<?php defined('MW_PATH') || exit('No direct script access allowed');
/**
 * SmsReplyCommand
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */
class SmsStopCounterCommand extends ConsoleCommand 
{
    public function actionIndex() 
    {
		$options = Yii::app()->options;
		$customers_array = Customer::model()->findByAttributes(array('status' => 'active'))->findAll();
		
		foreach($customers_array as $customers_key => $customers){
			
			$email_array = TransactionalEmail::model()->findByAttributes(array('to_email' => $customers->email,'subject' => 'Received STOP Requests - Beelift'));
			
			$customer_company = CustomerCompany::model()->findByAttributes(array('customer_id' => $customers->customer_id));
			if(!is_array($email_array) && !count($email_array)){
		
				if(!empty($customer_company)){
					
					$sms_rply_stop_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM uic_sms_rply WHERE customer_id='".$customers->customer_id."' AND sms_rply_to_number='".$customer_company->flowroute_sms_num."' AND sms_rply_body LIKE '%STOP%'")->queryScalar();
					
					if($sms_rply_stop_count > 89){
						$emailTemplate  = $options->get('system.email_templates.common');
						$emailBody      = Yii::app()->command->renderFile(Yii::getPathOfAlias('console.views.sms-stop-counter').'.php', compact('customers','customer_company'), true);
						$emailTemplate  = str_replace('[CONTENT]', $emailBody, $emailTemplate);
						
						$email = new TransactionalEmail();
						$email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
						$email->to_name      = $customers->getFullName();
						$email->to_email     = $customers->email;
						$email->from_name    = $options->get('system.common.site_name', 'Marketing website');
						$email->subject      = 'Received STOP Requests - Beelift';
						$email->body         = $emailTemplate;
						$email->save();
					}
				}
			}
		}
		
		return 0;
    }
}
