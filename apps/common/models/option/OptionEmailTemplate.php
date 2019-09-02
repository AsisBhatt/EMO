<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * OptionEmailTemplate
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class OptionEmailTemplate extends OptionBase
{
    // settings category
    protected $_categoryName = 'system.email_templates';
    
    public $common; 

    public function rules()
    {
        $rules = array(
            array('common', 'required', 'on' => 'common'),
        );
        
        return CMap::mergeArray($rules, parent::rules());    
    }
    
    public function attributeLabels()
    {
        $labels = array(
            'common'    => Yii::t('settings', 'Common template'),
        );
        
        return CMap::mergeArray($labels, parent::attributeLabels());    
    }
    
    public function attributePlaceholders()
    {
        $placeholders = array(
            'common' => null,
        );
        
        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
    }
    
    public function attributeHelpTexts()
    {
        $texts = array(
            'common' => Yii::t('settings', 'The "common" template is used when sending notifications, password reset emails, etc.'),
        );
        
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }
    
    public function beforeValidate()
    {
        if ($this->scenario == 'common' && strpos($this->common, '[CONTENT]') === false) {
            $this->addError('common', Yii::t('settings', 'The "[CONTENT]" tag is required but it has not been found in the content.'));
        }
        return parent::beforeValidate();
    }
}
