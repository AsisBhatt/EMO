<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * SocialmediApi
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
 * @property integer $socialmedia_id
 * @property integer $customer_id
 * @property string $socialmedia_website
 * @property string $socialmedia_fname
 * @property string $socialmedia_lname
 * @property string $socialmedia_business_name
 * @property string $socialmedia_mobile_no
 * @property string $socialmedia_email
 * @property string $socialmedia_gmail
 * @property string $socialmedia_logging
 * @property string $socialmedia_password
 * @property string $socialmedia_link
 * @property string $socialmedia_status
 * @property string $socialmedia_last_logining
 * @property string $socialmedia_created
 * @property string $socialmedia_updated
 *
 * The followings are the available model relations:
 * @property Customer $customer
 */
class SocialmediaApi extends ActiveRecord
{
	//const STATUS_UNSEEN = 'unseen';

	//const STATUS_SEEN = 'seen';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{socialmedia_api}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('socialmedia_fname, socialmedia_lname, socialmedia_business_name, socialmedia_mobile_no , socialmedia_email, socialmedia_gmail, socialmedia_logging, socialmedia_password', 'required'),

			// The following rule is used by search().
			array('socialmedia_website, socialmedia_business_name, socialmedia_email', 'safe', 'on'=>'search'),
			
			array('socialmedia_updated','default',
              'value'=>new CDbExpression('NOW()'),
              'setOnEmpty'=>false,'on'=>'update'),
			  
			array('socialmedia_created,socialmedia_updated','default',
              'value'=>new CDbExpression('NOW()'),
              'setOnEmpty'=>false,'on'=>'insert')
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
		);
		return CMap::mergeArray($relations, parent::relations());
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'socialmedia_id'  => Yii::t('socialmedia_api', 'Socialmedia Id'),
			'customer_id'  => Yii::t('socialmedia_api', 'Customer Id'),
			'socialmedia_website'  => Yii::t('socialmedia_api', 'Website'),
			'socialmedia_fname'  => Yii::t('socialmedia_api', 'First name'),
			'socialmedia_lname'  => Yii::t('socialmedia_api', 'Last name'),
			'socialmedia_business_name'  => Yii::t('socialmedia_api', 'Business name'),
			'socialmedia_mobile_no'  => Yii::t('socialmedia_api', 'Mobile Cell Phone'),
			'socialmedia_email'  => Yii::t('socialmedia_api', 'Email'),
			'socialmedia_gmail'  => Yii::t('socialmedia_api', 'Gmail'),
			'socialmedia_logging'  => Yii::t('socialmedia_api', 'Logging'),
			'socialmedia_password'  => Yii::t('socialmedia_api', 'Password'),
			'socialmedia_link'  => Yii::t('socialmedia_api', 'Social Link'),
			
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
		
		$criteria->compare('t.socialmedia_website', $this->socialmedia_website, true);
		$criteria->compare('t.socialmedia_business_name', $this->socialmedia_business_name, true);
		$criteria->compare('t.socialmedia_email', $this->socialmedia_email, true);

		$criteria->order = 't.socialmedia_id DESC';

		return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize' => $this->paginationOptions->getPageSize(),
                'pageVar'  => 'page',
            ),
            'sort'=>array(
                'defaultOrder' => array(
                    't.socialmedia_id' => CSort::SORT_DESC,
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

	/*public function getStatusesList()
    {
        return array(
            self::STATUS_UNSEEN => Yii::t('messages', 'Unseen'),
            self::STATUS_SEEN   => Yii::t('messages', 'Seen'),
        );
    }*/

}
