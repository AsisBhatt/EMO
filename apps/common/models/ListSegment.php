<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * ListSegment
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

/**
 * This is the model class for table "list_segment".
 *
 * The followings are the available columns in table 'list_segment':
 * @property integer $segment_id
 * @property string $segment_uid
 * @property integer $list_id
 * @property string $name
 * @property string $operator_match
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property Campaign[] $campaigns
 * @property List $list
 * @property ListSegmentCondition[] $segmentConditions
 */
class ListSegment extends ActiveRecord
{
    const OPERATOR_MATCH_ANY = 'any';

    const OPERATOR_MATCH_ALL = 'all';

    private $_fieldConditions;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{list_segment}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            array('name, operator_match', 'required'),

            array('name', 'length', 'max'=>255),
            array('operator_match', 'in', 'range'=>array_keys($this->getOperatorMatchArray())),
        );

        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations = array(
            'campaigns'        => array(self::HAS_MANY, 'Campaign', 'segment_id'),
            'list'             => array(self::BELONGS_TO, 'Lists', 'list_id'),
            'segmentConditions'=> array(self::HAS_MANY, 'ListSegmentCondition', 'segment_id'),
        );

        return CMap::mergeArray($relations, parent::relations());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'segment_id'        => Yii::t('list_segments', 'Segment'),
            'list_id'           => Yii::t('list_segments', 'List'),
            'name'              => Yii::t('list_segments', 'Name'),
            'operator_match'    => Yii::t('list_segments', 'Operator match'),
            'subscribers_count' => Yii::t('list_segments', 'Subscribers count'),
        );

        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->compare('list_id', (int)$this->list_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize'  => $this->paginationOptions->getPageSize(),
                'pageVar'   => 'page',
            ),
            'sort'=>array(
                'defaultOrder' => array(
                    'name'    => CSort::SORT_ASC,
                ),
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ListSegment the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function findAllByListId($listId)
    {
        $criteria = new CDbCriteria();
        $criteria->compare('list_id', (int)$listId);
        $criteria->order = 'name ASC';
        return $this->findAll($criteria);
    }

    public function getOperatorMatchArray()
    {
        return array(
            self::OPERATOR_MATCH_ANY => Yii::t('list_segments', self::OPERATOR_MATCH_ANY),
            self::OPERATOR_MATCH_ALL => Yii::t('list_segments', self::OPERATOR_MATCH_ALL),
        );
    }

    public function getFieldsDropDownArray()
    {
        static $_options = array();
        if (isset($_options[$this->list_id])) {
            return $_options[$this->list_id];
        }

        if (empty($this->list_id)) {
            return array();
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'field_id, label';
        $criteria->compare('list_id', $this->list_id);
        $criteria->order = 'sort_order ASC';
        $fields = ListField::model()->findAll($criteria);

        $options = array();

        foreach ($fields as $field) {
            $options[$field->field_id] = $field->label;
        }

        return $_options[$this->list_id] = $options;
    }

    public function countSubscribers($extraCriteria = null)
    {
        $criteria = $this->_createCountFindSubscribersCriteria();
        $this->_appendCountFindSubscribersCriteria($criteria);

        // this is here so that we can hook when sending the campaign.
        if (!empty($extraCriteria) && $extraCriteria instanceof CDbCriteria) {
            $criteria->mergeWith($extraCriteria);
        }

        // since 1.3.4.9
        $criteria->select = 'COUNT(DISTINCT t.subscriber_id) as counter';
        $criteria->group  = '';

        return ListSubscriber::model()->count($criteria);
    }

    public function findSubscribers($offset = 0, $limit = 10, $extraCriteria = null)
    {
        $criteria = $this->_createCountFindSubscribersCriteria();
        $this->_appendCountFindSubscribersCriteria($criteria);

        // this is here so that we can hook when sending the campaign.
        if (!empty($extraCriteria) && $extraCriteria instanceof CDbCriteria) {
            $criteria->mergeWith($extraCriteria);
        }

        $criteria->offset = (int)$offset;
        $criteria->limit  = (int)$limit;
        return ListSubscriber::model()->findAll($criteria);
    }

    protected function _createCountFindSubscribersCriteria()
    {
        $segmentConditions = ListSegmentCondition::model()->findAllByAttributes(array(
            'segment_id' => (int)$this->segment_id,
        ));

        $criteria = new CDbCriteria();
        $criteria->select = 't.subscriber_id, t.subscriber_uid, t.email';
        $criteria->compare('t.list_id', $this->list_id);
        $criteria->compare('t.status', ListSubscriber::STATUS_CONFIRMED);
        $criteria->group = 't.subscriber_id';
        $criteria->order = 't.subscriber_id DESC';

        $fieldConditions = array();
        foreach ($segmentConditions as $segmentCondition) {
            if (!isset($fieldConditions[$segmentCondition->field_id])) {
                $fieldConditions[$segmentCondition->field_id] = array();
            }
            $fieldConditions[$segmentCondition->field_id][] = $segmentCondition;
        }
        
        $subscriber = ListSubscriber::model();
        $md = $subscriber->getMetaData();
        foreach ($fieldConditions as $field_id => $conditions) {
            if ($md->hasRelation('fieldValues'.$field_id)) {
                continue;
            }
            $md->addRelation('fieldValues'.$field_id, array(ListSubscriber::HAS_MANY, 'ListFieldValue', 'subscriber_id'));
        }
        $this->_fieldConditions = $fieldConditions;

        unset($segmentConditions, $fieldConditions);
        return $criteria;
    }

    protected function _appendCountFindSubscribersCriteria(CDbCriteria $criteria)
    {
        $fieldConditions = $this->_fieldConditions;

        $with                       = array();
        $params                     = array();
        $appendCriteriaCondition    = array();

        foreach ($fieldConditions as $field_id => $conditions) {
            $with['fieldValues'.$field_id] = array(
                    'select'    => false,
                    'together'  => true,
                    'joinType'  => 'LEFT JOIN',
            );

            $conditionString = '(`fieldValues'.$field_id.'`.`field_id` = :field_id'.$field_id.' AND (%s) )';
            $injectCondition = array();

            $params[':field_id'.$field_id] = $field_id;

            // note: since 1.3.4.7, added the is_numeric() and is_float() checks and values casting if needed
            foreach ($conditions as $idx => $condition) {
                $index = $field_id + $idx;
                $value = $condition->getParsedValue();

                if ($condition->operator->slug === ListSegmentOperator::IS) {
                    if (is_numeric($value)) {
                        if (is_float($value)) {
                            $injectCondition[] = 'CAST(`fieldValues'.$field_id.'`.`value` AS DECIMAL) = :value'.$index;
                            $params[':value'.$index] = (float)$value;
                        } else {
                            $injectCondition[] = 'CAST(`fieldValues'.$field_id.'`.`value` AS UNSIGNED) = :value'.$index;
                            $params[':value'.$index] = (int)$value;
                        }
                    } else {
                        $injectCondition[] = '`fieldValues'.$field_id.'`.`value` = :value'.$index;
                        $params[':value'.$index] = $value;
                    }
                    continue;
                }

                if ($condition->operator->slug === ListSegmentOperator::IS_NOT) {
                    if (is_numeric($value)) {
                        if (is_float($value)) {
                            $injectCondition[] =  'CAST(`fieldValues'.$field_id.'`.`value` AS DECIMAL) != :value'.$index;
                            $params[':value'.$index] = (float)$value;
                        } else {
                            $injectCondition[] =  'CAST(`fieldValues'.$field_id.'`.`value` AS UNSIGNED) != :value'.$index;
                            $params[':value'.$index] = (int)$value;
                        }
                    } else {
                        $injectCondition[] =  '`fieldValues'.$field_id.'`.`value` != :value'.$index;
                        $params[':value'.$index] = $value;
                    }
                    continue;
                }

                if ($condition->operator->slug === ListSegmentOperator::CONTAINS) {
                    $injectCondition[] =  '`fieldValues'.$field_id.'`.`value` LIKE :value'.$index;
                    $params[':value'.$index] = '%'.$value.'%';
                    continue;
                }

                if ($condition->operator->slug === ListSegmentOperator::NOT_CONTAINS) {
                    $injectCondition[] =  '`fieldValues'.$field_id.'`.`value` NOT LIKE :value'.$index;
                    $params[':value'.$index] = '%'.$value.'%';
                    continue;
                }

                if ($condition->operator->slug === ListSegmentOperator::STARTS_WITH) {
                    $injectCondition[] =  '`fieldValues'.$field_id.'`.`value` LIKE :value'.$index;
                    $params[':value'.$index] = $value.'%';
                    continue;
                }

                if ($condition->operator->slug === ListSegmentOperator::NOT_STARTS_WITH) {
                    $injectCondition[] =  '`fieldValues'.$field_id.'`.`value` NOT LIKE :value'.$index;
                    $params[':value'.$index] = $value.'%';
                    continue;
                }

                if ($condition->operator->slug === ListSegmentOperator::ENDS_WITH) {
                    $injectCondition[] =  '`fieldValues'.$field_id.'`.`value` LIKE :value'.$index;
                    $params[':value'.$index] = '%'.$value;
                    continue;
                }

                if ($condition->operator->slug === ListSegmentOperator::NOT_ENDS_WITH) {
                    $injectCondition[] =  '`fieldValues'.$field_id.'`.`value` NOT LIKE :value'.$index;
                    $params[':value'.$index] = '%'.$value;
                    continue;
                }

                if ($condition->operator->slug === ListSegmentOperator::GREATER) {
                    if (is_numeric($value)) {
                        if (is_float($value)) {
                            $injectCondition[] =  'CAST(`fieldValues'.$field_id.'`.`value` AS DECIMAL) > :value'.$index;
                            $params[':value'.$index] = (float)$value;
                        } else {
                            $injectCondition[] =  'CAST(`fieldValues'.$field_id.'`.`value` AS UNSIGNED) > :value'.$index;
                            $params[':value'.$index] = (int)$value;
                        }
                    } else {
                        $injectCondition[] =  '`fieldValues'.$field_id.'`.`value` > :value'.$index;
                        $params[':value'.$index] = $value;
                    }
                    continue;
                }

                if ($condition->operator->slug === ListSegmentOperator::LESS) {
                    if (is_numeric($value)) {
                        if (is_float($value)) {
                            $injectCondition[] =  'CAST(`fieldValues'.$field_id.'`.`value` AS DECIMAL) < :value'.$index;
                            $params[':value'.$index] = (float)$value;
                        } else {
                            $injectCondition[] =  'CAST(`fieldValues'.$field_id.'`.`value` AS UNSIGNED) < :value'.$index;
                            $params[':value'.$index] = (int)$value;
                        }
                    } else {
                        $injectCondition[] =  '`fieldValues'.$field_id.'`.`value` < :value'.$index;
                        $params[':value'.$index] = $value;
                    }
                    continue;
                }
            }

            if (!empty($injectCondition)) {
                if ($this->operator_match === ListSegment::OPERATOR_MATCH_ANY) {
                    $injectCondition = implode(' OR ', $injectCondition);
                } else {
                    $injectCondition = implode(' AND ', $injectCondition);
                }
                $appendCriteriaCondition[] = sprintf($conditionString, $injectCondition);
            }
        }

        if (!empty($appendCriteriaCondition)) {
            $criteria->params = array_merge($criteria->params, $params);
            if ($this->operator_match === ListSegment::OPERATOR_MATCH_ANY) {
                $appendCondition = ' AND ' . '( '. implode(' OR ', $appendCriteriaCondition) .' )';
            } else {
                $appendCondition = ' AND ' . implode(' AND ', $appendCriteriaCondition);
            }

            $criteria->with = $with;
            $criteria->condition .= $appendCondition;
        } else {
            // add a condition to return nothing as a result
            $criteria->compare('t.subscriber_id', -1);
        }
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord || empty($this->segment_uid)) {
            $this->segment_uid = $this->generateUid();
        }

        return parent::beforeSave();
    }

    public function findByUid($segment_uid)
    {
        return $this->findByAttributes(array(
            'segment_uid' => $segment_uid,
        ));
    }

    public function generateUid()
    {
        $unique = StringHelper::uniqid();
        $exists = $this->findByUid($unique);

        if (!empty($exists)) {
            return $this->generateUid();
        }

        return $unique;
    }

    public function getUid()
    {
        return $this->segment_uid;
    }

    public function copy()
    {
        $copied = false;

        if ($this->isNewRecord) {
            return $copied;
        }

        $transaction = Yii::app()->db->beginTransaction();

        try {
            $segment = clone $this;
            $segment->isNewRecord  = true;
            $segment->segment_id   = null;
            $segment->segment_uid  = $this->generateUid();
            $segment->date_added   = new CDbExpression('NOW()');
            $segment->last_updated = new CDbExpression('NOW()');

            if (preg_match('/\#(\d+)$/', $segment->name, $matches)) {
                $counter = (int)$matches[1];
                $counter++;
                $segment->name = preg_replace('/\#(\d+)$/', '#' . $counter, $segment->name);
            } else {
                $segment->name .= ' #1';
            }

            if (!$segment->save(false)) {
                throw new CException($segment->shortErrors->getAllAsString());
            }

            $conditions = !empty($this->segmentConditions) ? $this->segmentConditions : array();
            foreach ($conditions as $condition) {
                $condition = clone $condition;
                $condition->isNewRecord  = true;
                $condition->condition_id = null;
                $condition->segment_id   = $segment->segment_id;
                $condition->date_added   = new CDbExpression('NOW()');
                $condition->last_updated = new CDbExpression('NOW()');
                $condition->save(false);
            }

            $transaction->commit();
            $copied = $segment;
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        return $copied;
    }
}
