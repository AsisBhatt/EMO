<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CustomerMessage
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
 * @property integer $sms_id
 * @property integer $customer_id
 * @property string $subscriber_uid
 * @property string $mobile
 * @property string $message
 * @property string $response
 * @property string $status
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property Customer $customer
 */
class Sms extends ActiveRecord
{
	const STATUS_UNSEEN = 'notsent';

	//const STATUS_SEEN = 'sent';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{sms}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('mobile, message , response', 'required'),

			// The following rule is used by search().
			array('customer_id, mobile, message, response,status, date_added', 'safe', 'on'=>'search'),
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
			'list_subscriber' => array(self::BELONGS_TO, 'ListSubscriber', 'subscriber_uid'),
		);
		return CMap::mergeArray($relations, parent::relations());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'sms_id'  => Yii::t('sms', 'SmsId'),
			'customer_id' => Yii::t('sms', 'Customer'),
			'mobile' => Yii::t('sms', 'Mobile'),
			'message'		  => Yii::t('sms', 'Message'),
			'response' 	  => Yii::t('sms', 'Response'),
			'status' 	  => Yii::t('sms', 'Status'),
			'date_added' 	  => Yii::t('sms', 'Date'),
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

		$criteria->compare('t.customer_id', (int)$this->customer_id);
		$criteria->compare('t.mobile', $this->mobile, true);
		$criteria->compare('t.message', $this->message, true);
		$criteria->compare('t.response', $this->response, true);
		$criteria->compare('t.status', $this->status, true);
		$criteria->compare('t.date_added', $this->date_added, true);

		$criteria->order = 't.sms_id DESC';

		return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize' => $this->paginationOptions->getPageSize(),
                'pageVar'  => 'page',
            ),
            'sort'=>array(
                'defaultOrder' => array(
                    't.sms_id' => CSort::SORT_DESC,
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

	public function findByUid($message_uid)
    {
        return $this->findByAttributes(array(
            'message_uid' => $message_uid,
        ));
    }
	
	public function sendSms($mobile, $message)
	{
		if(isset($mobile) && isset($message)){
		
			$customer = Yii::app()->customer->getModel();
			$sid = 2;
			$smssetting = Smssetting::model()->findByPk((int)$sid);
			
			$sender = $customer->company->flowroute_sms_num;
			$data_array = array('to' => $mobile, 'from' => $sender, 'body' => $message);
			
			$data_string = json_encode($data_array);
			$ch = curl_init(trim($smssetting->apiurl));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_USERPWD, trim($smssetting->access_key) . ":" . trim($smssetting->authkey));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json; charset=utf-8',
				'Content-Length: ' . strlen($data_string))
			);                                                                                                        
			$result = curl_exec($ch);
			$sms_obj = json_decode($result);
			
			$sms_id = '';
			$msg_status = '';
			
			if(isset($sms_obj->data) && $sms_obj->data != ''){
				$sms_id  = $sms_obj->data->id;
				$msg_status .= 'sent';
			}else if(isset($sms_obj->errors)){
				$sms_id = $sms_obj->errors[0]->id;
				$msg_status .= $sms_obj->errors[0]->detail;
			}
			
			if($sms_id){
				$sms = new Sms();
				$sms->customer_id = $customer->customer_id;
				$sms->mobile = $mobile;
				$sms->message = $message;
				$sms->response = $sms_id;
				$sms->status = $msg_status;
				$sms->save();
			}
			return $msg_status;
		}
	}
	
	public function sendMms($mobile, $media_url){
		
	}
	
	public function shceduleSms($mobile, $message, $customer_id)
	{
		if(isset($mobile) && isset($message)){
		
			//$customer = Yii::app()->customer->getModel();
			$sid = 2;
			$smssetting = Smssetting::model()->findByPk((int)$sid);
			
			//$sender = $customer->company->flowroute_sms_num;
			$customer_company = CustomerCompany::model()->findByAttributes(array('customer_id'=> $customer_id));
			$data_array = array('to' => $mobile, 'from' => $customer_company->flowroute_sms_num, 'body' => $message);
			
			$data_string = json_encode($data_array);
			$ch = curl_init(trim($smssetting->apiurl));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_USERPWD, trim($smssetting->access_key) . ":" . trim($smssetting->authkey));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json; charset=utf-8',
				'Content-Length: ' . strlen($data_string))
			);                                                                                                        
			$result = curl_exec($ch);
			$sms_obj = json_decode($result);
			
			$sms_id = '';
			$msg_status = '';
			
			if(isset($sms_obj->data) && $sms_obj->data != ''){
				$sms_id  = $sms_obj->data->id;
				$msg_status .= 'sent';
			}else if(isset($sms_obj->errors)){
				$sms_id = $sms_obj->errors[0]->id;
				$msg_status .= $sms_obj->errors[0]->detail;
			}
			
			if($sms_id){
				$sms = new Sms();
				$sms->customer_id = $customer_id;
				$sms->mobile = $mobile;
				$sms->message = $message;
				$sms->response = $sms_id;
				$sms->status = $msg_status;
				$sms->save();
			}
			return $msg_status;
		}
	}
	
	public function sendbulksms($mobile, $message)
	{
		echo $mobile;exit;
		if(isset($mobile) && isset($message)){
			$customer   = Yii::app()->customer->getModel();
			$sid = 2;
			$smssetting = Smssetting::model()->findByPk((int)$sid);
			
			$sender = $customer->company->flowroute_sms_num;
			$data_array = array('to' => $mobile, 'from' => $sender, 'body' => $message);
			
			$data_string = json_encode($data_array);
			$ch = curl_init(trim($smssetting->apiurl));
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_USERPWD, trim($smssetting->access_key) . ":" . trim($smssetting->authkey));
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json; charset=utf-8',
				'Content-Length: ' . strlen($data_string))
			);
			$result = curl_exec($ch);
			try {
				$mdr_record = $controller->getMessageLookup($mdr_id); // 'mdr1-b334f89df8de4f8fa7ce377e06090a2e'
				print_r($mdr_record);
			} catch(\FlowrouteMessagingLib\APIException $e) {
				print("Error - " . strval($e->getResponseCode()) . ' ' . $e->getMessage());
			}
			
			try {
				echo 'a';exit;
				$sms_obj = json_decode($result);
				if(isset($sms_obj->data) && $sms_obj->data != ''){
					$sms_id  = $sms_obj->data->id;
					$msg_status = 'sent';
				}
				if($sms_id){
					$sms = new Sms();
					$sms->customer_id = $customer->customer_id;
					$sms->mobile = $mobile;
					$sms->message = $message;
					$sms->response = $sms_id;
					$sms->status = $msg_status;
					$sms->save();
				}
				return $msg_status;
			} catch(Exception  $e) {
				echo 'false'.$e;exit;
			}
			/*$sms_id = '';
			$msg_status = '';
			
			if(isset($sms_obj->data) && $sms_obj->data != ''){
				$sms_id  = $sms_obj->data->id;
				$msg_status = 'sent';
			}else if(isset($sms_obj->errors)){
				$sms_id = $sms_obj->errors[0]->id;
				$msg_status = $sms_obj->errors[0]->detail;
			}
			
			
			if($sms_id){
				$sms = new Sms();
				$sms->customer_id = $customer->customer_id;
				$sms->mobile = $mobile;
				$sms->message = $message;
				$sms->response = $sms_id;
				$sms->status = $msg_status;
				$sms->save();
			}
			return $msg_status;*/
		}
	}


	public function getStatusesList()
    {
        return array(
            self::STATUS_UNSEEN => Yii::t('messages', 'Notsent'),
        );
    }

}