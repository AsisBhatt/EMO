<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * TrackingDomain
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.6
 */

/**
 * This is the model class for table "{{tracking_domain}}".
 *
 * The followings are the available columns in table '{{tracking_domain}}':
 * @property integer $domain_id
 * @property integer $customer_id
 * @property string $name
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property DeliveryServer[] $deliveryServers
 * @property Customer $customer
 */
class TrackingDomain extends ActiveRecord
{
	// whether we should skip dns validation.
	public $skipValidation = 0;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{tracking_domain}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('name', 'required'),
			array('name', 'length', 'max'=> 255),
            array('name', '_validateDomainCname'),
            array('customer_id', 'exist', 'className' => 'Customer'),

            array('customer_id', 'unsafe', 'on' => 'customer-insert, customer-update'),

			// The following rule is used by search().
			array('customer_id, name', 'safe', 'on'=>'search'),

			array('skipValidation', 'safe'),
		);

        return CMap::mergeArray($rules, parent::rules());
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
			'deliveryServers' => array(self::HAS_MANY, 'DeliveryServer', 'tracking_domain_id'),
			'customer'        => array(self::BELONGS_TO, 'Customer', 'customer_id'),
		);

        return CMap::mergeArray($relations, parent::relations());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'domain_id'      => Yii::t('tracking_domains', 'Domain'),
			'customer_id'    => Yii::t('tracking_domains', 'Customer'),
			'name'           => Yii::t('tracking_domains', 'Name'),
			'skipValidation' => Yii::t('tracking_domains', 'Skip validation'),
		);

        return CMap::mergeArray($labels, parent::attributeLabels());
	}

    /**
	 * @return array customized attribute placeholders (name=>placeholder)
	 */
	public function attributePlaceholders()
	{
		$placeholders = array(
			'name' => Yii::t('tracking_domains', 'tracking.your-domain.com'),
		);

        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
	}

	/**
     * @return array help text for attributes
     */
    public function attributeHelpTexts()
    {
        $texts = array(
			'skipValidation' => Yii::t('tracking_domains', 'Please DO NOT SKIP validation unless you are 100% sure you know what you are doing.'),
		);

        return CMap::mergeArray($texts, parent::attributeHelpTexts());
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
		$criteria=new CDbCriteria;

        if (!empty($this->customer_id)) {
            if (is_numeric($this->customer_id)) {
                $criteria->compare('t.customer_id', $this->customer_id);
            } else {
                $criteria->with = array(
                    'customer' => array(
                        'joinType'  => 'INNER JOIN',
                        'condition' => 'CONCAT(customer.first_name, " ", customer.last_name) LIKE :name',
                        'params'    => array(
                            ':name'    => '%' . $this->customer_id . '%',
                        ),
                    )
                );
            }
        }

		$criteria->compare('t.name', $this->name, true);

		return new CActiveDataProvider(get_class($this), array(
            'criteria'   => $criteria,
            'pagination' => array(
                'pageSize' => $this->paginationOptions->getPageSize(),
                'pageVar'  => 'page',
            ),
            'sort'=>array(
                'defaultOrder' => array(
                    't.domain_id'  => CSort::SORT_DESC,
                ),
            ),
        ));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TrackingDomain the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function _validateDomainCname($attribute, $params)
    {
        if ($this->hasErrors() || $this->skipValidation) {
            return;
        }
        $currentDomainName = parse_url(Yii::app()->options->get('system.urls.frontend_absolute_url'), PHP_URL_HOST);
        if (empty($currentDomainName)) {
            return $this->addError($attribute, Yii::t('tracking_domains', 'Unable to get the current domain name!'));
        }
        $domainName = strpos($this->$attribute, 'http') !== 0 ? 'http://' . $this->$attribute : $this->$attribute;
        $domainName = parse_url($domainName, PHP_URL_HOST);
        if (empty($domainName)) {
            return $this->addError($attribute, Yii::t('tracking_domains', 'Your specified domain name does not seem to be valid!'));
        }
        if (!CommonHelper::functionExists('dns_get_record')) {
            return $this->addError($attribute, Yii::t('tracking_domains', 'Your PHP install does not contain the {function} function needed to query the DNS records!', array(
                '{function}' => 'dns_get_record',
            )));
        }
        $dnsRecords = (array)dns_get_record($domainName, DNS_ALL);
        $found = false;

		// cname first.
        foreach ($dnsRecords as $record) {
            if (!isset($record['host'], $record['type'], $record['target'])) {
                continue;
            }
            if ($record['host'] == $domainName && $record['type'] == 'CNAME' && $record['target'] == $currentDomainName) {
                $found = true;
                break;
            }
        }

		// subdomain second
		if (!$found) {
			foreach ($dnsRecords as $record) {
	            if (!isset($record['host'], $record['type'], $record['ip'])) {
	                continue;
	            }
				if ($record['type'] != 'A') {
					continue;
				}
				$ipDomain = gethostbyname($domainName);
	            if ($record['host'] == $domainName && $record['ip'] == $ipDomain) {
	                $found = true;
	                break;
	            }
	        }
		}

        if (!$found) {
            return $this->addError($attribute, Yii::t('tracking_domains', 'Cannot find a valid CNAME record for {domainName}! Remember, the CNAME of {domainName} must point to {currentDomain}!', array(
                '{domainName}'    => $domainName,
                '{currentDomain}' => $currentDomainName,
            )));
        }
    }
}
