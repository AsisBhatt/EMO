<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CampaignEmailTemplateUpload
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.2
 */
 
/**
 * This class is a trick to make use of the {@link EmailTemplateUploadBehavior}
 * so that we can parse a uploaded zip file directly from the campaign without writing all the logic again
 */
class CampaignEmailTemplateUpload extends CustomerEmailTemplate
{
    // hold the zip file
    public $archive;
    
    // populate with the running campaign
    public $campaign;
    
    public $auto_plain_text = self::TEXT_YES;
    
    public function rules()
    {
        $rules = array(
            array('auto_plain_text', 'in', 'range' => array_keys($this->getAutoPlainTextArray())),
        );
        
        return CMap::mergeArray($rules, parent::rules());
    }
    
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CustomerEmailTemplate the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeSave()
    {
        return true;
    }
    
    public function save($runValidation=true, $attributes=null)
    {
        return true;
    }
    
    public function generateUid()
    {
        return 'cmp' . $this->campaign->campaign_uid;
    }
    
    public function getAutoPlainTextArray()
    {
        return array(
            self::TEXT_YES  => Yii::t('app', 'Yes'),
            self::TEXT_NO   => Yii::t('app', 'No'),
        );
    }
    
    public function attributeHelpTexts()
    {
        $texts = array(
            'auto_plain_text'   => 'Whether the plain text version of the html template should be auto generated.',
        );
        
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }

}
