<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Customer
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

/**
 * This is the model class for table "customer".
 *
 * The followings are the available columns in table 'customer':
 * @property integer $customer_id
 * @property string $customer_uid
 * @property integer $group_id
 * @property integer $plan_id
 * @property integer $language_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property string $mobile
 * @property string $phone
 * @property string $cs_number
 * @property string $gmail_email
 * @property string $timezone
 * @property string $avatar
 * @property string $avatar_1
 * @property string $removable
 * @property string $confirmation_key
 * @property integer $oauth_uid
 * @property string $oauth_provider
 * @property string $status
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property BounceServer[] $bounceServers
 * @property Campaign[] $campaigns
 * @property CustomerCampaignTag[] $campaignTags
 * @property CustomerMessage[] $messages
 * @property CampaignGroup[] $campaignGroups
 * @property CustomerGroup $group
 * @property CustomerApiKey[] $apiKeys
 * @property CustomerCompany $company
 * @property CustomerAutoLoginToken[] $autoLoginTokens
 * @property CustomerEmailTemplate[] $emailTemplates
 * @property CustomerActionLog[] $actionLogs
 * @property CustomerQuotaMark[] $quotaMarks
 * @property DeliveryServer[] $deliveryServers
 * @property FeedbackLoopServer[] $fblServers
 * @property Language $language
 * @property DeliveryServerUsageLog[] $usageLogs
 * @property Lists[] $lists
 * @property PricePlanOrder[] $pricePlanOrders
 * @property PricePlanOrderNote[] $pricePlanOrderNotes
 * @property TrackingDomain[] $trackingDomains
 * @property SendingDomain[] $sendingDomains
 * @property TransactionalEmail[] $transactionalEmails
 * @property CustomerEmailBlacklist[] $blacklistedEmails
 */
class Customer extends ActiveRecord
{
    const TEXT_NO = 'no';

    const TEXT_YES = 'yes';

    const STATUS_PENDING_CONFIRM = 'pending-confirm';

    const STATUS_PENDING_ACTIVE = 'pending-active';

    protected $_lastQuotaMark;

    // see getIsOverQuota()
    protected $_lastQuotaCheckTime = 0;

    // see getIsOverQuota()
    protected $_lastQuotaCheckTimeDiff = 30;

    // see getIsOverQuota()
    protected $_lastQuotaCheckMaxDiffCounter = 500;

    // see getIsOverQuota()
    protected $_lastQuotaCheckTimeOverQuota = false;

    public $fake_password;

    public $confirm_password;

    //public $confirm_email;

    public $tc_agree;

    public $sending_quota_usage;

    public $company_name;

    public $new_avatar;
	
	public $new_avatar_1;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $avatarMimes = null;
        if (CommonHelper::functionExists('finfo_open')) {
            $avatarMimes = Yii::app()->extensionMimes->get(array('png', 'jpg', 'jpeg', 'bmp', 'gif'))->toArray();
        }

