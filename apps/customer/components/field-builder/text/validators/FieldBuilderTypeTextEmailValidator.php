<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * FieldBuilderTypeTextEmailValidator
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class FieldBuilderTypeTextEmailValidator extends CValidator
{
    public $field;

    public $subscriber;

    protected function validateAttribute($object, $attribute)
    {
        // extract the attribute value from it's model object
        $value = $object->$attribute;
        $field = $this->field;
		
        $blacklisted = EmailBlacklist::isBlacklisted($value, $this->subscriber);
		//print_r($blacklisted);exit;
        if (!empty($blacklisted)) {
            // temp flag since 1.3.5.9
            Yii::app()->params['validationSubscriberAlreadyExists'] = true;
            $this->addError($object, $attribute, Yii::t('list_fields', 'This email address is blacklisted!'));
            return;
        }

        $criteria = new CDbCriteria();
        $criteria->compare('email', $value);
        $criteria->compare('list_id', (int)$this->subscriber->list_id);
        $criteria->addCondition('subscriber_id != :sid');
        $criteria->params[':sid'] = (int)$this->subscriber->subscriber_id;

        $subscriberExists = ListSubscriber::model()->find($criteria);
		
        /*if (!empty($subscriberExists)) {
			
            if ($subscriberExists->status == ListSubscriber::STATUS_UNSUBSCRIBED) {
                ListFieldValue::model()->deleteAll('subscriber_id = :sid', array(':sid' => $subscriberExists->subscriber_id));
                return;
            }
            // temp flag since 1.3.5.9
            Yii::app()->params['validationSubscriberAlreadyExists'] = true;
            $this->addError($object, $attribute, Yii::t('list_fields', 'This email address is already registered in this list!'));
            return;
        }*/

        $criteria = new CDbCriteria();
        $criteria->select = 't.field_id';
        $criteria->compare('t.list_id', (int)$field->list_id);
        $criteria->compare('t.type_id', (int)$field->type_id);
        $criteria->compare('t.field_id', (int)$field->field_id);
        $criteria->compare('t.tag', 'EMAIL');

        $criteria->with = array(
            'value' => array(
                'select'    => false,
                'joinType'  => 'INNER JOIN',
                'together'  => true,
                'condition' => '`value`.`subscriber_id` != :sid AND `value`.`value` = :val',
                'params'    => array(
                    ':sid'  => (int)$this->subscriber->subscriber_id,
                    ':val'  => $value
                )
        ));

        $model = ListField::model()->find($criteria);

        if (empty($model)) {
            return;
        }

        // temp flag since 1.3.5.9
        /*Yii::app()->params['validationSubscriberAlreadyExists'] = true;
        $this->addError($object, $attribute, Yii::t('list_fields', 'This email address is already registered in this list!'));*/
    }
}
