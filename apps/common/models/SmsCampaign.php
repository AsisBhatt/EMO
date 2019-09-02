<?php defined('MW_PATH') || exit('No direct script access allowed');
/**
 * This is the model class for table "{{sms_campaign}}".
 *
 * The followings are the available columns in table '{{sms_campaign}}':
 * @property int $sms_campaign_id
 * @property int $customer_id
 * @property string $campaign_type
 * @property string $campaign_name
 * @property string $campaign_text
 * @property string $campaign_media
 * @property int $list_id
 * @property string $send_at
 * @property string $finished_at
 * @property int $sent_record
 * @property int $not_sent_record
 * @property string $campaign_status
 * @property int $is_deleted
 * @property string $campaign_created
 */
class SmsCampaign extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{sms_campaign}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			//array('customer_id, campaign_name, campaign_type', 'required'),
			//array('customer_id, campaign_name, campaign_type, campaign_text, list_id, send_at', 'required'),
			//array('customer_id', 'required', 'on' => 'step-name, step-confirm'),
			array('customer_id, campaign_name, campaign_type', 'required', 'on' => 'step-name'),
			
			array('list_id, campaign_text', 'required', 'on' => 'step-setup'),
			
			array('campaign_text', 'length', 'max' => 160),
			
			array('send_at', 'date', 'format' => 'yyyy-mm-dd hh:mm:ss', 'on' => 'insert'),
			
			array('customer_id, campaign_name, campaign_text, campaign_media, list_id, finished_at, campaign_status, is_deleted', 'safe', 'on'=>'search'),
			
			array('campaign_created','default',
              'value'=>new CDbExpression('NOW()'),
              'setOnEmpty'=>false,'on'=>'step-name')
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
			'list'                  => array(self::BELONGS_TO, 'Lists', 'list_id'),
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
			'sms_campaign_id' => Yii::t('sms_campaign','Sms Campaign Id'),
			'customer_id' => Yii::t('sms_campaign','Customer'),
			'campaign_name' => Yii::t('sms_campaign','Campaign Name'),
			'campaign_text' => Yii::t('sms_campaign','Campaign Text'),
			'campaign_media' => Yii::t('sms_campaign','Campaign Media'),
			'list_id' => Yii::t('sms_campaign','Campaign List'),
			'send_at' => Yii::t('sms_campaign','Campaign Schedule'),
			'finished_at' => Yii::t('sms_campaign','Campaign Finish'),
			'sent_record' => Yii::t('sms_campaign','Send Count'),
			'not_sent_record' => Yii::t('sms_campaign','Not Send Count'),
			'campaign_status' => Yii::t('sms_campaign','Campaign Status'),
			'is_deleted' => Yii::t('sms_campaign','Is Deleted'),
			'campaign_created' => Yii::t('sms_campaign','Campaign Created'),
		);
		return CMap::mergeArray($labels, parent::attributeLabels());
	}
	
	public function attributePlaceholders()
    {
        $placeholders = array(
            'campaign_name' => null,
			'campaign_text' => null,
			'list_id' => null,
            'send_at' => $this->getDateTimeFormat(),
        );

        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
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

		$criteria->compare('t.sms_campaign_id',$this->sms_campaign_id,true);
		$criteria->compare('t.customer_id',$this->customer_id,true);
		$criteria->compare('t.campaign_type',$this->campaign_type,true);
		$criteria->compare('t.campaign_name',$this->campaign_name,true);
		$criteria->compare('t.campaign_text',$this->campaign_text,true);
		$criteria->compare('t.campaign_media',$this->campaign_media,true);
		$criteria->compare('t.list_id',$this->list_id,true);
		$criteria->compare('t.send_at',$this->send_at,true);
		$criteria->compare('t.sent_record',$this->sent_record,true);
		$criteria->compare('t.not_sent_record',$this->not_sent_record,true);
		$criteria->compare('t.campaign_status',$this->campaign_status,true);
		$criteria->compare('t.is_deleted',$this->is_deleted,true);
		$criteria->compare('t.campaign_created',$this->campaign_created,true);
		
		$criteria->order = 't.campaign_created ASC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'pagination' => array(
				'pageSize' => $this->paginationOptions->getPageSize(),
				'pageVar' => 'page',
			),
			'sort' => array(
				'defaultOrder' => array(
					't.campaign_created' => CSort::SORT_DESC,
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
	
	public function getsendAt()
    {
		return $this->dateTimeFormatter->formatLocalizedDateTime($this->send_at);
    }	
	
	protected function afterFind()
    {
		
        if ($this->send_at == '0000-00-00 00:00:00') {
            $this->send_at = null;
        }
		
        if (empty($this->send_at)) {
            $this->send_at = date('Y-m-d H:i:s');
        }

        parent::afterFind();
    }
	
	protected function afterConstruct()
    {
        if (empty($this->send_at)) {
            $this->send_at = date('Y-m-d H:i:s');
        }
        
		parent::afterConstruct();
    }	
	
	public function findByUid($sms_campaign_id){
		
		return $this->findByAttributes(array(
			'sms_campaign_id' => $sms_campaign_id,
		));
	}
	
	public function getDateTimeFormat()
    {
        $locale = Yii::app()->locale;
        $searchReplace = array(
            '{1}' => $locale->getDateFormat('short'),
            '{0}' => $locale->getTimeFormat('short'),
        );

        return str_replace(array_keys($searchReplace), array_values($searchReplace), $locale->getDateTimeFormat());
    }	
	
	
	public function saveSendAt($sendAt = null)
    {
        if (empty($this->sms_campaign_id)) {
            return false;
        }
        if ($sendAt) {
            $this->send_at = $sendAt;
        }
        $attributes = array('send_at' => $this->send_at);
        return Yii::app()->getDb()->createCommand()->update($this->tableName(), $attributes, 'sms_campaign_id = :sid', array(':sid' => (int)$this->sms_campaign_id));
    }
	
	public function saveSendrecord($count_rec, $count_rec_not_sent,$sms_campaign_id){
		if($count_rec == $count_rec_not_sent || $count_rec > $count_rec_not_sent){
			$sms_campaign_model = SmsCampaign::model()->findByPk((int)$sms_campaign_id);
			$sms_campaign_model->finished_at = date('Y-m-d H:i:s');
			$sms_campaign_model->sent_record = $count_rec;
			$sms_campaign_model->not_sent_record = $count_rec_not_sent;
			//$sms_campaign_model->campaign_status = 'SENT';
			$sms_campaign_model->save();
		}else if($count_rec < $count_rec_not_sent){
			$sms_campaign_model = SmsCampaign::model()->findByPk((int)$sms_campaign_id);
			$sms_campaign_model->finished_at = date('Y-m-d H:i:s');
			$sms_campaign_model->sent_record = $count_rec;
			$sms_campaign_model->not_sent_record = $count_rec_not_sent;
			//$sms_campaign_model->campaign_status = 'DRAFTS';
			$sms_campaign_model->save();
		}
		return $sms_campaign_id;
		
	}
}