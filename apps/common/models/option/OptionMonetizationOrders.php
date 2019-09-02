<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * OptionMonetizationOrders
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.4
 */
 
class OptionMonetizationOrders extends OptionBase
{
    // settings category
    protected $_categoryName = 'system.monetization.orders';

    // remove uncomplete orders after x days
    public $uncomplete_days_removal = 7;

    public function rules()
    {
        $rules = array(
            array('uncomplete_days_removal', 'required'),
            array('uncomplete_days_removal', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 365),
        );
        
        return CMap::mergeArray($rules, parent::rules());    
    }

    public function attributeLabels()
    {
        $labels = array(
            'uncomplete_days_removal'    => Yii::t('settings', 'Uncomplete orders removal days'),
        );
        
        return CMap::mergeArray($labels, parent::attributeLabels());    
    }
    
    public function attributePlaceholders()
    {
        $placeholders = array(
            'uncomplete_days_removal' => '',
        );
        
        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
    }
    
    public function attributeHelpTexts()
    {
        $texts = array(
            'uncomplete_days_removal' => Yii::t('settings', 'How many days to keep the uncompleted orders in the system before permanent removal'),
        );
        
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }
}
