<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * OptionLicense
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.9
 */

class OptionLicense extends OptionBase
{
    // settings category
    protected $_categoryName = 'system.license';

    public $first_name;

    public $last_name;

    public $email;

    public $market_place;

    public $purchase_code;

    public function rules()
    {
        $rules = array(
            array('first_name, last_name, email, market_place, purchase_code', 'required'),
            array('first_name, last_name, email, market_place, purchase_code', 'length', 'max' => 255),
            array('email', 'email', 'validateIDN' => true),
        );

        return CMap::mergeArray($rules, parent::rules());
    }

    public function attributeLabels()
    {
        $labels = array(
            'first_name'    => Yii::t('settings', 'First name'),
            'last_name'     => Yii::t('settings', 'Last name'),
            'email'         => Yii::t('settings', 'Email'),
            'market_place'  => Yii::t('settings', 'Market place'),
            'purchase_code' => Yii::t('settings', 'Purchase code'),
        );

        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    public function attributeHelpTexts()
    {
        $texts = array();
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }
}
