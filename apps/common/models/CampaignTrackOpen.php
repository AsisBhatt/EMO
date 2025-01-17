<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CampaignTrackOpen
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
/**
 * This is the model class for table "campaign_track_open".
 *
 * The followings are the available columns in table 'campaign_track_open':
 * @property string $id
 * @property integer $campaign_id
 * @property integer $subscriber_id
 * @property integer $location_id
 * @property string $ip_address
 * @property string $user_agent
 * @property string $date_added
 *
 * The followings are the available model relations:
 * @property Campaign $campaign
 * @property ListSubscriber $subscriber
 * @property IpLocation $ipLocation
 */
class CampaignTrackOpen extends ActiveRecord
{
    public $counter = 0;
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{campaign_track_open}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array();
        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations = array(
            'campaign' => array(self::BELONGS_TO, 'Campaign', 'campaign_id'),
            'subscriber' => array(self::BELONGS_TO, 'ListSubscriber', 'subscriber_id'),
            'ipLocation' => array(self::BELONGS_TO, 'IpLocation', 'location_id'),
        );
        
        return CMap::mergeArray($relations, parent::relations());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'id'            => Yii::t('campaigns', 'ID'),
            'campaign_id'   => Yii::t('campaigns', 'Campaign'),
            'subscriber_id' => Yii::t('campaigns', 'Subscriber'),
            'location_id'   => Yii::t('campaigns', 'Location'),
            'ip_address'    => Yii::t('campaigns', 'Ip address'),
            'user_agent'    => Yii::t('campaigns', 'User agent'),
            'open_times'    => Yii::t('campaigns', 'Open times'),
        );
        
        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CampaignTrackOpen the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    
    public function getIpWithLocationForGrid()
    {
        if (empty($this->ipLocation)) {
            return $this->ip_address;
        }
        
        return $this->ip_address .' <br />('.$this->ipLocation->getLocation().')';
    }
}
