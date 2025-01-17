<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Ajax action to return map informations.
 * 
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */
 
class ActivityMapClicksAction extends CAction 
{
    public function run($campaign_uid)
    {
        $controller = $this->controller;
        $request = Yii::app()->request;
        
        if (!$request->isAjaxRequest) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
        
        $campaign   = $controller->loadCampaignModel($campaign_uid);
        $extension  = Yii::app()->extensionsManager->getExtensionInstance('campaign-activity-map');
        $model      = new CampaignTrackUrl();
        
        $criteria = new CDbCriteria();
        $criteria->select = 't.location_id, t.subscriber_id, t.ip_address, t.user_agent, t.date_added';
        $criteria->addCondition('t.location_id IS NOT NULL');
        $criteria->with = array(
            'url' => array(
                'select'    => 'url.campaign_id', 
                'joinType'  => 'INNER JOIN',
                'condition' => 'url.campaign_id = :cid',
                'params'    => array(
                    ':cid'  => (int)$campaign->campaign_id,
                ),
            ),
            'subscriber' => array(
                'select'    => 'subscriber.email',
                'joinType'  => 'INNER JOIN',
            ),
            'ipLocation' => array(
                'together'  => true,
                'joinType'  => 'INNER JOIN',
                'condition' => 'ipLocation.latitude IS NOT NULL AND ipLocation.longitude IS NOT NULL',
            ),
        );
        $criteria->group = 't.subscriber_id';
        
        $count = $model->count($criteria);
        
        $pages = new CPagination($count);
        $pages->pageSize = (int)$extension->getOption('clicks_at_once', 50);;
        $pages->applyLimit($criteria);
        
        $uniqueClicks = $model->findAll($criteria);
        $results = array();
        
        Yii::import('common.vendors.MobileDetect.*');
        $mobileDetect = new Mobile_Detect();
        
        foreach ($uniqueClicks as $click) {
            
            $device = Yii::t('campaign_reports', 'Desktop');
            if (!empty($click->user_agent)) {
                $mobileDetect->setUserAgent($click->user_agent);
                if ($mobileDetect->isMobile()) {
                    $device = Yii::t('campaign_reports', 'Mobile');
                } elseif ($mobileDetect->isTablet()) {
                    $device = Yii::t('campaign_reports', 'Tablet');
                }    
            }

            $results[] = array(
                'email'     => $click->subscriber->email,
                'ip_address'=> $click->ip_address,
                'location'  => $click->ipLocation->getLocation(),
                'device'    => $device,
                'date_added'=> $click->dateAdded,
                'latitude'  => $click->ipLocation->latitude,
                'longitude' => $click->ipLocation->longitude,
            );
        }
       
        return $controller->renderJson(array(
            'results'       => $results, 
            'pages_count'   => $pages->pageCount,
            'current_page'  => $pages->currentPage + 1,
        ));
    }
}