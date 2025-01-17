<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * RecaptchaExt
 * 
 * @package Unika DMS
 * @subpackage recaptcha
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */
 
class RecaptchaExt extends ExtensionInit 
{
    // name of the extension as shown in the backend panel
    public $name = 'Recaptcha';
    
    // description of the extension as shown in backend panel
    public $description = 'Protect the public forms using Google\'s Recaptcha';
    
    // current version of this extension
    public $version = '1.0';
    
    // minimum app version
    public $minAppVersion = '1.3.5.8';
    
    // the author name
    public $author = 'Cristian Serban';
    
    // author website
    public $website = 'http://www.mailwizz.com/';
    
    // contact email address
    public $email = 'cristian.serban@mailwizz.com';
    
    // in which apps this extension is allowed to run
    public $allowedApps = array('backend', 'frontend', 'customer');

    // can this extension be deleted? this only applies to core extensions.
    protected $_canBeDeleted = false;
    
    // can this extension be disabled? this only applies to core extensions.
    protected $_canBeDisabled = true;
    
    // run the extension
    public function run()
    {
        Yii::import('ext-recaptcha.common.models.*');

        if ($this->isAppName('backend')) {
            // add the url rules
            Yii::app()->urlManager->addRules(array(
                array('ext_recaptcha_settings/index', 'pattern' => 'extensions/recaptcha/settings'),
                array('ext_recaptcha_settings/<action>', 'pattern' => 'extensions/recaptcha/settings/*'),
            ));
            
            // add the controller
            Yii::app()->controllerMap['ext_recaptcha_settings'] = array(
                'class'     => 'ext-recaptcha.backend.controllers.Ext_recaptcha_settingsController',
                'extension' => $this,
            );
        }

        // keep these globally for easier access from the callback.
        Yii::app()->params['extensions.recaptcha.data.enabled']                = $this->getOption('enabled') == 'yes';
        Yii::app()->params['extensions.recaptcha.data.enabled_for_list_forms'] = $this->getOption('enabled_for_list_forms') == 'yes';

        Yii::app()->params['extensions.recaptcha.data.enabled_for_registration'] = $this->getOption('enabled_for_registration') == 'yes';
        Yii::app()->params['extensions.recaptcha.data.enabled_for_login']        = $this->getOption('enabled_for_login') == 'yes';
        Yii::app()->params['extensions.recaptcha.data.enabled_for_forgot']       = $this->getOption('enabled_for_forgot') == 'yes';
        
        Yii::app()->params['extensions.recaptcha.data.site_key']               = $this->getOption('site_key');
        Yii::app()->params['extensions.recaptcha.data.secret_key']             = $this->getOption('secret_key');
        
        if ($this->getOption('enabled') != 'yes' || strlen($this->getOption('site_key')) < 20 || strlen($this->getOption('secret_key')) < 20) {
            return;
        }

        if ($this->isAppName('frontend') && Yii::app()->params['extensions.recaptcha.data.enabled_for_list_forms']) {
            Yii::app()->hooks->addAction('frontend_list_subscribe_at_transaction_start', array($this, '_listFormCheckSubmission'));
            Yii::app()->hooks->addFilter('frontend_list_subscribe_before_transform_list_fields', array($this, '_listFormAppendHtml'));

            Yii::app()->hooks->addAction('frontend_list_update_profile_at_transaction_start', array($this, '_listFormCheckSubmission'));
            Yii::app()->hooks->addFilter('frontend_list_update_profile_before_transform_list_fields', array($this, '_listFormAppendHtml'));
        }
        
        if ($this->isAppName('customer')) {
            Yii::app()->hooks->addAction('customer_controller_guest_before_action', array($this, '_guestActions'));
        }
    }

    // Add the landing page for this extension (settings/general info/etc)
    public function getPageUrl()
    {
        return Yii::app()->createUrl('ext_recaptcha_settings/index');
    }

