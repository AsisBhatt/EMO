<?php defined('MW_PATH') || exit('No direct script access allowed');
/**
 * TestCommand
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */
 
class TestCommand extends ConsoleCommand 
{
    public function actionIndex() 
    {
		echo 'First';exit;
		$sms = new Sms();
		$sms->customer_id = '8';
		$sms->mobile = '+918200566412';
		$sms->message = 'Test SMS Via Command Prompt';
		$sms->response = '12345674878';
		$sms->status = 'Pending';
		$sms->save();
		return true;
    }
}