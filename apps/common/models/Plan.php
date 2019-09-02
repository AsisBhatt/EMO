<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * PricePlan
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.4
 */

/**
 * This is the model class for table "{{price_plan}}".
 *
 * The followings are the available columns in table '{{price_plan}}':
 * @property integer $plan_id
 * @property string $plan_uid
 * @property integer $group_id
 * @property string $name
 * @property string $price
 * @property string $description
 * @property string $recommended
 * @property string $visible
 * @property integer $sort_order
 * @property integer $list_limit
 * @property integer $listsend_limit
 * @property string $status
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property CustomerGroup $customerGroup
 * @property PricePlanOrder[] $pricePlanOrders
 */
class Plan extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{plan}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			array('name, sms_total, email_total, validity, list_limit, price,status, listsend_limit', 'required'),
			array('name', 'length', 'max' => 50),
			array('price', 'numerical'),
            //array('price', 'type', 'type' => 'float'),
			//array('status', 'in', 'range' => array_keys($this->getStatusesList())),
			//array('sort_order', 'numerical', 'integerOnly' => true),
            
			// The following rule is used by search().
			array('name, sms_total,email_total, validity,price, list_limit, status', 'safe', 'on'=>'search'),
            array('description', 'safe'),
		);
        return CMap::mergeArray($rules, parent::rules());
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		/*
		$relations = array(
			'customerGroup'   => array(self::BELONGS_TO, 'CustomerGroup', 'group_id'),
            'pricePlanOrders' => array(self::HAS_MANY, 'PricePlanOrder', 'plan_id'),
		);
        return CMap::mergeArray($relations, parent::relations());
		*/
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		$labels = array(
			'plan_id'     => Yii::t('plan', 'Plan'),
			'name'    => Yii::t('plan', 'Name'),
			'description' => Yii::t('plan', 'Description'),
			'sms_total'    => Yii::t('plan', 'Sms Total'),
			'email_total'    => Yii::t('plan', 'Email Total'),
			'validity'    => Yii::t('plan', 'Validity'),
			'list_limit'    => Yii::t('plan', 'Set List Limit'),
			'listsend_limit'    => Yii::t('plan', 'Set Send List'),
			'price'       => Yii::t('plan', 'Price'),
		);
        return CMap::mergeArray($labels, parent::attributeLabels());
	}
    
    /**
     * @return array help text for attributes
     */
    /*
	public function attributeHelpTexts()
    {
        $texts = array(
			'group_id'    => Yii::t('plan', 'The group where the customer will be moved after purchasing this plan. Make sure the group has proper permissions and limits'),
			'name'        => Yii::t('plan', 'The price plan name, used in customer display area, orders, etc'),
			'price'       => Yii::t('plan', 'The amount the customers will be charged when buying this plan'),
			'description' => Yii::t('plan', 'A detailed description about the price plan features'),
			'recommended' => Yii::t('plan', 'Whether this plan has the recommended badge on it'),
            'visible'     => Yii::t('plan', 'Whether this plan is visible in customers area'),
		);
        
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }
	*/

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

		$criteria->compare('name', $this->name, true);
        $criteria->compare('sms_total', $this->sms_total);
        $criteria->compare('email_total', $this->email_total);
        $criteria->compare('validity', $this->validity);
		$criteria->compare('price', $this->price, true);
		$criteria->compare('list_limit', $this->list_limit, true);
		$criteria->compare('listsend_limit', $this->listsend_limit, true);
		$criteria->compare('status', $this->status);

		return new CActiveDataProvider(get_class($this), array(
            'criteria'   => $criteria,
            'pagination' => array(
                'pageSize' => $this->paginationOptions->getPageSize(),
                'pageVar'  => 'page',
            ),
            'sort'=>array(
                'defaultOrder' => array(
                    'sort_order'  => CSort::SORT_ASC,
                    'plan_id'     => CSort::SORT_DESC,
                ),
            ),
        ));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PricePlan the static model class
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
        /*
        if (empty($this->plan_uid)) {
            $this->plan_uid = $this->generateUid();
        }
		*/

        return true;
    }
    
    public function findByUid($plan_uid)
    {
        return $this->findByAttributes(array(
            'plan_uid' => $plan_uid,
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
        return $this->plan_uid;
    }
    
    public function getFormattedPrice()
    {
        return Yii::app()->numberFormatter->formatCurrency($this->price, $this->getCurrency()->code);
    }
    
    public function getCurrency()
    {
        return Currency::model()->findDefault();
    }
    
    public function getIsRecommended()
    {
        return $this->recommended == self::TEXT_YES;
    }
}
