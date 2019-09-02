<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This is the model class for table "{{beelift_number}}".
 *
 * The followings are the available columns in table '{{beelift_number}}':
 * @property int $log_id
 * @property string $log_discription
 * @property int $log_table_id
 * @property int $log_table_name_id
 * @property string $log_operation_type
 * @property int $user_login_id
 * @property int $log_ip_address
 * @property string $created_at
 */
class ActivityLogs extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{activity_logs}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('log_discription, log_table_id, log_table_name_id, log_operation_type, user_login_id, log_ip_address, created_at', 'required'),
			
			array('log_discription, log_table_id, log_table_name_id, log_operation_type, user_login_id, log_ip_address, created_at', 'safe', 'on'=>'search'),
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
			'customer'              => array(self::BELONGS_TO, 'Customer', 'user_login_id'),
		);
		return CMap::mergeArray($relations, parent::relations());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'log_id' => Yii::t('activity_logs','Log Id'),
			'log_discription' => Yii::t('activity_logs','Log Description'),
			'log_table_id' => Yii::t('activity_logs','Log Table Id'),
			'log_table_name_id' => Yii::t('activity_logs','Log Table name Id'),
			'log_operation_type' => Yii::t('activity_logs','Log Operation type'),
			'user_login_id' => Yii::t('activity_logs','Customer'),
			'log_ip_address' => Yii::t('activity_logs','Ip Address'),
			'created_at' => Yii::t('activity_logs','Created at'),
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
		
		$criteria->compare('t.log_id',$this->log_id,true);
		$criteria->compare('t.log_discription',$this->log_discription,true);
		$criteria->compare('t.log_table_id',$this->log_table_id,true);
		$criteria->compare('t.log_table_name_id',$this->log_table_name_id,true);
		$criteria->compare('t.log_operation_type',$this->log_operation_type,true);
		//$criteria->compare('t.user_login_id',$this->user_login_id,true);
		if (!empty($this->user_login_id)) {
            if (is_numeric($this->user_login_id)) {
                $criteria->compare('t.user_login_id', $this->user_login_id);
            } else {
                $criteria->with['customer'] = array(
                    'condition' => 'customer.email LIKE :name OR customer.first_name LIKE :name OR customer.last_name LIKE :name',
                    'params'    => array(':name' => '%' . $this->user_login_id . '%')
                );
            }
        }
		$criteria->compare('t.log_ip_address',$this->log_ip_address,true);
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
	
	public function findByUid($log_id){
		
		return $this->findByAttributes(array(
			'log_id' => $log_id,
		));
	}
	
	public function getTablenameInInt($table_name = null, $table_id = null){
		$table_name = ($table_name == 'list' ? 'lists' : $table_name);
		
		$tables_name = array(
			'logins' => 1,
			'activity_logs' => 2,
			'article' => 3,
			'article_category' => 4,
			'article_to_category' => 5,
			'autoreply_template' => 6,
			'beelift_number' => 7,
			'bounce_server' => 8,
			'campaign' => 9,
			'campaign_abuse_report' => 10,
			'campaign_attachment' => 11,
			'campaign_bounce_log' => 12,
			'campaign_delivery_log' => 13,
			'campaign_delivery_log_archive' => 14,
			'campaign_forward_friend' => 15,
			'campaign_group' => 16,
			'campaign_open_action_list_field' => 17,
			'campaign_open_action_subscriber' => 18,
			'campaign_option' => 19,
			'campaign_template' => 20,
			'campaign_template_url_action_list_field' => 21,
			'campaign_template_url_action_subscriber' => 22,
			'campaign_temporary_source' => 23,
			'campaign_to_delivery_server' => 24,
			'campaign_track_open' => 25,
			'campaign_track_unsubscribe' => 26,
			'campaign_track_url' => 27,
			'campaign_url' => 28,
			'company_type' => 29,
			'country' => 30,
			'cst_sms_grp' => 31,
			'currency' => 32,
			'customer' => 33,
			'customer_action_log' => 34,
			'customer_api_key' => 35,
			'customer_auto_login_token' => 36,
			'customer_campaign_tag' => 37,
			'customer_company' => 38,
			'customer_email_blacklist' => 39,
			'customer_email_template' => 40,
			'customer_group' => 41,
			'customer_group_option' => 42,
			'customer_login_log' => 43,
			'customer_message' => 44,
			'customer_password_reset' => 45,
			'customer_quota_mark' => 46,
			'delivery_server' => 47,
			'delivery_server_domain_policy' => 48,
			'delivery_server_to_customer_group' => 49,
			'delivery_server_usage_log' => 50,
			'email_blacklist' => 51,
			'email_blacklist_monitor' => 52,
			'feedback_loop_server' => 53,
			'guest_fail_attempt' => 54,
			'image_gallery' => 55,
			'ip_location' => 56,
			'language' => 57,
			'links' => 58,
			'lists' => 59,
			'list_company' => 60,
			'list_customer_notification' => 61,
			'list_default' => 62,
			'list_field' => 63,
			'list_field_option' => 64,
			'list_field_type' => 65,
			'list_field_value' => 66,
			'list_form_custom_asset' => 67,
			'list_form_custom_redirect' => 68,
			'list_form_custom_webhook' => 69,
			'list_page' => 70,
			'list_page_type' => 71,
			'list_segment' => 72,
			'list_segment_condition' => 73,
			'list_segment_operator' => 74,
			'list_subscriber' => 75,
			'list_subscriber_action' => 76,
			'list_subscriber_field_cache' => 77,
			'list_subscriber_list_move' => 78,
			'option' => 79,
			'plan' => 80,
			'price_plan' => 81,
			'price_plan_order' => 82,
			'price_plan_order_note' => 83,
			'price_plan_order_transaction' => 84,
			'price_plan_promo_code' => 85,
			'sending_domain' => 86,
			'session' => 87,
			'sms' => 88,
			'smssetting' => 89,
			'sms_campaign' => 90,
			'sms_campaign_quota' => 91,
			'sms_rply' => 92,
			'sms_template' => 93,
			'socialmedia_api' => 94,
			'socialpost2' => 95,
			'socialsetting' => 96,
			'tag_registry' => 97,
			'tax' => 98,
			'text_reply' => 99,
			'tracking_domain' => 100,
			'transactional_email' => 101,
			'transactional_email_log' => 102,
			'user' => 103,
			'user_auto_login_token' => 104,
			'user_group' => 105,
			'user_group_route_access' => 106,
			'user_password_reset' => 107,
			'zone' => 108,
		);
		if($table_name != ''){
			return $tables_name[$table_name];
		}else if($table_id != ''){
			$tableName = array_search($table_id, $tables_name);
			return ucwords(str_replace("_", " ", $tableName));
		}
		
	}
}
