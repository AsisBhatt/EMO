<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * OptionMonetizationMonetization
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.4
 */
 
class OptionMonetizationMonetization extends OptionBase
{
    // settings category
    protected $_categoryName = 'system.monetization.monetization';

    public $enabled = 'no';

    public function rules()
    {
        $rules = array(
            array('enabled', 'required'),
            array('enabled', 'in', 'range' => array_keys($this->getYesNoOptions())),
        );
        
        return CMap::mergeArray($rules, parent::rules());    
    }

    public function attributeLabels()
    {
        $labels = array(
            'enabled' => Yii::t('settings', 'Enabled'),
        );
        
        return CMap::mergeArray($labels, parent::attributeLabels());    
    }
    
    public function attributePlaceholders()
    {
        $placeholders = array(
            'enabled' => '',
        );
        
        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
    }
    
    public function attributeHelpTexts()
    {
        $texts = array(
            'enabled' => Yii::t('settings', 'Whether the whole monetization module is enabled'),
        );
        
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }
}
