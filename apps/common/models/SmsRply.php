<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This is the model class for table "{{sms_rply}}".
 *
 * The followings are the available columns in table '{{sms_rply}}':
 * @property string $sms_rply_id
 * @property string $customer_id
 * @property string $sms_rply_time
 * @property string $sms_rply_direction
 * @property string $sms_rply_to_number
 * @property string $sms_rply_from_number
 * @property string $sms_rply_cost
 * @property string $sms_rply_body
 * @property string $sms_rply_created
 */
class SmsRply extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{sms_rply}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('sms_rply_id, customer_id, sms_rply_direction, sms_rply_to_number, sms_rply_from_number, sms_rply_cost, sms_rply_body', 'required'),
			
			array('sms_rply_time, sms_rply_direction, sms_rply_to_number, sms_rply_from_number, sms_rply_body, sms_rply_cost', 'safe', 'on'=>'search'),
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
			'sms_rply_id' => Yii::t('sms_rply','Sms Rply Id'),
			'customer_id' => Yii::t('sms_rply','Customer Id'),
			'sms_rply_time' => Yii::t('sms_rply','Time'),
			'sms_rply_direction' => Yii::t('sms_rply','Direction'),
			'sms_rply_to_number' => Yii::t('sms_rply','To Number'),
			'sms_rply_from_number' => Yii::t('sms_rply','From Number'),
			'sms_rply_cost' => Yii::t('sms_rply','Cost'),
			'sms_rply_body' => Yii::t('sms_rply','Body'),
			'sms_rply_created' => Yii::t('sms_rply','Created'),
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

		$criteria->compare('t.sms_rply_id',$this->sms_rply_id,true);
		$criteria->compare('t.customer_id',$this->customer_id,true);
		$criteria->compare('t.sms_rply_time',$this->sms_rply_time,true);
		$criteria->compare('t.sms_rply_direction',$this->sms_rply_direction,true);
		$criteria->compare('t.sms_rply_to_number',$this->sms_rply_to_number,true);
		$criteria->compare('t.sms_rply_from_number',$this->sms_rply_from_number,true);
		$criteria->compare('t.sms_rply_cost',$this->sms_rply_cost,true);
		$criteria->compare('t.sms_rply_body',$this->sms_rply_body,true);
		$criteria->compare('t.sms_rply_created',$this->sms_rply_created,true);
		$criteria->addCondition('t.sms_rply_direction = "inbound"');
		
		$criteria->order = 't.sms_rply_created DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => $this->paginationOptions->getPageSize(),
				'pageVar' => 'page',
			),
			'sort' => array(
				'defaultOrder' => array(
					't.sms_rply_created' => CSort::SORT_DESC,
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
	
	public function findByUid($sms_rply_id){
		
		return $this->findByAttributes(array(
			'sms_rply_id' => $sms_rply_id,
		));
	}
	
	public function getStatus($sms_rply_from_number){
		$customer_id = Yii::app()->customer->getId();
		//$sms_rply_from_number = 15612123370;
		
		$status_array = Yii::app()->db->createCommand("SELECT lisu.status FROM uic_list_field_value lival, uic_list li, uic_list_subscriber lisu WHERE lival.subscriber_id = lisu.subscriber_id  AND li.list_id = lisu.list_id AND li.customer_id = '".$customer_id."' AND lival.value = '".$sms_rply_from_number."' GROUP BY lival.subscriber_id")->queryRow();
		
		//var_dump($status_array);exit;
		//$status_array['status'] == '' || 
		$status = '';
		if($status_array['status'] == 'confirmed'){
			$status = 'confirmed';
		}else if($status_array['status'] == 'stop'){
			$status = 'stop';
		}
		return $status;
		// ['status']);
		// return $status;
		// print_r($status_array);exit;
	}
}
