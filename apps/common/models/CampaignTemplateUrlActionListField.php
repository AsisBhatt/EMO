<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CampaignTemplateUrlActionListField
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.5
 */
 
/**
 * This is the model class for table "{{campaign_template_url_action_list_field}}".
 *
 * The followings are the available columns in table '{{campaign_template_url_action_list_field}}':
 * @property string $url_id
 * @property integer $campaign_id
 * @property integer $template_id
 * @property integer $list_id
 * @property integer $field_id
 * @property string $field_value
 * @property string $url
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property Campaign $campaign
 * @property List $list
 * @property CampaignTemplate $template
 * @property ListField $field
 */
class CampaignTemplateUrlActionListField extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{campaign_template_url_action_list_field}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('field_id, field_value, url', 'required'),
			array('field_id', 'numerical', 'integerOnly'=>true),
            array('field_id', 'exist', 'className' => 'ListField'),
            array('field_value', 'length', 'max'=>255),
            array('url', 'url'),
		);
        return CMap::mergeArray($rules, parent::rules());
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
			'campaign'   => array(self::BELONGS_TO, 'Campaign', 'campaign_id'),
			'list'       => array(self::BELONGS_TO, 'List', 'list_id'),
			'template'   => array(self::BELONGS_TO, 'CampaignTemplate', 'template_id'),
			'field'      => array(self::BELONGS_TO, 'ListField', 'field_id'),
		);
        return CMap::mergeArray($relations, parent::relations());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'url_id'         => Yii::t('campaigns', 'Url'),
			'campaign_id'    => Yii::t('campaigns', 'Campaign'),
			'template_id'    => Yii::t('campaigns', 'Template'),
			'list_id'        => Yii::t('campaigns', 'List'),
			'field_id'       => Yii::t('campaigns', 'Field'),
			'field_value'    => Yii::t('campaigns', 'Field value'),
			'url'            => Yii::t('campaigns', 'Url'),
		);
        return CMap::mergeArray($labels, parent::attributeLabels());
	}
    
    public function attributeHelpTexts()
    {
        $texts = array(
            'url'         => Yii::t('campaigns', 'Trigger the selected action when the subscriber will access this url'),
            'field_id'    => Yii::t('campaigns', 'Which field to change when the subscriber opens the campaign. Please note that only text fields are usable and email, first name and last name are not used'),
			'field_value' => Yii::t('campaigns', 'The value that the custom field should get after the subscriber opens the campaign'),
        );
        
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }
    
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CampaignTemplateUrlActionListField the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	protected function beforeSave()
	{
		$this->url = StringHelper::normalizeUrl($this->url);
		return parent::beforeSave();
	}
	
    public function getTextFieldsAsDropDownOptions()
    {
        $this->list_id  = (int)$this->list_id;
        static $options = array();
        if (isset($options[$this->list_id])) {
            return $options[$this->list_id];
        }
        $textField = ListFieldType::model()->findByAttributes(array('identifier' => 'text'));
        if (empty($textField)) {
            return $options[$this->list_id] = array();
        }
        $options[$this->list_id] = array();
        $criteria = new CDbCriteria();
        $criteria->select = 'field_id, label';
        $criteria->compare('list_id', $this->list_id);
        $criteria->compare('type_id', $textField->type_id);
        $criteria->addNotInCondition('tag', array('EMAIL', 'FNAME', 'LNAME'));
        $models = ListField::model()->findAll($criteria);
        foreach ($models as $model) {
            $options[$this->list_id][$model->field_id] = $model->label;
        }
        return $options[$this->list_id];
    }
}
