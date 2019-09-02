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
 * @property integer $smssetting_id
 * @property string $apiurl
 * @property integer $authkey
 * @property string $access_key
 * @property string $sender
 * @property string $route
 * @property string $country
 * @property string $status
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property Customer $customer
 */
class Smssetting extends ActiveRecord
{
	const STATUS_UNSEEN = 'unseen';

	const STATUS_SEEN = 'seen';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{smssetting}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('apiurl, authkey, access_key, sender , route, country', 'required'),

			// The following rule is used by search().
			array('apiurl, authkey, access_key, sender , route, country', 'safe', 'on'=>'search'),
		);

		return CMap::mergeArray($rules, parent::rules());
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = array(
			//'customer' => array(self::BELONGS_TO, 'Customer', 'customer_id'),
		);
		return CMap::mergeArray($relations, parent::relations());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'smssetting_id'  => Yii::t('messages', 'Smssetting'),
			'apiurl' => Yii::t('messages', 'API Url'),
			'authkey'		  => Yii::t('messages', 'Auth Key'),
			'access_key'	=> Yii::t('messages', 'Access Key'),
			'sender' 	  => Yii::t('messages', 'Sender'),
			'route' 	  => Yii::t('messages', 'Route'),
			'country'	  => Yii::t('messages', 'Country'),
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

		$criteria->compare('t.apiurl', $this->apiurl, true);
		$criteria->compare('t.authkey', $this->authkey, true);
		$criteria->compare('t.access_key', $this->access_key,true);
		$criteria->compare('t.sender', $this->sender, true);
		$criteria->compare('t.route', $this->route, true);
		$criteria->compare('t.country', $this->country, true);
		$criteria->compare('t.status', $this->status);

		$criteria->order = 't.smssetting_id DESC';

		return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize' => $this->paginationOptions->getPageSize(),
                'pageVar'  => 'page',
            ),
            'sort'=>array(
                'defaultOrder' => array(
                    't.message_id' => CSort::SORT_DESC,
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
        return $this->message_uid;
    }

	public function getStatusesList()
    {
        return array(
            self::STATUS_UNSEEN => Yii::t('messages', 'Unseen'),
            self::STATUS_SEEN   => Yii::t('messages', 'Seen'),
        );
    }

}
