<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * UserLogin
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class UserLogin extends User
{
    public $remember_me = true;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $hooks  = Yii::app()->hooks;
        $apps   = Yii::app()->apps;
        $filter = $apps->getCurrentAppName() . '_model_'.strtolower(get_class($this)).'_'.strtolower(__FUNCTION__);

        $rules = array(
            array('email, password', 'required'),

            array('email', 'length', 'min' => 7, 'max' => 100),
            array('email', 'email', 'validateIDN' => true),
            array('password', 'length', 'min' => 6, 'max' => 100),
            array('password', 'authenticate'),

            array('remember_me', 'safe'),
        );

        $rules = $hooks->applyFilters($filter, new CList($rules));
        $this->onRules(new CModelEvent($this, array(
            'rules' => $rules,
        )));

        return $rules->toArray();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'remember_me' => Yii::t('users', 'Remember'),
        );

        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Customer the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function authenticate($attribute, $params)
    {
        if ($this->hasErrors()) {
            return;
        }

        $identity = new UserIdentity($this->email, $this->password);
        if (!$identity->authenticate()) {
            $this->addError($attribute, $identity->errorCode);
            return;
        }

        Yii::app()->user->login($identity, $this->remember_me ? 3600 * 24 * 30 : 0);
    }
}