        $rules = array(
            // when new customer is created by a user.
            array('first_name, last_name, mobile, email, fake_password, confirm_password, timezone, status, mobile, phone, cs_number', 'required', 'on' => 'insert'),
            // when a customer is updated by a user
            array('first_name, last_name, email, mobile, timezone, status', 'required', 'on' => 'update'),
            // when a customer updates his profile
            array('first_name, last_name, email,  mobile, timezone', 'required', 'on' => 'update-profile'),
            // when a customer registers
            array('first_name, last_name, mobile, email, fake_password, confirm_password, timezone, tc_agree', 'required', 'on' => 'register'),

            array('group_id', 'numerical', 'integerOnly' => true),
            array('group_id', 'exist', 'className' => 'CustomerGroup'),
			array('plan_id', 'numerical', 'integerOnly' => true),
			array('plan_id', 'exist', 'className' => 'PricePlan'),
            array('language_id', 'numerical', 'integerOnly' => true),
            array('language_id', 'exist', 'className' => 'Language'),
            array('first_name, last_name', 'length', 'min' => 1, 'max' => 100),
            array('email', 'length', 'min' => 4, 'max' => 100),
            array('email', 'email', 'validateIDN' => true),
            array('timezone', 'in', 'range' => array_keys(DateTimeHelper::getTimeZones())),
            array('fake_password, confirm_password', 'length', 'min' => 6, 'max' => 100),
            array('confirm_password', 'compare', 'compareAttribute' => 'fake_password'),
            //array('confirm_email', 'compare', 'compareAttribute' => 'email'),
            array('email', 'unique'),
			array('mobile, phone, cs_number', 'numerical', 'integerOnly' => true),
			array('gmail_email', 'unique'),

            // avatar
            array('new_avatar', 'file', 'types' => array('png', 'jpg', 'jpeg', 'bmp', 'gif'), 'mimeTypes' => $avatarMimes, 'allowEmpty' => true),
			
			// avatar
            array('new_avatar_1', 'file', 'types' => array('png', 'jpg', 'jpeg', 'bmp', 'gif'), 'mimeTypes' => $avatarMimes, 'allowEmpty' => true),

            // unsafe
            array('group_id, status', 'unsafe', 'on' => 'update-profile, register'),
			array('plan_id, status', 'unsafe', 'on' => 'update-profile, register'),

            // mark them as safe for search
            array('first_name, last_name, email, group_id, plan_id, status, company_name', 'safe', 'on' => 'search'),
        );

        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations = array(
            'bounceServers'         => array(self::HAS_MANY, 'BounceServer', 'customer_id'),
            'campaigns'             => array(self::HAS_MANY, 'Campaign', 'customer_id'),
            'campaignGroups'        => array(self::HAS_MANY, 'CampaignGroup', 'customer_id'),
            'campaignTags'          => array(self::HAS_MANY, 'CustomerCampaignTags', 'customer_id'),
            'messages'              => array(self::HAS_MANY, 'CustomerMessage', 'customer_id'),
            'group'                 => array(self::BELONGS_TO, 'CustomerGroup', 'group_id'),
			'plan'                  => array(self::BELONGS_TO, 'PricePlan', 'plan_id'),
            'apiKeys'               => array(self::HAS_MANY, 'CustomerApiKey', 'customer_id'),
            'company'               => array(self::HAS_ONE, 'CustomerCompany', 'customer_id'),
            'autoLoginTokens'       => array(self::HAS_MANY, 'CustomerAutoLoginToken', 'customer_id'),
            'emailTemplates'        => array(self::HAS_MANY, 'CustomerEmailTemplate', 'customer_id'),
            'actionLogs'            => array(self::HAS_MANY, 'CustomerActionLog', 'customer_id'),
            'quotaMarks'            => array(self::HAS_MANY, 'CustomerQuotaMark', 'customer_id'),
            'deliveryServers'       => array(self::HAS_MANY, 'DeliveryServer', 'customer_id'),
            'fblServers'            => array(self::HAS_MANY, 'FeedbackLoopServer', 'customer_id'),
            'language'              => array(self::BELONGS_TO, 'Language', 'language_id'),
            'usageLogs'             => array(self::HAS_MANY, 'DeliveryServerUsageLog', 'customer_id'),
            'lists'                 => array(self::HAS_MANY, 'Lists', 'customer_id'),
            'pricePlanOrders'       => array(self::HAS_MANY, 'PricePlanOrder', 'customer_id'),
            'pricePlanOrderNotes'   => array(self::HAS_MANY, 'PricePlanOrderNote', 'customer_id'),
            'trackingDomains'       => array(self::HAS_MANY, 'TrackingDomain', 'customer_id'),
            'sendingDomains'        => array(self::HAS_MANY, 'SendingDomain', 'customer_id'),
            'transactionalEmails'   => array(self::HAS_MANY, 'TransactionalEmail', 'customer_id'),
            'blacklistedEmails'     => array(self::HAS_MANY, 'CustomerEmailBlacklist', 'customer_id'),
        );

        return CMap::mergeArray($relations, parent::relations());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'customer_id'   => Yii::t('customers', 'Customer'),
            'group_id'      => Yii::t('customers', 'Billing Pricing Group'),
			'plan_id'       => Yii::t('customers', 'Billing Pricing Plan'),
            'language_id'   => Yii::t('customers', 'Language'),
            'first_name'    => Yii::t('customers', 'First name'),
            'last_name'     => Yii::t('customers', 'Last name'),
            'email'         => Yii::t('customers', 'Email'),
            'password'      => Yii::t('customers', 'Password'),
			'mobile'      	=> Yii::t('customers', 'Mobile Cell Number'),
            'phone'      	=> Yii::t('customers', 'Phone number'),
			'cs_number'     => Yii::t('customers', 'CS Number'),
			'gmail_email'   => Yii::t('customers', 'Gmail'),
			'timezone'      => Yii::t('customers', 'Timezone'),
            'avatar'        => Yii::t('customers', 'Avatar'),
            'new_avatar'    => Yii::t('customers', 'New avatar'),
			'new_avatar_1'  => Yii::t('customers', 'Your Company Logo'),
            'removable'     => Yii::t('customers', 'Removable'),

