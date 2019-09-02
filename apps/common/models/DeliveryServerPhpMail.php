<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * DeliveryServerPhpMail
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.2
 */

class DeliveryServerPhpMail extends DeliveryServer
{
    protected $serverType = 'php-mail';

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return DeliveryServer the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function sendEmail(array $params = array())
    {
        $params = (array)Yii::app()->hooks->applyFilters('delivery_server_before_send_email', $this->getParamsArray($params), $this);

        if ($sent = $this->getMailer()->send($params)) {
            $sent = array('message_id' => $this->getMailer()->getEmailMessageId());
            $this->logUsage();
        }

        Yii::app()->hooks->doAction('delivery_server_after_send_email', $params, $this, $sent);

        return $sent;
    }

    public function getParamsArray(array $params = array())
    {
        $params['transport'] = self::TRANSPORT_PHP_MAIL;
        return parent::getParamsArray($params);
    }

    protected function beforeValidate()
    {
        $this->hostname = 'php-mail.local.host';
        $this->port     = null;
        $this->timeout  = null;

        return parent::beforeValidate();
    }
}
