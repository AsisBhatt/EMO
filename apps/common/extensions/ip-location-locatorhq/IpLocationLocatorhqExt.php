<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Ip location - LocatorHQ.com
 * 
 * Creates the connection to locatorhq.com website to retrieve ip location data.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */
 
class IpLocationLocatorhqExt extends ExtensionInit 
{
    // name of the extension as shown in the backend panel
    public $name = 'Ip location - locatorhq.com';
    
    // description of the extension as shown in backend panel
    public $description = 'Creates the connection to locatorhq.com website to retrieve ip location data.';
    
    // current version of this extension
    public $version = '1.0';
    
    // the author name
    public $author = 'Cristian Serban';
    
    // author website
    public $website = 'http://www.mailwizz.com/';
    
    // contact email address
    public $email = 'cristian.serban@mailwizz.com';
    
    // in which apps this extension is allowed to run
    public $allowedApps = array('frontend', 'backend', 'customer');

    // can this extension be deleted? this only applies to core extensions.
    protected $_canBeDeleted = false;
    
    // can this extension be disabled? this only applies to core extensions.
    protected $_canBeDisabled = true;

    // run the extension
    public function run()
    {
        $hooks = Yii::app()->hooks;
        
        // register the extension page route and controller only if backend
        if ($this->isAppName('backend')) {
            
            // register the url rule to resolve the extension page.
            Yii::app()->urlManager->addRules(array(
                array('ip_location_services_ext_locatorhq/index', 'pattern' => 'ip-location-services/locatorhq'),
                array('ip_location_services_ext_locatorhq/<action>', 'pattern' => 'ip-location-services/locatorhq/*'),
            ));
            
            // add the backend controller
            Yii::app()->controllerMap['ip_location_services_ext_locatorhq'] = array(
                'class' => 'ext-ip-location-locatorhq.backend.controllers.Ip_location_services_ext_locatorhqController',
            );
            
            // register the service in the list of available services.
            $hooks->addFilter('backend_ip_location_services_display_list', array($this, '_registerServiceForDisplay'));
        
        } elseif ($this->isAppName('frontend')) {
            
            // register the hooks
            if ($this->getOption('status', 'disabled') == 'enabled' && $this->getOption('api_key') && $this->getOption('username')) {
                // track email opens if allowed
                if ($this->getOption('status_on_email_open', 'disabled') == 'enabled') {
                    $hooks->addAction('frontend_campaigns_after_track_opening', array($this, '_registerServiceForSavingLocation'), (int)$this->getOption('sort_order', 0));
                }
                // track url clicks if allowed
                if ($this->getOption('status_on_track_url', 'disabled') == 'enabled') {
                    $hooks->addAction('frontend_campaigns_after_track_url', array($this, '_registerServiceForSavingLocation'), (int)$this->getOption('sort_order', 0));
                }
                // track unsubscribes if allowed
                if ($this->getOption('status_on_unsubscribe', 'disabled') == 'enabled') {
                    $hooks->addAction('frontend_lists_after_track_campaign_unsubscribe', array($this, '_registerServiceForSavingLocation'), (int)$this->getOption('sort_order', 0));
                }
            }

        } elseif ($this->isAppName('customer')) {

            // register the hooks
            if ($this->getOption('status', 'disabled') == 'enabled' && $this->getOption('api_key') && $this->getOption('username')) {

                // track customer login
                if ($this->getOption('status_on_customer_login', 'disabled') == 'enabled') {
                    $hooks->addAction('customer_login_log_add_new_before_save', array($this, '_trackCustomerLoginLog'));
                }

            }

        }
    }
    
    /**
     * Add the landing page for this extension (settings/general info/etc)
     */
    public function getPageUrl()
    {
        return Yii::app()->createUrl('ip_location_services_ext_locatorhq/index');
    }
    
    // register the service in the available services list
    public function _registerServiceForDisplay(array $registeredServices = array())
    {
        if (isset($registeredServices['locatorhq'])) {
            return $registeredServices;
        }
        
        $registeredServices['locatorhq'] = array(
            'id'            => 'locatorhq',
            'name'          => Yii::t('ext_ip_location_locatorhq', 'Locatorhq.com'),
            'description'   => Yii::t('ext_ip_location_locatorhq', 'Offers IP location data based on the Locatorhq.com web service'),
            'status'        => $this->getOption('status', 'disabled'),
            'sort_order'    => (int)$this->getOption('sort_order', 0),
            'page_url'      => $this->getPageUrl(),
        );
        
        return $registeredServices;
    }
    
