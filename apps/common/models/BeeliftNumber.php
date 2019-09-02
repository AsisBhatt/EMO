<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This is the model class for table "{{beelift_number}}".
 *
 * The followings are the available columns in table '{{beelift_number}}':
 * @property int $number_id
 * @property int $customer_id
 * @property string $number
 * @property string $status
 * @property string $created_at
 */
class BeeliftNumber extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{beelift_number}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('customer_id, number, status, created_at', 'required'),
			
			array('customer_id, number, status, created_at', 'safe', 'on'=>'search'),
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
			'number_id' => Yii::t('beelift_number','Number Id'),
			'customer_id' => Yii::t('beelift_number','Customer'),
			'number' => Yii::t('beelift_number','Beelift Number'),
			'status' => Yii::t('beelift_number','Status'),
			'created_at' => Yii::t('beelift_number','Created'),
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
		
		$criteria->compare('t.number_id',$this->number_id,true);
		$criteria->compare('t.customer_id',$this->customer_id,true);
		$criteria->compare('t.number',$this->number,true);
		$criteria->compare('t.status',$this->status,true);
		$criteria->compare('t.created_at',$this->created_at,true);
		
		$criteria->order = 't.created_at DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => $this->paginationOptions->getPageSize(),
				'pageVar' => 'page',
			),
			'sort' => array(
				'defaultOrder' => array(
					't.created_at' => CSort::SORT_DESC,
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
	
	public function findByUid($number_id){
		
		return $this->findByAttributes(array(
			'number_id' => $number_id,
		));
	}
}