            //'confirm_email'         => Yii::t('customers', 'Confirm email'),
            'fake_password'         => Yii::t('customers', 'Password'),
            'confirm_password'      => Yii::t('customers', 'Confirm password'),
            'tc_agree'              => Yii::t('customers', 'Terms and conditions'),
            'sending_quota_usage'   => Yii::t('customers', 'Sending quota usage'),
            'company_name'          => Yii::t('customers', 'Company'),
        );

        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    /**
    * Retrieves a list of models based on the current search/filter conditions.
    * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
    */
    public function search()
    {
        $criteria=new CDbCriteria;

        $criteria->compare('t.first_name', $this->first_name, true);
        $criteria->compare('t.last_name', $this->last_name, true);
        $criteria->compare('t.email', $this->email, true);
        $criteria->compare('t.group_id', $this->group_id);
		$criteria->compare('t.plan_id', $this->plan_id);
        $criteria->compare('t.status', $this->status);

        if ($this->company_name) {
            $criteria->with['company'] = array(
                'together' => true,
                'joinType' => 'INNER JOIN',
            );
            $criteria->compare('company.name', $this->company_name, true);
        }

        $criteria->order = 't.customer_id DESC';

        return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize' => $this->paginationOptions->getPageSize(),
                'pageVar'  => 'page',
            ),
            'sort'=>array(
                'defaultOrder'     => array(
                    't.customer_id'  => CSort::SORT_DESC,
                ),
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Customer the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function afterValidate()
    {
        parent::afterValidate();
        $this->handleUploadedAvatar();
		$this->handleUploadedAvatar1();
    }

    protected function beforeSave()
    {
        if (!parent::beforeSave()) {
            return false;
        }

        if ($this->isNewRecord) {
            $this->customer_uid = $this->generateUid();
        }

        if (!empty($this->fake_password)) {
            $this->password = Yii::app()->passwordHasher->hash($this->fake_password);
        }

        if ($this->removable === self::TEXT_NO) {
            $this->status = self::STATUS_ACTIVE;
        }

        if (empty($this->confirmation_key)) {
            $this->confirmation_key = sha1($this->customer_uid . StringHelper::uniqid());
        }

        if (empty($this->timezone)) {
            $this->timezone = 'UTC';
        }

        return true;
    }

    protected function afterFind()
    {
        parent::afterFind();
    }

    protected function afterSave()
    {
        parent::afterSave();
    }

    protected function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        return $this->removable === self::TEXT_YES;
    }

    protected function afterDelete()
    {
        if (!empty($this->customer_uid)) {
            // clean customer files, if any.
            $storagePath = Yii::getPathOfAlias('root.frontend.files.customer');
            $customerFiles = $storagePath.'/'.$this->customer_uid;
            if (file_exists($customerFiles) && is_dir($customerFiles)) {
                FileSystemHelper::deleteDirectoryContents($customerFiles, true, 1);
            }
        }

        parent::afterDelete();
    }

    public function getFullName()
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name.' '.$this->last_name;
        }
        return $this->email;
    }

    public function getStatusesArray()
    {
        return array(
            self::STATUS_ACTIVE          => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE        => Yii::t('app', 'Inactive'),
            self::STATUS_PENDING_CONFIRM => Yii::t('app', 'Pending confirm'),
            self::STATUS_PENDING_ACTIVE  => Yii::t('app', 'Pending active'),
        );
    }

    public function getTimeZonesArray()
    {
        //return DateTimeHelper::getTimeZones();
		return DateTimeHelper::getStaticTimeZones();
		
    }

    public function findByUid($customer_uid)
    {
        return $this->findByAttributes(array(
            'customer_uid' => $customer_uid,
        ));
    }

    public function generateUid()
    {
        $unique = StringHelper::uniqid();
        $exists = $this->findByUid($unique);

        if (!empty($exists)) {
            return $this->generateUid();
        }

        return $unique;
    }

    public function getUid()
    {
        return $this->customer_uid;
    }

    public function getAvailableDeliveryServers()
    {
        static $deliveryServers;
        if ($deliveryServers !== null) {
            return $deliveryServers;
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'server_id, hostname, name';
        $criteria->compare('customer_id', (int)$this->customer_id);
        $criteria->addInCondition('status', array(DeliveryServer::STATUS_ACTIVE, DeliveryServer::STATUS_IN_USE));
        // since 1.3.5
        $criteria->addInCondition('use_for', array(DeliveryServer::USE_FOR_ALL, DeliveryServer::USE_FOR_CAMPAIGNS));
        //
        $deliveryServers = DeliveryServer::model()->findAll($criteria);

        if (empty($deliveryServers) && !empty($this->group_id)) {
            $groupServerIds = array();
            $groupServers = DeliveryServerToCustomerGroup::model()->findAllByAttributes(array('group_id' => $this->group_id));
            foreach ($groupServers as $group) {
                $groupServerIds[] = (int)$group->server_id;
            }

            if (!empty($groupServerIds)) {
                $criteria = new CDbCriteria();
                $criteria->select = 'server_id, hostname, name';
                $criteria->addInCondition('server_id', $groupServerIds);
                $criteria->addCondition('customer_id IS NULL');
                $criteria->addInCondition('status', array(DeliveryServer::STATUS_ACTIVE, DeliveryServer::STATUS_IN_USE));
                // since 1.3.5
                $criteria->addInCondition('use_for', array(DeliveryServer::USE_FOR_ALL, DeliveryServer::USE_FOR_CAMPAIGNS));
                //
                $deliveryServers = DeliveryServer::model()->findAll($criteria);
            }
        }

        if (empty($deliveryServers) && $this->getGroupOption('servers.can_send_from_system_servers', 'yes') == 'yes') {
            $criteria = new CDbCriteria();
            $criteria->select = 'server_id, hostname, name';
            $criteria->addCondition('customer_id IS NULL');
            $criteria->addInCondition('status', array(DeliveryServer::STATUS_ACTIVE, DeliveryServer::STATUS_IN_USE));
            // since 1.3.5
            $criteria->addInCondition('use_for', array(DeliveryServer::USE_FOR_ALL, DeliveryServer::USE_FOR_CAMPAIGNS));
            //
            $deliveryServers = DeliveryServer::model()->findAll($criteria);
        }

        return $deliveryServers;
    }

    /** to be removed **/
    public function countHourlyUsage()
    {
        return 0;
    }

    /** to be removed **/
    public function getCanHaveHourlyQuota()
    {
        return false;
    }

    public function getSendingQuotaUsageDisplay()
    {
        $formatter = Yii::app()->format;
        $_allowed  = (int)$this->getGroupOption('sending.quota', -1);
        $_count    = $this->countUsageFromQuotaMark();
        $allowed   = !$_allowed ? 0 : ($_allowed == -1 ? '&infin;' : $formatter->formatNumber($_allowed));
        $count     = $formatter->formatNumber($_count);
        $percent   = ($_allowed < 1 ? 0 : ($_count > $_allowed ? 100 : round(($_count / $_allowed) * 100, 2)));

        return sprintf('%s (%s/%s)', $percent . '%', $count, $allowed);
    }

    public function resetSendingQuota()
    {
        CustomerQuotaMark::model()->deleteAllByAttributes(array('customer_id' => (int)$this->customer_id));
        return $this;
    }

    public function getIsOverQuota()
    {
        if ($this->isNewRecord) {
            return false;
        }

        // since 1.3.5.5
        if (MW_PERF_LVL && MW_PERF_LVL & MW_PERF_LVL_DISABLE_CUSTOMER_QUOTA_CHECK) {
            return false;
        }

        $timeNow = time();
        if ($this->_lastQuotaCheckTime > 0 && ($this->_lastQuotaCheckTime + $this->_lastQuotaCheckTimeDiff) > $timeNow) {
            return $this->_lastQuotaCheckTimeOverQuota;
        }
        $this->_lastQuotaCheckTime = $timeNow;

        $quota     = (int)$this->getGroupOption('sending.quota', -1);
        $timeValue = (int)$this->getGroupOption('sending.quota_time_value', -1);

        if ($quota == 0 || $timeValue == 0) {
            $this->_lastQuotaCheckTime += $timeNow;
            return $this->_lastQuotaCheckTimeOverQuota = true;
        }

        if ($quota == -1 && $timeValue == -1) {
            $this->_lastQuotaCheckTime += $timeNow;
            return $this->_lastQuotaCheckTimeOverQuota = false;
        }

        $timestamp = 0;
        if ($timeValue > 0) {
            $timeUnit  = $this->getGroupOption('sending.quota_time_unit', 'month');
            $seconds   = strtotime(sprintf('+ %d %s', $timeValue, ($timeValue == 1 ? $timeUnit : $timeUnit . 's')), $timeNow) - $timeNow;
            $timestamp = strtotime($this->getLastQuotaMark()->date_added) + $seconds;

            if ($timeNow >= $timestamp) {
                $this->_takeQuotaAction();
                // SINCE 1.3.5.9
                if ($this->getGroupOption('sending.action_quota_reached') == 'reset') {
                    return $this->_lastQuotaCheckTimeOverQuota = false;
                }
                //
                return $this->_lastQuotaCheckTimeOverQuota = true; // keep an eye on it
            }
        }

        if ($quota == -1) {
            $this->_lastQuotaCheckTime += $timeNow;
            return $this->_lastQuotaCheckTimeOverQuota = false;
        }

        $currentUsage = $this->countUsageFromQuotaMark();

        if ($currentUsage >= $quota) {
            // force waiting till end of ts
            if ($this->getGroupOption('sending.quota_wait_expire', 'yes') == 'yes' && $timeNow <= $timestamp) {
                $this->_lastQuotaCheckTime += $timeNow;
                return $this->_lastQuotaCheckTimeOverQuota = true;
            }
            $this->_takeQuotaAction();
            return $this->_lastQuotaCheckTimeOverQuota = true;
        }

        if (($quota - $currentUsage) > $this->_lastQuotaCheckMaxDiffCounter) {
            $this->_lastQuotaCheckTime += $timeNow;
            return $this->_lastQuotaCheckTimeOverQuota = false;
        }

        return $this->_lastQuotaCheckTimeOverQuota = false;
    }
	
	public function getIsSmsOverQuota()
	{
		if ($this->isNewRecord) {
			return false;
		}
		
		// since 1.3.5.5
		if (MW_PERF_LVL && MW_PERF_LVL & MW_PERF_LVL_DISABLE_CUSTOMER_QUOTA_CHECK) {
			return false;
		}
		
		$timeNow = time();
		
		if ($this->_lastQuotaCheckTime > 0 && ($this->_lastQuotaCheckTime + $this->_lastQuotaCheckTimeDiff) > $timeNow) {
			return $this->_lastQuotaCheckTimeOverQuota;
		}
		$this->_lastQuotaCheckTime = $timeNow;
		
		$quota     = (int)$this->getGroupOption('smssending.sms_quota', -1);
		$timeValue = (int)$this->getGroupOption('sending.quota_time_value', -1);
		
		if ($quota == 0 || $timeValue == 0) {
			$this->_lastQuotaCheckTime += $timeNow;
			return $this->_lastQuotaCheckTimeOverQuota = true;
		}
		
		if ($quota == -1 && $timeValue == -1) {
			$this->_lastQuotaCheckTime += $timeNow;
			return $this->_lastQuotaCheckTimeOverQuota = false;
		}
		
		$timestamp = 0;
		if ($timeValue > 0) {
			$timeUnit  = $this->getGroupOption('sending.quota_time_unit', 'month');
			$seconds   = strtotime(sprintf('+ %d %s', $timeValue, ($timeValue == 1 ? $timeUnit : $timeUnit . 's')), $timeNow) - $timeNow;
			$timestamp = strtotime($this->getLastQuotaMark()->date_added) + $seconds;

			if ($timeNow >= $timestamp) {
				$this->_takeQuotaAction();
				// SINCE 1.3.5.9
				if ($this->getGroupOption('sending.action_quota_reached') == 'reset') {
					return $this->_lastQuotaCheckTimeOverQuota = false;
				}
				return $this->_lastQuotaCheckTimeOverQuota = true; // keep an eye on it
			}
		}
		
		if ($quota == -1) {
			$this->_lastQuotaCheckTime += $timeNow;
			return $this->_lastQuotaCheckTimeOverQuota = false;
		}
		
		$smscurrentUsage = $this->countUsageSmsFromQuotaMark();

		if ($smscurrentUsage >= $quota) {
			// force waiting till end of ts
			if ($this->getGroupOption('sending.quota_wait_expire', 'yes') == 'yes' && $timeNow <= $timestamp) {
				$this->_lastQuotaCheckTime += $timeNow;
				return $this->_lastQuotaCheckTimeOverQuota = true;
			}
			$this->_takeQuotaAction();
			return $this->_lastQuotaCheckTimeOverQuota = true;
		}
		
		if (($quota - $smscurrentUsage) > $this->_lastQuotaCheckMaxDiffCounter) {
			$this->_lastQuotaCheckTime += $timeNow;
			return $this->_lastQuotaCheckTimeOverQuota = false;
		}
		return $this->_lastQuotaCheckTimeOverQuota = false;
	}

    public function countUsageFromQuotaMark(CustomerQuotaMark $quotaMark = null)
    {
        if ($quotaMark === null || empty($quotaMark->date_added)) {
            $quotaMark = $this->getLastQuotaMark();
        }

        $criteria = new CDbCriteria();
        $criteria->compare('customer_id', (int)$this->customer_id);
        $criteria->compare('customer_countable', self::TEXT_YES);
        $criteria->addCondition('`date_added` >= :startDateTime');
        $criteria->params[':startDateTime'] = $quotaMark->date_added;
		
        return DeliveryServerUsageLog::model()->count($criteria);
    }
	
	public function countUsageSmsFromQuotaMark(CustomerQuotaMark $quotaMark = null)
	{
		if($quotaMark === null || empty($quotaMark->date_added)){
			$quotaMark = $this->getLastQuotaMark();
		}
		
		$count_sms = Yii::app()->db->createCommand("SELECT * FROM uic_sms WHERE customer_id = '".(int)$this->customer_id."' AND date_added >= '".$quotaMark->date_added."'")->queryAll();
		return count($count_sms);
		
		/*$criteria = new CDbCriteria();
		$criteria->compare('customer_id', (int)$this->customer_id);
		//$criteria->compare('delivery_status', 1);
		$criteria->compare('status', 'Sent');
		$criteria->addCondition('`date_added` >= '.$quotaMark->date_added);
		//$criteria->params[':startDateTime'] = $quotaMark->date_added;*/
		
		//$count = Sms::model()->count($criteria);
		//return Sms::model()->count($criteria);
	}

    public function getLastQuotaMark()
    {
		
        if ($this->_lastQuotaMark !== null) {
            return $this->_lastQuotaMark;
        }

        $criteria = new CDbCriteria();
        $criteria->compare('customer_id', (int)$this->customer_id);
        $criteria->order = 'mark_id DESC';
        $criteria->limit = 1;
        $quotaMark = CustomerQuotaMark::model()->find($criteria);

        if (empty($quotaMark)) {
            $quotaMark = $this->createQuotaMark(false);
        }
        return $this->_lastQuotaMark = $quotaMark;
    }

    public function createQuotaMark($deleteOlder = true)
    {
        if ($deleteOlder) {
            $this->resetSendingQuota();
        }

        $quotaMark = new CustomerQuotaMark();
        $quotaMark->customer_id = $this->customer_id;
        $quotaMark->save(false);
        return $this->_lastQuotaMark = $quotaMark;
    }

    public function getHasGroup()
    {
        if (!$this->hasAttribute('group_id') || !$this->group_id) {
            return false;
        }
        return !empty($this->group) ? $this->group : false;
    }

    public function getGroupOption($option, $defaultValue = null)
    {
        static $loaded = array();

        if (!isset($loaded[$this->customer_id])) {
            $loaded[$this->customer_id] = array();
        }

        if (strpos($option, 'system.customer_') !== 0) {
            $option = 'system.customer_' . $option;
        }

        if (array_key_exists($option, $loaded[$this->customer_id])) {
            return $loaded[$this->customer_id][$option];
        }

        if (!($group = $this->getHasGroup())) {
            return $loaded[$this->customer_id][$option] = Yii::app()->options->get($option, $defaultValue);
        }

        return $loaded[$this->customer_id][$option] = $group->getOptionValue($option, $defaultValue);
    }

    public function getGravatarUrl($size = 50)
    {
        $gravatar = sprintf('//www.gravatar.com/avatar/%s?s=%d', md5(strtolower(trim($this->email))), (int)$size);
        return Yii::app()->hooks->applyFilters('customer_get_gravatar_url', $gravatar, $this, $size);
    }
	

    public function getAvatarUrl($width = 50, $height = 50, $forceSize = false)
    {
        if (empty($this->avatar)) {
            return $this->getGravatarUrl($width);
        }
        return ImageHelper::resize($this->avatar, $width, $height, $forceSize);
    }
	
	public function getAvatar1Url($width = 50, $height = 50, $forceSize = false)
    {
        if (empty($this->avatar_1)) {
			return '/frontend/assets/files/avatars1/default_logo.png';
            //return $this->getGravatarUrl($width);
        }
        return ImageHelper::resize($this->avatar_1, $width, $height, $forceSize);
    }

    // since 1.3.5
    public function getIsActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function getAllListsIds()
    {
        static $ids = array();
        if (isset($ids[$this->customer_id])) {
            return $ids[$this->customer_id];
        }
        $ids[$this->customer_id] = array();

        $criteria = new CDbCriteria();
        $criteria->select    = 'list_id';
        $criteria->condition = 'customer_id = :cid AND `status` != :st';
        $criteria->params    = array(':cid' => (int)$this->customer_id, ':st' => Lists::STATUS_PENDING_DELETE);
        $models = Lists::model()->findAll($criteria);
        foreach ($models as $model) {
            $ids[$this->customer_id][] = $model->list_id;
        }
        return $ids[$this->customer_id];
    }

    /**
     * @since 1.3.6.2
     * @param PricePlan $pricePlan
     * @return CAttributeCollection
     */
    public function isOverPricePlanLimits(PricePlan $pricePlan)
    {
        $default = new CAttributeCollection(array(
            'overLimit' => false,
            'object'    => '',
            'limit'     => 0,
            'count'     => 0,
        ));
        
        $in = clone $default;
        $in->overLimit = true;
        
        $kp  = 'system.customer_';
        $grp = $pricePlan->customerGroup;
        
        if (($in->limit = (int)$grp->getOptionValue($kp . 'servers.max_bounce_servers', 0)) > 0) {
            $in->count  = BounceServer::model()->countByAttributes(array('customer_id' => (int)$this->customer_id));
            $in->object = 'bounce servers';
            if ($in->count >= $in->limit) {
                return $in;
            }
        }

        if (($in->limit = (int)$grp->getOptionValue($kp . 'servers.max_delivery_servers', 0)) > 0) {
            $in->count  = DeliveryServer::model()->countByAttributes(array('customer_id' => (int)$this->customer_id));
            $in->object = 'delivery servers';
            if ($in->count >= $in->limit) {
                return $in;
            }
        }

        if (($in->limit = (int)$grp->getOptionValue($kp . 'servers.max_fbl_servers', 0)) > 0) {
            $in->count  = FeedbackLoopServer::model()->countByAttributes(array('customer_id' => (int)$this->customer_id));
            $in->object = 'feedback loop servers';
            if ($in->count >= $in->limit) {
                return $in;
            }
        }
        
        if (($in->limit = (int)$grp->getOptionValue($kp . 'campaigns.max_campaigns', 0)) > 0) {
            $criteria = new CDbCriteria();
            $criteria->compare('customer_id', (int)$this->customer_id);
            $criteria->addNotInCondition('status', array(Campaign::STATUS_PENDING_DELETE));
            $in->count  = Campaign::model()->count($criteria);
            $in->object = 'campaigns';
            if ($in->count >= $in->limit) {
                return $in;
            }
        }
        
        if (($in->limit = (int)$grp->getOptionValue($kp . 'lists.max_subscribers', 0)) > 0) {
            $criteria = new CDbCriteria();
            $criteria->select = 'COUNT(DISTINCT(t.email)) as counter';
            $criteria->addInCondition('t.list_id', $this->getAllListsIds());
            $in->count  = ListSubscriber::model()->count($criteria);
            $in->object = 'subscribers';
            if ($in->count >= $in->limit) {
                return $in;
            }
        }

        if (($in->limit = (int)$grp->getOptionValue($kp . 'lists.max_lists', 0)) > 0) {
            $criteria = new CDbCriteria();
            $criteria->compare('customer_id', (int)$this->customer_id);
            $criteria->addNotInCondition('status', array(Lists::STATUS_PENDING_DELETE));
            $in->count  = Lists::model()->count($criteria);
            $in->object = 'lists';
            if ($in->count >= $in->limit) {
                return $in;
            }
        }

        if (($in->limit = (int)$grp->getOptionValue($kp . 'sending_domains.max_sending_domains', 0)) > 0) {
            $in->count  = SendingDomain::model()->countByAttributes(array('customer_id' => (int)$this->customer_id));
            $in->object = 'sending domains';
            if ($in->count >= $in->limit) {
                return $in;
            }
        }
        
        return $default;
    }
	
	public function getRemainingDay(){
		$data = array();
		$timeValue = (int)$this->getGroupOption('sending.quota_time_value', -1);
		
		$timeNow = time();
		$timeUnit  = $this->getGroupOption('sending.quota_time_unit', 'month');
		$seconds   = strtotime(sprintf('+ %d %s', $timeValue, ($timeValue == 1 ? $timeUnit : $timeUnit . 's')), $timeNow) - $timeNow;
		$timestamp = strtotime($this->getLastQuotaMark()->date_added) + $seconds;
		$over_date = date('m/d/Y', $timestamp);
		
		if(strtotime($over_date) > strtotime(date('m/d/Y'))){
			$remaining_day = strtotime($over_date) - strtotime(date('m/d/Y'));
		}else{
			$remaining_day = 0;
		}
		
		
		$days = floor($remaining_day / (60*60*24) );
		
		$get_percentage =  ($days == 0 ? 100 : $this->get_percentage($timeValue,$days));
		$remaing_per = (100 - $get_percentage);
		$data['remaining_day'] = $days;
		$data['get_percentage'] = $get_percentage;
		$data['remaing_per'] = $remaing_per;
		$prog_css_class = '';
		
		if($get_percentage < 66){
			$data['prog_css_class'] = 'success';
		}
		if($get_percentage > 66){
			$data['prog_css_class'] = 'warning';
		}
		if($get_percentage > 83){
			$data['prog_css_class'] = 'danger';
		}
		return $data;
	}
	protected function get_percentage($total, $number)
	{
	  if ( $total > 0 ) {
	   return round($number / ($total / 100),2);
	  } else {
		return 0;
	  }
	}

    protected function handleUploadedAvatar()
    {
        if ($this->hasErrors()) {
            return;
        }

        if (!($avatar = CUploadedFile::getInstance($this, 'new_avatar'))) {
            return;
        }

        $storagePath = Yii::getPathOfAlias('root.frontend.assets.files.avatars');
        if (!file_exists($storagePath) || !is_dir($storagePath)) {
            if (!@mkdir($storagePath, 0777, true)) {
                $this->addError('new_avatar', Yii::t('customers', 'The avatars storage directory({path}) does not exists and cannot be created!', array(
                    '{path}' => $storagePath,
                )));
                return;
            }
        }

        $newAvatarName = uniqid(rand(0, time())) . '-' . $avatar->getName();
		//echo $storagePath;exit;
		//echo $storagePath . '/' . $newAvatarName;exit;
        if (!$avatar->saveAs($storagePath . '/' . $newAvatarName)) {
            $this->addError('new_avatar', Yii::t('customers', 'Cannot move the avatar into the correct storage folder!'));
            return;
        }

        $this->avatar = '/frontend/assets/files/avatars/' . $newAvatarName;
    }
	
	protected function handleUploadedAvatar1()
    {
        if ($this->hasErrors()) {
            return;
        }

        if (!($avatar_1 = CUploadedFile::getInstance($this, 'new_avatar_1'))) {
            return;
        }

        $storagePath = Yii::getPathOfAlias('root.frontend.assets.files.avatars1');
        if (!file_exists($storagePath) || !is_dir($storagePath)) {
            if (!@mkdir($storagePath, 0777, true)) {
                $this->addError('new_avatar_1', Yii::t('customers', 'The logos storage directory({path}) does not exists and cannot be created!', array(
                    '{path}' => $storagePath,
                )));
                return;
            }
        }

        $newAvatar1Name = uniqid(rand(0, time())) . '-' . $avatar_1->getName();
		//echo $storagePath;exit;
		//echo $storagePath . '/' . $newAvatarName;exit;
        if (!$avatar_1->saveAs($storagePath . '/' . $newAvatar1Name)) {
            $this->addError('new_avatar_1', Yii::t('customers', 'Cannot move the logo into the correct storage folder!'));
            return;
        }

        $this->avatar_1 = '/frontend/assets/files/avatars1/' . $newAvatar1Name;
    }

    protected function _takeQuotaAction()
    {
        $quotaAction = $this->getGroupOption('sending.action_quota_reached', '');
        if (empty($quotaAction)) {
            return true;
        }

        $this->createQuotaMark();

        if ($quotaAction != 'move-in-group') {
            return true;
        }

        $moveInGroupId = (int)$this->getGroupOption('sending.move_to_group_id', '');
        if (empty($moveInGroupId)) {
            return true;
        }

        $group = CustomerGroup::model()->findByPk($moveInGroupId);
        if (empty($group)) {
            return true;
        }

        $this->group_id = $group->group_id;
        $this->addRelatedRecord('group', $group, false);
        $this->save(false);

        return true;
    }
	
	public function getPlan(){
		$plan_array = PricePlanOrder::model()->findAllByAttributes(array('customer_id' => $this->customer_id));
		$plan_name = '';
		if(empty($plan_array) && $this->plan_id == ''){
			$group_array = CustomerGroup::model()->findByPk($this->group_id);
			$plan_name = $group_array['name'];
		}else if(is_array($plan_array) && count($plan_array)){
			foreach($plan_array as $plan_key => $plans){
				$price_plan = PricePlan::model()->findByPk($plans->plan->plan_id);
				$plan_name = $price_plan['name'];
			}
		}else if($this->plan_id != ''){
			$price_plan = PricePlan::model()->findByPk($this->plan_id);
			$plan_name = $price_plan['name'];
		}
		return $plan_name;
	}
}