    // callback to respond to the action hook: frontend_list_subscribe_at_transaction_start
    // this is inside a try/catch block so we have to throw an exception on failure.
    public function _listFormCheckSubmission()
    {
        $request  = Yii::app()->request;
        $response = AppInitHelper::simpleCurlPost('https://www.google.com/recaptcha/api/siteverify', array(
            'secret'   => Yii::app()->params['extensions.recaptcha.data.secret_key'],
            'response' => $request->getPost('g-recaptcha-response'),
            'remoteip' => $request->getUserHostAddress(),
        ));
        $response = CJSON::decode($response['message']);
        if (empty($response['success'])) {
            throw new Exception(Yii::t("lists", "Invalid captcha response!"));
        }
    }

    // callback to respond to the filter hook: frontend_list_subscribe_before_transform_list_fields
    public function _listFormAppendHtml($content)
    {
        $controller = Yii::app()->getController();
        $controller->getData('pageScripts')->add(array('src' => 'https://www.google.com/recaptcha/api.js'));
        
        $append  = sprintf('<div class="g-recaptcha pull-right" data-sitekey="%s"></div>', Yii::app()->params['extensions.recaptcha.data.site_key']);
        $append .= '<div class="clearfix"><!-- --></div>';

        return preg_replace('/\[LIST_FIELDS\]/', "[LIST_FIELDS]\n" . $append, $content, 1, $count);
    }
    
    public function _guestActions($action)
    {
        if (!in_array($action->id, array('index', 'register', 'forgot_password'))) {
            return;
        }
        
        $canShow = Yii::app()->params['extensions.recaptcha.data.enabled_for_registration'] || 
                   Yii::app()->params['extensions.recaptcha.data.enabled_for_login'] ||
                   Yii::app()->params['extensions.recaptcha.data.enabled_for_forgot'];
        
        if (!$canShow) {
            return;
        }
        
        $action->controller->getData('pageScripts')->add(array('src' => 'https://www.google.com/recaptcha/api.js'));
        
        if (Yii::app()->params['extensions.recaptcha.data.enabled_for_registration'] && $action->id == 'register') {
            Yii::app()->hooks->addAction('customer_controller_guest_form_submit_start', array($this, '_guestProcessForm'));
            Yii::app()->hooks->addAction('after_active_form_fields', array($this, '_guestFormAppendHtml'));
        } elseif (Yii::app()->params['extensions.recaptcha.data.enabled_for_login'] && $action->id == 'index') {
            Yii::app()->hooks->addAction('customer_controller_guest_form_submit_start', array($this, '_guestProcessForm'));
            Yii::app()->hooks->addAction('after_active_form_fields', array($this, '_guestFormAppendHtml'));
        } elseif (Yii::app()->params['extensions.recaptcha.data.enabled_for_forgot'] && $action->id == 'forgot_password') {
            Yii::app()->hooks->addAction('customer_controller_guest_form_submit_start', array($this, '_guestProcessForm'));
            Yii::app()->hooks->addAction('after_active_form_fields', array($this, '_guestFormAppendHtml'));
        }
    }
    
    public function _guestProcessForm($collection)
    {
        $request  = Yii::app()->request;
        $response = AppInitHelper::simpleCurlPost('https://www.google.com/recaptcha/api/siteverify', array(
            'secret'   => Yii::app()->params['extensions.recaptcha.data.secret_key'],
            'response' => $request->getPost('g-recaptcha-response'),
            'remoteip' => $request->getUserHostAddress(),
        ));
        $response = CJSON::decode($response['message']);
        if (empty($response['success'])) {
            $collection->error = Yii::t("lists", "Invalid captcha response!");
            return;
        }
    }
    
    public function _guestFormAppendHtml($collection)
    {
        $append  = sprintf('<div class="col-lg-12 g-recaptcha" data-sitekey="%s"></div>', Yii::app()->params['extensions.recaptcha.data.site_key']);
        $append .= '<div class="clearfix"><!-- --></div>';
        echo $append;
    }
}