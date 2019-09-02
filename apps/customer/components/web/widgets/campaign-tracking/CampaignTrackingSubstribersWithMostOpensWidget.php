<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CampaignTrackingSubstribersWithMostOpensWidget
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class CampaignTrackingSubstribersWithMostOpensWidget extends CWidget 
{
    public $campaign;
    
    public $showDetailLinks = true;
    
    public function run() 
    {
        $campaign = $this->campaign;
        
        if ($campaign->status == Campaign::STATUS_DRAFT) {
            return;
        }
        
        $criteria = new CDbCriteria();
        $criteria->select = 't.subscriber_id, COUNT(*) as counter';
        $criteria->compare('t.campaign_id', $campaign->campaign_id);
        $criteria->group = 't.subscriber_id';
        $criteria->order = 'counter DESC';
        $criteria->limit = 10;
        
        $criteria->with = array('subscriber' => array(
            'together'  => true,
            'joinType'  => 'INNER JOIN',
            'select'    => 'subscriber.email',
        ));
        
        $models = CampaignTrackOpen::model()->findAll($criteria);
        
        $this->render('subscribers-with-most-opens', compact('campaign', 'models'));
    }
}