<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CampaignTrackingTopClickedLinksWidget
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class CampaignTrackingTopClickedLinksWidget extends CWidget 
{
    public $campaign;
    
    public $showDetailLinks = true;
    
    public function run() 
    {
        $campaign = $this->campaign;
        
        if ($campaign->status == Campaign::STATUS_DRAFT) {
            return;
        }
        
        if ($campaign->option->url_tracking != CampaignOption::TEXT_YES) {
            return;
        }
        
        $criteria = new CDbCriteria();
        $criteria->select = 't.*, (SELECT COUNT(*) FROM {{campaign_track_url}} WHERE url_id = t.url_id) as counter';
        $criteria->compare('t.campaign_id', $campaign->campaign_id);
        $criteria->order = 'counter DESC';
        $criteria->limit = 10;
        
        $models = CampaignUrl::model()->findAll($criteria);
        
        $this->render('top-clicked-links', compact('campaign', 'models'));
    }
}