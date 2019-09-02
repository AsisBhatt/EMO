<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This is the model class for table "{{autoreply_template}}".
 *
 * The followings are the available columns in table '{{autoreply_template}}':
 * @property int $auto_temp_id
 * @property int $customer_id
 * @property string $auto_temp_type
 * @property string $auto_temp_text
 * @property string $auto_temp_status
 * @property string $auto_temp_date_added
 */
class AutoreplyTemplate extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{autoreply_template}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('auto_temp_text, auto_temp_status, auto_temp_date_added', 'required'),
			
			array('customer_id, auto_temp_type, auto_temp_text, auto_temp_status, auto_temp_date_added', 'safe', 'on'=>'search'),
			//
			/*array('auto_temp_date_added','default',
              'value'=>new CDbExpression('NOW()'),
              'setOnEmpty'=>false,'on'=>'create')*/
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
			'auto_temp_id' => Yii::t('autoreply_template','Auto Reply Id'),
			'customer_id' => Yii::t('autoreply_template','Customer'),
			'auto_temp_type' => Yii::t('autoreply_template','Template Type'),
			'auto_temp_text' => Yii::t('autoreply_template','Auto Text'),
			'auto_temp_status' => Yii::t('autoreply_template','Auto Status'),
			'auto_temp_date_added' => Yii::t('autoreply_template','Created'),
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
		
		$criteria->compare('t.auto_temp_id',$this->auto_temp_id,true);
		$criteria->compare('t.customer_id',$this->customer_id,true);
		$criteria->compare('t.auto_temp_type',$this->auto_temp_type,true);
		$criteria->compare('t.auto_temp_text',$this->auto_temp_text,true);
		$criteria->compare('t.auto_temp_status',$this->auto_temp_status,true);
		$criteria->compare('t.auto_temp_date_added',$this->auto_temp_date_added,true);
		
		$criteria->order = 't.auto_temp_date_added DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => $this->paginationOptions->getPageSize(),
				'pageVar' => 'page',
			),
			'sort' => array(
				'defaultOrder' => array(
					't.auto_temp_date_added' => CSort::SORT_DESC,
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
	
	public function findByUid($auto_temp_id){
		
		return $this->findByAttributes(array(
			'auto_temp_id' => $auto_temp_id,
		));
	}
}
