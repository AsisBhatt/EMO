<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CustomerSystemInit
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class CustomerSystemInit extends CApplicationComponent
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
        Yii::app()->hooks->addAction('customer_controller_init', array($this, '_checkControllerAccess'));

        // display a global notification message to logged in customers
        //Yii::app()->hooks->addAction('customer_controller_init', array($this, '_displayNotificationMessage'));

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

    // callback for customer_controller_init and customer_before_controller_action action.
    public function _checkControllerAccess()
    {
        static $_unprotectedControllersHookDone = false;
        static $_hookCalled = false;

        if ($_hookCalled) {
            return;
        }

        $controller = Yii::app()->getController();
        $_hookCalled = true;
        $unprotectedControllers = (array)Yii::app()->params->itemAt('unprotectedControllers');

        if (!$_unprotectedControllersHookDone) {
            Yii::app()->params->add('unprotectedControllers', $unprotectedControllers);
            $_unprotectedControllersHookDone = true;
        }

        if (!in_array($controller->id, $unprotectedControllers) && !Yii::app()->customer->getId()) {
            // make sure we set a return url to the previous page that required the customer to be logged in.
            Yii::app()->customer->setReturnUrl(Yii::app()->request->requestUri);
            // and redirect to the login url.
            $controller->redirect(Yii::app()->customer->loginUrl);
        }

        if (Yii::app()->options->get('system.customer.action_logging_enabled', true)) {
            if (Yii::app()->customer->getModel()) {
                // and attach the actionLog behavior to log various actions for this customer.
                Yii::app()->customer->getModel()->attachBehavior('logAction', array(
                    'class' => 'customer.components.behaviors.CustomerActionLogBehavior',
                ));
            }
        }

        // since 1.3.4.9, check sending quota here with a probability of 50%
        // experimental for now, might get removed in future.
        if (rand(0, 100) >= 50 && Yii::app()->customer->getId() && !Yii::app()->request->isPostRequest && !Yii::app()->request->isAjaxRequest) {
            Yii::app()->customer->getModel()->getIsOverQuota();
        }
    }

    // callback for customer_controller_init.
    public function _displayNotificationMessage()
    {
        if (!Yii::app()->customer->getId() || !($customer = Yii::app()->customer->getModel())) {
            return;
        }

        if (in_array(Yii::app()->getController()->id, (array)Yii::app()->params->itemAt('unprotectedControllers'))) {
            return;
        }

        $notification = $customer->getGroupOption('common.notification_message', '');
        if (strlen(strip_tags($notification)) > 0) {
            Yii::app()->notify->addInfo($notification);
        }
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
            array('src' => $apps->getBaseUrl('customer/assets/js/custom.js'), 'priority' => -1000),            
            array('src' => $apps->getBaseUrl('customer/assets/js/app.js'), 'priority' => -1000),            
            array('src' => $apps->getBaseUrl('assets/js/knockout-3.1.0.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/notify.js'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/js/adminlte.js'), 'priority' => -1000),
           // array('src' => AssetsUrl::js('app.js'), 'priority' => -1000),
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
			array('src' => $apps->getBaseUrl('assets/css/common/ionicons.min.css'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/css/adminlte.css'), 'priority' => -1000),
            array('src' => $apps->getBaseUrl('assets/css/common.css'), 'priority' => -1000),
            //array('src' => AssetsUrl::css('style.css'), 'priority' => -1000),
        ));

        // since 1.3.5.4 - skin
        $skinName = null;
        if (($_skinName = Yii::app()->options->get('system.customization.customer_skin'))) {
            if (is_file(Yii::getPathOfAlias('root.customer.assets.css') . '/' . $_skinName . '.css')) {
                $styles->add(array('src' => $apps->getBaseUrl('customer/assets/css/' . $_skinName . '.css'), 'priority' => -1000));
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
}
