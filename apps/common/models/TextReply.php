<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This is the model class for table "{{autoreply_template}}".
 *
 * The followings are the available columns in table '{{autoreply_template}}':
 * @property int $text_id
 * @property string $text_mdr_id
 * @property int $customer_id
 * @property string $text_body
 * @property string $text_from_number
 * @property string $text_to_number
 * @property string $text_status
 * @property string $text_created
 */
class TextReply extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{text_reply}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('text_mdr_id, text_body, text_from_number, text_to_number, text_status, text_created', 'required'),
			
			array('text_mdr_id, customer_id, text_body, text_from_number, text_to_number, text_status, text_created', 'safe', 'on'=>'search'),
			
			array('text_created','default',
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
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		$relations = array(
			'customer'              => array(self::BELONGS_TO, 'Customer', 'customer_id'),
		);
		return CMap::mergeArray($relations, parent::relations());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'text_id' => Yii::t('text_reply','Text Id'),
			'text_id' => Yii::t('text_reply','Mdr Id'),
			'customer_id' => Yii::t('text_reply','Customer'),
			'text_body' => Yii::t('text_reply','Message'),
			'text_from_number' => Yii::t('text_reply','From Number'),
			'text_to_number' => Yii::t('text_reply','To Number'),
			'text_status' => Yii::t('text_reply','Text Status'),
			'text_created' => Yii::t('text_reply','Created'),
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
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;
		
		$criteria->compare('t.text_id',$this->text_id,true);
		$criteria->compare('t.text_mdr_id',$this->text_mdr_id,true);
		$criteria->compare('t.customer_id',$this->customer_id,true);
		$criteria->compare('t.text_body',$this->text_body,true);		
		$criteria->compare('t.text_from_number',$this->text_from_number,true);
		$criteria->compare('t.text_to_number',$this->text_to_number,true);
		$criteria->compare('t.text_status',$this->text_status,true);
		$criteria->compare('t.text_created',$this->text_created,true);
		
		$criteria->order = 't.text_created DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => $this->paginationOptions->getPageSize(),
				'pageVar' => 'page',
			),
			'sort' => array(
				'defaultOrder' => array(
					't.text_created' => CSort::SORT_DESC,
				),
			),
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return SmsRply the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function beforeSave(){
		if(!parent::beforeSave()){
			return false;
		}
		
		if($this->isNewRecord){
			//$this->sms_rply_id = $this->generateUid();
		}
		
		return true;
	}
	
	public function findByUid($text_id){
		
		return $this->findByAttributes(array(
			'text_id' => $text_id,
		));
	}
}
