<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Country
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
/**
 * This is the model class for table "image_gallery".
 *
 * The followings are the available columns in table 'country':
 * @property integer $image_id
 * @property integer $customer_id
 * @property string $filename
 * @property string $created_at
 *
 */
class ImageGallery extends ActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{image_gallery}}';
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
		$image_type = null;
        if (CommonHelper::functionExists('finfo_open')) {
            $image_type = Yii::app()->extensionMimes->get(array('png', 'jpg', 'jpeg', 'bmp', 'gif'))->toArray();
        }
        $rules = array(
            array('customer_id, filename', 'required'),
			//array('filename', 'file', 'types' => array('png', 'jpg', 'jpeg', 'bmp', 'gif'), 'mimeTypes' => $image_type, 'allowEmpty' => true),
            array('customer_id, filename', 'safe', 'on' => 'search'),
			
        );
        
        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations = array(
            'customer'	=> array(self::BELONGS_TO, 'Customer', 'customer_id'),
        );
        
        return CMap::mergeArray($relations, parent::relations());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'customer_id'    => Yii::t('countries', 'Customer'),
            'filename'          => Yii::t('countries', 'Image Name'),
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
        $criteria=new CDbCriteria;
		$criteria->compare('customer_id', $this->customer_id, true);
        $criteria->compare('filename', $this->filename, true);
        
        return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize'  => $this->paginationOptions->getPageSize(),
                'pageVar'   => 'page',
            ),
            'sort'=>array(
                'defaultOrder' => array(
                    'created_at'     => CSort::SORT_ASC,
                ),
            ),
        ));
    }
    
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Country the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
