<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * FeedbackLoopServer
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */
 
/**
 * This is the model class for table "bounce_server".
 *
 * The followings are the available columns in table 'bounce_server':
 * @property integer $server_id
 * @property integer $customer_id
 * @property string $hostname
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $service
 * @property integer $port
 * @property string $protocol
 * @property string $validate_ssl
 * @property string $locked
 * @property string $status
 * @property string $date_added
 * @property string $last_updated
 * 
 * The followings are the available model relations:
 * @property Customer $customer
 */
class FeedbackLoopServer extends BounceServer
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{feedback_loop_server}}';
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return FeedbackLoopServer the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
