<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * ListSegmentCondition
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
/**
 * This is the model class for table "list_segment_condition".
 *
 * The followings are the available columns in table 'list_segment_condition':
 * @property integer $condition_id
 * @property integer $segment_id
 * @property integer $operator_id
 * @property integer $field_id
 * @property string $value
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property ListSegmentOperator $operator
 * @property ListSegment $segment
 * @property ListField $field
 */
class ListSegmentCondition extends ActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{list_segment_condition}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            array('field_id, operator_id, value', 'required'),
            array('field_id, operator_id', 'numerical', 'integerOnly' => true),
            array('value', 'length', 'max'=>255),
        );
        
        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations = array(
            'operator'  => array(self::BELONGS_TO, 'ListSegmentOperator', 'operator_id'),
            'segment'   => array(self::BELONGS_TO, 'ListSegment', 'segment_id'),
            'field'     => array(self::BELONGS_TO, 'ListField', 'field_id'),
        );
        
        return CMap::mergeArray($relations, parent::relations());
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'condition_id'  => Yii::t('list_segments', 'Condition'),
            'segment_id'    => Yii::t('list_segments', 'Segment'),
            'operator_id'   => Yii::t('list_segments', 'Operator'),
            'field_id'      => Yii::t('list_segments', 'Field'),
            'value'         => Yii::t('list_segments', 'Value'),
        );
        
        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ListSegmentCondition the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function getOperatorsDropDownArray()
    {
        static $_options = array();
        if (!empty($_options)) {
            return $_options;
        }
        
        $operators = ListSegmentOperator::model()->findAll();
        foreach ($operators as $operator) {
            $_options[$operator->operator_id] = Yii::t('list_segments', $operator->name);
        }
        
        return $_options;
    }
    
    // since 1.3.5
    public function getParsedValue()
    {
        $tags  = self::getValueTags();
        $value = trim($this->value);
        foreach ($tags as $data) {
            $value = call_user_func_array($data['callback'], array($data, $value, $this));
        }
        return $value;
    }
    
    // since 1.3.5
    public static function getValueTags()
    {
        static $tags;
        if ($tags === null) {
            $tags = array(
                array(
                    'tag'         => '[EMPTY]',
                    'description' => Yii::t('list_segments', 'It will be transformed into an empty value'),
                    'callback'    => array(__CLASS__, '_parseEmptyValueTag')
                ),
                array(
                    'tag'         => '[DATETIME]',
                    'description' => Yii::t('list_segments', 'It will be transformed into the current date/time in the format of Y-m-d H:i:s (i.e: {datetime})', array('{datetime}' => date('Y-m-d H:i:s'))),
                    'callback'    => array(__CLASS__, '_parseDatetimeValueTag'),
                ),
                array(
                    'tag'         => '[DATE]',
                    'description' => Yii::t('list_segments', 'It will be transformed into the current date in the format of Y-m-d (i.e: {date})', array('{date}' => date('Y-m-d'))),
                    'callback'    => array(__CLASS__, '_parseDateValueTag'),
                ),
                array(
                    'tag'         => '[BIRTHDAY]',
                    'description' => Yii::t('list_segments', 'It requires the birthday custom field value to be in the format of Y-m-d (i.e: {date}) in order to work properly', array('{date}' => date('Y-m-d'))),
                    'callback'    => array(__CLASS__, '_parseBirthDateValueTag'),
                ),
            );
            $tags = (array)Yii::app()->hooks->applyFilters('list_segment_condition_value_tags', $tags);
            foreach ($tags as $index => $data) {
                if (!isset($data['tag'], $data['description'], $data['callback']) || !is_callable($data['callback'], false)) {
                    unset($tags[$index]);
                }
            }
            ksort($tags);
        }
        return $tags;
    }
    
    // since 1.3.5
    public static function _parseEmptyValueTag($data, $value, $condition)
    {
        if ($data['tag'] != $value) {
            return $value;
        }        
        return str_replace($data['tag'], '', $value);
    }
    
    // since 1.3.5
    public static function _parseDatetimeValueTag($data, $value, $condition)
    {
        if ($data['tag'] != $value) {
            return $value;
        } 
        return str_replace($data['tag'], date('Y-m-d H:i:s'), $value);   
    }
    
    // since 1.3.5
    public static function _parseDateValueTag($data, $value, $condition)
    {
        if ($data['tag'] != $value) {
            return $value;
        } 
        return str_replace($data['tag'], date('Y-m-d'), $value); 
    }
    
    // since 1.3.5
    public static function _parseBirthDateValueTag($data, $value, $condition)
    {
        if ($data['tag'] != $value) {
            return $value;
        }
        if (in_array($condition->operator->slug, array(ListSegmentOperator::IS, ListSegmentOperator::ENDS_WITH))) {
            $condition->operator->slug = ListSegmentOperator::ENDS_WITH;
            return str_replace($data['tag'], date('m-d'), $value);
        }
        if (in_array($condition->operator->slug, array(ListSegmentOperator::IS_NOT, ListSegmentOperator::NOT_ENDS_WITH))) {
            $condition->operator->slug = ListSegmentOperator::NOT_ENDS_WITH;
            return str_replace($data['tag'], date('m-d'), $value);
        }
        return $value;
    }
}