    // register the service to save the location of the ip address
    public function _registerServiceForSavingLocation(Controller $controller, $trackModel)
    {
        // if the ip data has been saved already, don't bother.
        if ($controller->getData('ipLocationSaved') || !empty($trackModel->location_id) || empty($trackModel->id)) {
            return false;
        }

        $model = null;
        if ($trackModel instanceof CampaignTrackOpen) {
            $model = CampaignTrackOpen::model();
        } elseif ($trackModel instanceof CampaignTrackUrl) {
            $model = CampaignTrackUrl::model();
        } elseif ($trackModel instanceof CampaignTrackUnsubscribe) {
            $model = CampaignTrackUnsubscribe::model();
        }
        
        if (empty($model)) {
            return false;
        }
        
        $exists = IpLocation::model()->findByAttributes(array(
            'ip_address' => $trackModel->ip_address,
        ));
        
        if (!empty($exists)) {
            $model->updateByPk((int)$trackModel->id, array(
                'location_id' => $exists->location_id
            ));
            return $controller->setData('ipLocationSaved', true);
        }
        
        $remoteUrl = 'http://api.locatorhq.com/?user=%s&key=%s&ip=%s&format=json';
        $remoteUrl = sprintf($remoteUrl, $this->getOption('username'), $this->getOption('api_key'), $trackModel->ip_address);
        $response = AppInitHelper::simpleCurlGet($remoteUrl, 10);
        
        if ($response['status'] == 'error' || empty($response['message'])) {
            return false;
        }
        
        $response = CJSON::decode($response['message']);

        if (!isset($response['countryCode'], $response['countryName'], $response['latitude'], $response['longitude'])) {
            return false;
        }

        $response = Yii::app()->ioFilter->xssClean($response);
        
        $exists = IpLocation::model()->findByAttributes(array(
            'ip_address' => $trackModel->ip_address,
        ));
        
        if (!empty($exists)) {
            $model->updateByPk((int)$trackModel->id, array(
                'location_id' => $exists->location_id
            ));
            return $controller->setData('ipLocationSaved', true);
        }
        
        $location = new IpLocation();
        $location->ip_address   = $trackModel->ip_address;
        $location->country_code = strtoupper($response['countryCode']);
        $location->country_name = ucwords(strtolower($response['countryName']));
        $location->zone_name    = !isset($response['region']) ? null : ucwords(strtolower($response['region']));
        $location->city_name    = !isset($response['city']) ? null : ucwords(strtolower($response['city']));
        $location->latitude     = (float)$response['latitude'];
        $location->longitude    = (float)$response['longitude'];
        
        if (!$location->save()) {
            return false;
        }
        
        $model->updateByPk((int)$trackModel->id, array(
            'location_id' => $location->location_id
        ));
        return $controller->setData('ipLocationSaved', true);
    }

    // track customer login
    public function _trackCustomerLoginLog(CAttributeCollection $collection)
    {
        if (empty($collection->model->ip_address) ||
            !FilterVarHelper::ip($collection->model->ip_address) ||
            !empty($collection->model->location_id)) {
            return true;
        }

        $location = IpLocation::model()->findByAttributes(array(
            'ip_address' => $collection->model->ip_address,
        ));

        if (!empty($location)) {
            $collection->model->location_id = $location->location_id;
            return true;
        }

        $remoteUrl = 'http://api.locatorhq.com/?user=%s&key=%s&ip=%s&format=json';
        $remoteUrl = sprintf($remoteUrl, $this->getOption('username'), $this->getOption('api_key'), $collection->model->ip_address);
        $response  = AppInitHelper::simpleCurlGet($remoteUrl, 10);

        if ($response['status'] == 'error' || empty($response['message'])) {
            return false;
        }

        $response = CJSON::decode($response['message']);

        if (!isset($response['countryCode'], $response['countryName'], $response['latitude'], $response['longitude'])) {
            return false;
        }

        $response = Yii::app()->ioFilter->xssClean($response);

        $location = IpLocation::model()->findByAttributes(array(
            'ip_address' => $collection->model->ip_address,
        ));

        if (!empty($location)) {
            $collection->model->location_id = $location->location_id;
            return true;
        }

        $location = new IpLocation();
        $location->ip_address   = $collection->model->ip_address;
        $location->country_code = strtoupper($response['countryCode']);
        $location->country_name = ucwords(strtolower($response['countryName']));
        $location->zone_name    = !isset($response['region']) ? null : ucwords(strtolower($response['region']));
        $location->city_name    = !isset($response['city']) ? null : ucwords(strtolower($response['city']));
        $location->latitude     = (float)$response['latitude'];
        $location->longitude    = (float)$response['longitude'];

        if (!$location->save()) {
            return false;
        }

        $collection->model->location_id = $location->location_id;
        return true;
    }
}