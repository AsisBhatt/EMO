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
 * This is the model class for table "socialpost".
 *
 * The followings are the available columns in table 'customer_message':
 * @property integer $socialpost_id
 * @property string $text
 * @property string $imagename
 * @property string $videoname
 * @property string $status
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property Customer $customer
 */
class Socialpost extends ActiveRecord
{
	const STATUS_UNSEEN = 'unseen';

	const STATUS_SEEN = 'seen';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{socialpost2}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			
			array('text ', 'required'),

			// The following rule is used by search().
			array('text ', 'safe', 'on'=>'search'),
						  
			array('date_added,last_updated','default',
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
			'socialpost_id'  => Yii::t('messages', 'socialpost_id'),
			'customer_id'  => Yii::t('messages', 'customer_id'),
			'text'  => Yii::t('messages', 'Message'),
			'image_url' => Yii::t('messages', 'image_url'),
			'video_url' => Yii::t('messages', 'video_url'),
			'link_url'		  => Yii::t('messages', 'link_url'),
			'linkedin_postid'		  => Yii::t('messages', 'linkedin_postid'),
			'twitter_postid' 	  => Yii::t('messages', 'twitter_postid'),
			'facebook_postid' 	  => Yii::t('messages', 'facebook_postid'),
			'status' 	  => Yii::t('messages', 'Status'),
			'date_added' 	  => Yii::t('messages', 'Date'),

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

		$criteria->compare('t.text', $this->text, true);
		
		//$criteria->compare('t.status', $this->status);

		$criteria->order = 't.socialpost_id DESC';

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
	
	
	
	
	
	
	protected function handleUploadedImage()
    {
		if (!($avatar = CUploadedFile::getInstance($this, 'imagename'))) {
			return;
		}

		$storagePath = Yii::getPathOfAlias('root.frontend.assets.files');
		if (!file_exists($storagePath) || !is_dir($storagePath)) {
			if (!@mkdir($storagePath, 0777, true)) {
				$this->addError('new_avatar', Yii::t('customers', 'The avatars storage directory({path}) does not exists and cannot be created!', array(
					'{path}' => $storagePath,
				)));
				return;
			}
		}

		$newAvatarName = uniqid(rand(0, time())) . '-' . $avatar->getName();
		if (!$avatar->saveAs($storagePath . '/' . $newAvatarName)) {
			$this->addError('new_avatar', Yii::t('customers', 'Cannot move the avatar into the correct storage folder!'));
			return;
		}
        $this->avatar = '/frontend/assets/files/' . $newAvatarName;
    }
	
	
	

}
