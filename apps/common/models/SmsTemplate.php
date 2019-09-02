<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Smstemplate
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.9
 */

/**
 * This is the model class for table "customer_message".
 *
 * The followings are the available columns in table 'customer_message':
 * @property integer $template_id
 * @property integer $customer_id
 * @property string $template_sub
 * @property string $template_text
 * @property string $template_created
 * @property string $template_updated
 *
 * The followings are the available model relations:
 * @property Customer $customer
 */
class SmsTemplate extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{sms_template}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('customer_id,template_sub, template_text', 'required'),

			// The following rule is used by search().
			array('template_sub, customer_id, template_text', 'safe', 'on'=>'search'),
			
			array('template_updated','default',
              'value'=>new CDbExpression('NOW()'),
              'setOnEmpty'=>false,'on'=>'update'),
			  
			array('template_created,template_updated','default',
              'value'=>new CDbExpression('NOW()'),
              'setOnEmpty'=>false,'on'=>'insert')
		);

		return CMap::mergeArray($rules, parent::rules());
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
			'customer' => array(self::BELONGS_TO, 'Customer', 'customer_id'),
		);
		return CMap::mergeArray($relations, parent::relations());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'template_id'  => Yii::t('smstemplate', 'Template Id'),
			'customer_id'  => Yii::t('smstemplate', 'Customer Id'),
			'template_sub'  => Yii::t('smstemplate', 'Subject'),
			'template_text'  => Yii::t('smstemplate', 'Template'),
			'template_created'  => Yii::t('smstemplate', 'Created'),
			'template_updated'  => Yii::t('smstemplate', 'Updated'),
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
		
		$criteria->compare('t.customer_id', $this->customer_id, true);
		$criteria->compare('t.template_sub', $this->template_sub, true);
		$criteria->compare('t.template_text', $this->template_text, true);

		$criteria->order = 't.template_id DESC';

		return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize' => $this->paginationOptions->getPageSize(),
                'pageVar'  => 'page',
            ),
            'sort'=>array(
                'defaultOrder' => array(
                    't.template_id' => CSort::SORT_DESC,
                ),
            ),
        ));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CustomerMessage the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	protected function beforeSave()
    {
        if (!parent::beforeSave()) {
            return false;
        }

        if ($this->isNewRecord) {
           // $this->message_uid = $this->generateUid();
        }

        return true;
    }

	public function findByUid($template_id)
    {
        return $this->findByAttributes(array(
            'template_id' => $template_id,
        ));
    }

}