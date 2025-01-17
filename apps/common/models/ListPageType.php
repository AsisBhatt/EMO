<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * ListPageType
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
/**
 * This is the model class for table "list_page_type".
 *
 * The followings are the available columns in table 'list_page_type':
 * @property integer $type_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string $content
 * @property string $full_html
 * @property string $meta_data
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property ListPage[] $listPages
 */
class ListPageType extends ActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{list_page_type}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            array('name, content, description', 'required'),
        );
        
        return CMap::mergeArray($rules, parent::rules());
    }
    
    /**
     * @return array available behaviors.
     */
    public function behaviors()
    {
        $behaviors = array(
            'tags' => array(
                'class' => 'common.components.db.behaviors.PageTypeTagsBehavior'
            ),
        );
        
        return CMap::mergeArray($behaviors, parent::behaviors());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations = array(
            'listPages' => array(self::HAS_MANY, 'ListPage', 'type_id'),
        );

        return CMap::mergeArray($relations, parent::relations());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'type_id'       => Yii::t('list_page_types', 'Type'),
            'name'          => Yii::t('list_page_types', 'Name'),
            'description'   => Yii::t('list_page_types', 'Description'),
            'content'       => Yii::t('list_page_types', 'Default content'),
            'full_html'     => Yii::t('list_page_types', 'Full html'),
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

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
    
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return ListDisplayType the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function findBySlug($slug)
    {
        return $this->findByAttributes(array(
            'slug' => $slug
        ));    
    }
    
    protected function beforeDelete()
    {
        return false;
    }
    
    public function beforeSave()
    {
        $this->content = StringHelper::decodeSurroundingTags($this->content);
        return parent::beforeSave();
    }
}
