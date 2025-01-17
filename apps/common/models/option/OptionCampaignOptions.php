<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * OptionCampaignOptions
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3
 */
 
class OptionCampaignOptions extends OptionBase
{
    protected $_categoryName = 'system.campaign.campaign_options';
    
    public $customer_select_delivery_servers = 'no';

    public function rules()
    {
        $rules = array(
            array('customer_select_delivery_servers', 'required'),
            array('customer_select_delivery_servers', 'in', 'range' => array_keys($this->getYesNoOptions())),
        );
        
        return CMap::mergeArray($rules, parent::rules());    
    }
    
    public function attributeLabels()
    {
        $labels = array(
            'customer_select_delivery_servers'   => Yii::t('settings', 'Customers can select delivery servers'),
        );
        
        return CMap::mergeArray($labels, parent::attributeLabels());    
    }
    
    public function attributePlaceholders()
    {
        $placeholders = array(
            'customer_select_delivery_servers' => '',
        );
        
        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
    }
    
    public function attributeHelpTexts()
    {
        $texts = array(
            'customer_select_delivery_servers' => Yii::t('settings', 'Wheather the customers are able to select what delivery servers to use'),

        );
        
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }
}
