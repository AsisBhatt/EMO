<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * PaymentHandlerAbstract
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.4
 */
 
abstract class PaymentHandlerAbstract extends CApplicationComponent
{
    // the extension instance for easy access
    public $extension;
    
    // the controller calling the handler
    public $controller;

    abstract public function renderPaymentView();
    
    abstract public function processOrder();
}
