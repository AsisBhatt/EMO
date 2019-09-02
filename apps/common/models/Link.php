<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Link
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.9
 */

/**
 * This is the model class for table "link".
 *
 * The followings are the available columns in table 'link':
 * @property integer $link_id
 * @property integer $customer_id
 * @property string $url
 * @property integer $hits
 * @property string $status
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property Customer $customer
 */
class Link extends ActiveRecord
{
	const STATUS_UNPUBLISHED = 'unpublished';

	const STATUS_PUBLISHED = 'published';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{links}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('url', 'required'),
           
			// The following rule is used by search().
			array('link_id, customer_id, url,hits', 'safe', 'on' => 'search'),	
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
            'link_id'    => Yii::t('link', 'LinkId'),
            'customer_id'         => Yii::t('link', 'Customer'),
            'url'          => Yii::t('link', 'Url (Including htpp/https)'),
            'hits'       => Yii::t('link', 'Hits'),
            'date_added'       => Yii::t('link', 'Date'),
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
		//print_r($this); die();
		$criteria->compare('t.link_id', $this->link_id, true);
		$criteria->compare('t.customer_id', (int)$this->customer_id);
		$criteria->compare('t.url', $this->url, true);
		$criteria->compare('t.hits', $this->hits, true);
		$criteria->compare('t.date_added', $this->date_added, true);
		
		$criteria->order = 't.link_id DESC';

		return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize' => $this->paginationOptions->getPageSize(),
                'pageVar'  => 'page',
            ),
            'sort'=>array(
                'defaultOrder' => array(
                    't.link_id' => CSort::SORT_DESC,
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


	public function getStatusesList()
    {
        return array(
            self::STATUS_UNPUBLISHED => Yii::t('messages', 'Unpublished'),
            self::STATUS_PUBLISHED   => Yii::t('messages', 'Published'),
        );
    }
	
	
}