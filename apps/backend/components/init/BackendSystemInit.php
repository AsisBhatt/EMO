<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * BackendSystemInit
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class BackendSystemInit extends CApplicationComponent 
{
    protected $_hasRanOnBeginRequest = false;
    protected $_hasRanOnEndRequest = false;
    
    public function init()
    {
        parent::init();
        Yii::app()->attachEventHandler('onBeginRequest', array($this, '_runOnBeginRequest'));
        Yii::app()->attachEventHandler('onEndRequest', array($this, '_runOnEndRequest'));
    }
    
    public function _runOnBeginRequest(CEvent $event)
    {
        if ($this->_hasRanOnBeginRequest) {
            return;
        }

        // a safety hook for logged in vs not logged in users.
        Yii::app()->hooks->addAction('backend_controller_init', array($this, '_checkControllerAccess'));

        // register core assets if not cli mode and no theme active
        if (!MW_IS_CLI && (!Yii::app()->hasComponent('themeManager') || !Yii::app()->getTheme())) {
            $this->registerAssets();
        }

        // and mark the event as completed.
        $this->_hasRanOnBeginRequest = true;
    }
    
    public function _runOnEndRequest(CEvent $event)
    {
        if ($this->_hasRanOnEndRequest) {
            return;
        }

        // and mark the event as completed.
        $this->_hasRanOnEndRequest = true;
    }

    // callback for user_controller_init and user_before_controller_action action.
    public function _checkControllerAccess() 
    {
        static $_unprotectedControllersHookDone = false;
        static $_hookCalled = false;
        
        if ($_hookCalled || !$controller = Yii::app()->getController()) {
            return;
        }
        
        $_hookCalled = true;
        $unprotectedControllers = (array)Yii::app()->params->itemAt('unprotectedControllers');

        if (!$_unprotectedControllersHookDone) {
            Yii::app()->params->add('unprotectedControllers', $unprotectedControllers);
            $_unprotectedControllersHookDone = true;
        }

        if (!in_array($controller->id, $unprotectedControllers) && !Yii::app()->user->getId()) {
            // make sure we set a return url to the previous page that required the user to be logged in.
            Yii::app()->user->setReturnUrl(Yii::app()->request->requestUri);
            // and redirect to the login url.
            $controller->redirect(Yii::app()->user->loginUrl);
        }
        
        // since 1.3.5, user permission to controller action, aka route
        if (!in_array($controller->id, $unprotectedControllers) && Yii::app()->user->getId()) {
            $controller->onBeforeAction = array($this, '_checkRouteAccess');
        }
        
        // check version update right before executing the action!
        $controller->onBeforeAction = array($this, '_checkUpdateVersion');
        
        // check app wide messages
        $controller->onBeforeAction = array($this, '_checkAppWideMessages');
    }
    
    public function registerAssets()
    {
        Yii::app()->hooks->addFilter('register_scripts', array($this, '_registerScripts'));
        Yii::app()->hooks->addFilter('register_styles', array($this, '_registerStyles'));
    }
    
    public function _registerScripts(CList $scripts)
    {
        $apps = Yii::app()->apps;
        $scripts->mergeWith(array(
		
            array('src' => $apps->getBaseUrl('assets/js/common/bootstrap.min.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/common/bootstrap.min.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/common/js.cookie.min.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/common/jquery.slimscroll.min.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/common/jquery.blockui.min.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/common/bootstrap-switch.min.js'), 'priority' => -1000),           
            array('src' => $apps->getBaseUrl('assets/js/common/bootstrap-fileinput.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/common/jquery.sparkline.min.js'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/js/common/app.min.js'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/js/common/layout.min.js'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/js/common/demo.min.js'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/js/common/quick-sidebar.min.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/common/quick-nav.min.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('backend/assets/js/custom.js'), 'priority' => -1000),            
            array('src' => $apps->getBaseUrl('assets/js/knockout-3.1.0.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/notify.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/adminlte.js'), 'priority' => -1000),
			//array('src' => AssetsUrl::js('app.js'), 'priority' => -1000),
        )); 
         
        // since 1.3.4.8
        if (is_file(AssetsPath::js('app-custom.js'))) {
            $scripts->mergeWith(array(
                array('src' => AssetsUrl::js('app-custom.js'), 'priority' => -1000),
            ));
        }
        
        return $scripts;
    }
    
    public function _registerStyles(CList $styles)
    {
        $apps = Yii::app()->apps;
        $styles->mergeWith(array(
            array('src' => 'https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all', 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/css/common/font-awesome.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/simple-line-icons.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/bootstrap.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/bootstrap-switch.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/daterangepicker.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/morris.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/fullcalendar.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/jqvmap.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/components.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/plugins.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/layout.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/darkblue.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/common/custom.min.css'), 'priority' => -1000),
           // array('src' => $apps->getBaseUrl('assets/css/ionicons.min.css'), 'priority' => -1000),
			array('src' => $apps->getBaseUrl('assets/css/adminlte.css'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/css/common.css'), 'priority' => -1000),
            //array('src' => AssetsUrl::css('style.css'), 'priority' => -1000),
        ));
        
        // since 1.3.5.4 - skin
        $skinName = null;
        if (($_skinName = Yii::app()->options->get('system.customization.backend_skin'))) {
            if (is_file(Yii::getPathOfAlias('root.backend.assets.css') . '/' . $_skinName . '.css')) {
                $styles->add(array('src' => $apps->getBaseUrl('backend/assets/css/' . $_skinName . '.css'), 'priority' => -1000));
                $skinName = $_skinName;
            } elseif (is_file(Yii::getPathOfAlias('root.assets.css') . '/' . $_skinName . '.css')) {
                $styles->add(array('src' => $apps->getBaseUrl('assets/css/' . $_skinName . '.css'), 'priority' => -1000));
                $skinName = $_skinName;
            } else {
                $_skinName = null;
            }
        }
        if (!$skinName) {
            $styles->add(array('src' => $apps->getBaseUrl('assets/css/skin-blue.css'), 'priority' => -1000));
            $skinName = 'skin-blue';
        }
        Yii::app()->getController()->getData('bodyClasses')->add($skinName);
        // end 1.3.5.4

        // since 1.3.4.8
        if (is_file(AssetsPath::css('style-custom.css'))) {
            $styles->mergeWith(array(
                array('src' => AssetsUrl::css('style-custom.css'), 'priority' => -1000),
            ));
        }
        
        return $styles;
    }
    
    // since 1.3.5
    public function _checkRouteAccess($event)
    {
        Yii::trace('Checking route access permission for controller ' . $event->sender->id . ', and action ' . $event->sender->action->id);
        if (Yii::app()->user->getModel()->hasRouteAccess($event->sender->route)) {
            return;
        }
        $message = Yii::t('user_groups', 'You do not have the permission to access this resource!');
        if (Yii::app()->request->isAjaxRequest) {
            return $event->sender->renderJson(array(
                'status'  => 'error', 
                'message' => $message,
            ));
        }
        throw new CHttpException(403, $message);
    }
 
    public function _checkUpdateVersion($event)
    {
        $controller = $event->sender;
        $options    = Yii::app()->options;
        $request    = Yii::app()->request;
        
        if ($request->isAjaxRequest) {
            return;
        }
        
        if (in_array($controller->id, array('update', 'guest'))) {
            return;
        }
        
        if ($controller->id == 'dashboard' && $controller->getAction() && $controller->getAction()->id != 'index') {
            return;
        }
        
        $checkEnabled   = $options->get('system.common.check_version_update', 'yes') == 'yes';
        $currentVersion = $options->get('system.common.version');
        $updateVersion  = $options->get('system.common.version_update.current_version', $currentVersion);
        
        if (!$checkEnabled || !$updateVersion || !version_compare($updateVersion, $currentVersion, '>')) {
            return;
        }
        
        Yii::app()->notify->addWarning('<strong><u>' . Yii::t('app', 'Version {version} is now available for download. Please update your application!', array(
            '{version}' => $updateVersion
        )).'</u></strong>');
    }
    
    public function _checkAppWideMessages($event)
    {
        $controller = $event->sender;
        $options    = Yii::app()->options;
        $request    = Yii::app()->request;
        
        if ($request->isAjaxRequest) {
            return;
        }
        
        if (in_array($controller->id, array('update', 'guest'))) {
            return;
        }
        
        $errorKeys = array('system.license.error_message');
        foreach ($errorKeys as $errorKey) {
            $error = $options->get($errorKey);
            if (!empty($error)) {
                Yii::app()->notify->addError($error);
            }
        }
    }
}