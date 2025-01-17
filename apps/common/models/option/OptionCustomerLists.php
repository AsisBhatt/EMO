<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * OptionCustomerLists
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */
 
class OptionCustomerLists extends OptionBase
{
    // settings category
    protected $_categoryName = 'system.customer_lists';
    
    // whether the customers are allowed to import
    public $can_import_subscribers = 'yes';
    
    // whether the customers are allowd to export
    public $can_export_subscribers = 'yes';
    
    // whether the customers are allowed to copy subscribers between the lists
    public $can_copy_subscribers = 'yes';
    
    // maximum number of lists a customer can have, -1 is unlimited
    public $max_lists = -1;
    
    // maximum number of subscribers, -1 is unlimited
    public $max_subscribers = -1 ;
    
    //maximum number of subscribers allowd into a list, -1 is unlimited
    public $max_subscribers_per_list = -1;
    
    //
    public $copy_subscribers_memory_limit;
    
    //
    public $copy_subscribers_at_once = 100;
    
    // can the customer delete his lists?
    public $can_delete_own_lists = 'yes';
    
    // can the customer delete his subscribers?
    public $can_delete_own_subscribers = 'yes';
    
    // can the customer segment lists?
    public $can_segment_lists = 'yes';
    
    // max number of segment conditions
    public $max_segment_conditions = 3;
    
    // max wait timeout for a segment to load
    public $max_segment_wait_timeout = 5;
    
    // whether is allowed to mark blacklisted emails as subscribed again
    public $can_mark_blacklisted_as_confirmed = 'no';

    // whether is allowed use own suppression list
    public $can_use_own_blacklist = 'no';
    
    public function rules()
    {
        $rules = array(
            array('can_import_subscribers, can_export_subscribers, can_copy_subscribers, max_lists, max_subscribers, max_subscribers_per_list, copy_subscribers_at_once, can_delete_own_lists, can_delete_own_subscribers, can_segment_lists, max_segment_conditions, max_segment_wait_timeout, can_mark_blacklisted_as_confirmed, can_use_own_blacklist', 'required'),
            array('can_import_subscribers, can_export_subscribers, can_copy_subscribers, can_delete_own_lists, can_delete_own_subscribers, can_segment_lists, can_use_own_blacklist', 'in', 'range' => array_keys($this->getYesNoOptions())),
            array('max_lists, max_subscribers, max_subscribers_per_list', 'numerical', 'integerOnly' => true, 'min' => -1),
            array('copy_subscribers_memory_limit', 'in', 'range' => array_keys($this->getMemoryLimitOptions())),
            array('copy_subscribers_at_once', 'numerical', 'integerOnly' => true, 'min' => 50, 'max' => 10000),
            array('max_segment_conditions, max_segment_wait_timeout', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 4000),
            array('can_mark_blacklisted_as_confirmed', 'in', 'range' => array_keys($this->getYesNoOptions())),
        );
        
        return CMap::mergeArray($rules, parent::rules());    
    }
    
    public function attributeLabels()
    {
        $labels = array(
            'can_import_subscribers'            => Yii::t('settings', 'Can import subscribers'),
            'can_export_subscribers'            => Yii::t('settings', 'Can export subscribers'),
            'can_copy_subscribers'              => Yii::t('settings', 'Can copy subscribers'),
            'max_lists'                         => Yii::t('settings', 'Max. lists'),
            'max_subscribers'                   => Yii::t('settings', 'Max. subscribers'),
            'max_subscribers_per_list'          => Yii::t('settings', 'Max. subscribers per list'),
            'copy_subscribers_memory_limit'     => Yii::t('settings', 'Copy subscribers memory limit'),
            'copy_subscribers_at_once'          => Yii::t('settings', 'Copy subscribers at once'),
            'can_delete_own_lists'              => Yii::t('settings', 'Can delete own lists'),
            'can_delete_own_subscribers'        => Yii::t('settings', 'Can delete own subscribers'),
            'can_segment_lists'                 => Yii::t('settings', 'Can segment lists'),
            'max_segment_conditions'            => Yii::t('settings', 'Max. segment conditions'),
            'max_segment_wait_timeout'          => Yii::t('settings', 'Max. segment wait timeout'),
            'can_mark_blacklisted_as_confirmed' => Yii::t('settings', 'Mark blacklisted as confirmed'),
            'can_use_own_blacklist'             => Yii::t('settings', 'Use own blacklist'),
        );
        
        return CMap::mergeArray($labels, parent::attributeLabels());    
    }
    
    public function attributePlaceholders()
    {
        $placeholders = array(
            'can_import_subscribers'        => '',
            'can_export_subscribers'        => '',
            'can_copy_subscribers'          => '',
            'max_lists'                     => '',
            'max_subscribers'               => '',
            'max_subscribers_per_list'      => '',
            'copy_subscribers_memory_limit' => '',
            'copy_subscribers_at_once'      => '',
            'max_segment_conditions'        => '',
            'max_segment_wait_timeout'      => '',
        );
        
        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
    }
    
    public function attributeHelpTexts()
    {
        $texts = array(
            'can_import_subscribers'            => Yii::t('settings', 'Whether customers are allowed to import subscribers'),
            'can_export_subscribers'            => Yii::t('settings', 'Whether customers are allowed to export subscribers'),
            'can_copy_subscribers'              => Yii::t('settings', 'Whether customers are allowed to copy subscribers from a list into another'),
            'max_lists'                         => Yii::t('settings', 'Maximum number of lists a customer can have, set to -1 for unlimited'),
            'max_subscribers'                   => Yii::t('settings', 'Maximum number of subscribers a customer can have, set to -1 for unlimited'),
            'max_subscribers_per_list'          => Yii::t('settings', 'Maximum number of subscribers per list a customer can have, set to -1 for unlimited'),
            'copy_subscribers_memory_limit'     => Yii::t('settings', 'Maximum memory the copy subscribers process is allowed to use'),
            'copy_subscribers_at_once'          => Yii::t('settings', 'How many subscribers to copy at once'),
            'can_delete_own_lists'              => Yii::t('settings', 'Whether customers are allowed to delete their own lists'),
            'can_delete_own_subscribers'        => Yii::t('settings', 'Whether customers are allowed to delete their own subscribers'),
            'can_segment_lists'                 => Yii::t('settings', 'Whether customers are allowed to segment their lists'),
            'max_segment_conditions'            => Yii::t('settings', 'Maximum number of conditions a list segment can have. This affects performance drastically, keep the number as low as possible'),
            'max_segment_wait_timeout'          => Yii::t('settings', 'Maximum number of seconds a segment is allowed to take in order to load subscribers.'),
            'can_mark_blacklisted_as_confirmed' => Yii::t('settings', 'Whether customers can mark blacklisted subscribers as confirmed. Please note that this will remove blacklisted emails from the main blacklist'),
            'can_use_own_blacklist'             => Yii::t('settings', 'Whether customers can use their own blacklist. Please note that the global blacklist has priority over the customer one.'),
        );
        
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }
}
