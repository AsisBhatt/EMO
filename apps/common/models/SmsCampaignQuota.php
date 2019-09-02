<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This is the model class for table "{{beelift_number}}".
 *
 * The followings are the available columns in table '{{beelift_number}}':
 * @property int $quota_id
 * @property int $customer_id
 * @property int $sms_campaign_id
 * @property int $reserve_quota
 * @property int $remaining_quota
 * @property int $total_quota
 * @property int $quota_send
 * @property int $quota_not_send
 * @property string $quota_status
 * @property string $quota_created
 */
class SmsCampaignQuota extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{sms_campaign_quota}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('customer_id, sms_campaign_id, reserve_quota, remaining_quota, total_quota, quota_status, quota_created', 'required'),
			
			array('customer_id, reserve_quota, total_quota, remaining_quota, quota_send, quota_not_send', 'safe', 'on'=>'search'),
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
			'sms_campaign'          => array(self::BELONGS_TO, 'Sms Campaign', 'sms_campaign_id'),
		);
		return CMap::mergeArray($relations, parent::relations());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'quota_id' => Yii::t('campaign_quota','Quota Id'),
			'customer_id' => Yii::t('campaign_quota','Customer'),
			'sms_campaign_id' => Yii::t('campaign_quota','Campaign Name'),
			'reserve_quota' => Yii::t('campaign_quota','Resrve Quota'),
			'remaining_quota' => Yii::t('campaign_quota','Remaining Quota'),
			'total_quota' => Yii::t('campaign_quota','Total Quota'),
			'quota_send' => Yii::t('campaign_quota','Send Count'),
			'quota_not_send' => Yii::t('campaign_quota','Not Send Count'),
			'quota_status' => Yii::t('campaign_quota','Status'),
			'quota_created' => Yii::t('campaign_quota','Date Added'),
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
		
		$criteria->compare('t.quota_id',$this->quota_id,true);
		$criteria->compare('t.customer_id',$this->customer_id,true);
		$criteria->compare('t.sms_campaign_id',$this->sms_campaign_id,true);
		$criteria->compare('t.reserve_quota',$this->reserve_quota,true);
		$criteria->compare('t.remaining_quota',$this->remaining_quota,true);
		$criteria->compare('t.total_quota',$this->total_quota,true);
		$criteria->compare('t.quota_send',$this->quota_send,true);
		$criteria->compare('t.quota_not_send',$this->quota_not_send,true);
		$criteria->compare('t.quota_status',$this->quota_status,true);
		$criteria->compare('t.quota_created',$this->quota_created,true);
		
		$criteria->order = 't.quota_created DESC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => $this->paginationOptions->getPageSize(),
				'pageVar' => 'page',
			),
			'sort' => array(
				'defaultOrder' => array(
					't.quota_created' => CSort::SORT_DESC,
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
	
	public function findByUid($quota_id){
		
		return $this->findByAttributes(array(
			'quota_id' => $quota_id,
		));
	}
	
	public function updateSendrecord($sms_campaign_id,$count_rec, $count_rec_not_send){
		$sms_campaign_quota = SmsCampaignQuota::model()->findByAttributes(array('sms_campaign_id' => $sms_campaign_id,'quota_status' => 'RESERVE'));
		
		$remaining_quota = $sms_campaign_quota->remaining_quota;
		// $remaining_quota = ($remaining_quota + $count_rec_not_send);
		
		if(isset($count_rec) && isset($count_rec_not_send)){
			
			if($count_rec_not_send > 0){
				$quota_array = Yii::app()->db->createCommand("SELECT * FROM uic_sms_campaign_quota WHERE quota_status ='RESERVE'")->queryAll();
				
				foreach($quota_array as $quotakey => $quota)
				{
					//echo "UPDATE uic_sms_campaign_quota SET remaining_quota = '".($quota['remaining_quota'] + $count_rec_not_send)."' WHERE quota_id='".$quota['quota_id']."'";
					$quota_update = Yii::app()->db->createCommand("UPDATE uic_sms_campaign_quota SET remaining_quota = '".($quota['remaining_quota'] + $count_rec_not_send)."' WHERE quota_id='".$quota['quota_id']."'")->execute();
				}
			}
			//print_r($sms_campaign_quota);exit;
			$sms_campaign_quota->reserve_quota = $count_rec;
			$sms_campaign_quota->remaining_quota = ($remaining_quota + $count_rec_not_send);
			$sms_campaign_quota->quota_send = $count_rec;
			$sms_campaign_quota->quota_not_send = $count_rec_not_send;
			$sms_campaign_quota->quota_status = 'COMPLETE';
			$sms_campaign_quota->save();
			
			return true;
		}
		
		return false;
		
	}
}
