<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * DeliveryServerLeadersendWebApi
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.9
 *
 */

class DeliveryServerLeadersendWebApi extends DeliveryServer
{
    protected $serverType = 'leadersend-web-api';

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            array('password', 'required'),
            array('password', 'length', 'max' => 255),
        );
        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'hostname' => Yii::t('servers', 'Domain name'),
            'password' => Yii::t('servers', 'Api key'),
        );
        return CMap::mergeArray(parent::attributeLabels(), $labels);
    }

    public function attributeHelpTexts()
    {
        $texts = array(
            'hostname'  => Yii::t('servers', 'Leadersend verified domain name.'),
            'password'  => Yii::t('servers', 'Leadersend api key.'),
        );

        return CMap::mergeArray(parent::attributeHelpTexts(), $texts);
    }

    public function attributePlaceholders()
    {
        $placeholders = array(
            'hostname'  => Yii::t('servers', 'Domain name'),
            'password'  => Yii::t('servers', 'Api key'),
        );

        return CMap::mergeArray(parent::attributePlaceholders(), $placeholders);
    }

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

        if (!isset($params['from'], $params['to'], $params['subject'], $params['body'])) {
            return false;
        }

        list($fromEmail, $fromName) = $this->getMailer()->findEmailAndName($params['from']);
        list($toEmail, $toName)     = $this->getMailer()->findEmailAndName($params['to']);

        if (!empty($params['fromName'])) {
            $fromName = $params['fromName'];
        }

        $sent = false;
        try {
            //
            $headers = array();
            if (!empty($params['headers'])) {
                $headers = $this->parseHeadersIntoKeyValue($params['headers']);
            }
            //

            $message = array(
                'from'   => array('name' => $fromName, 'email' => $fromEmail),
                'to'     => array('name' => $toName, 'email' => $toEmail),
                'html'   => $params['body'],
                'text'   => !empty($params['plainText']) ? $params['plainText'] : CampaignHelper::htmlToText($params['body']),
                'subject'=> $params['subject'],
                'headers'=> $headers,
                'auto_html'  => false,
                'auto_plain' => false,
                'tracking' => array(
                    'opens'         => false,
                    'html_clicks'   => false,
                    'plain_clicks'  => false,
                ),
                'signing_domain' => $this->hostname,
                'attachments'    => array(),
                'images'         => array(),
            );

            $onlyPlainText = !empty($params['onlyPlainText']) && $params['onlyPlainText'] === true;
            if (!$onlyPlainText && !empty($params['attachments']) && is_array($params['attachments'])) {
                $attachments = array_unique($params['attachments']);
                foreach ($attachments as $attachment) {
                    if (is_file($attachment)) {
                        $message['attachments'][] = array(
                            'type'    => 'application/octet-stream',
                            'name'    => basename($attachment),
                            'content' => base64_encode(file_get_contents($attachment)),
                        );
                    }
                }
            }

            if (!$onlyPlainText && !empty($params['embedImages']) && is_array($params['embedImages'])) {
                $cids = array();
                foreach ($params['embedImages'] as $imageData) {
                    if (!isset($imageData['path'], $imageData['cid'])) {
                        continue;
                    }
                    if (is_file($imageData['path'])) {
                        $cids['cid:' . $imageData['cid']] = base64_encode(file_get_contents($imageData['path']));
                        $imageData['mime'] = empty($imageData['mime']) ? 'image/jpg' : $imageData['mime'];
                        $message['images'][] = array(
                            'type'    => $imageData['mime'],
                            'name'    => basename($imageData['path']),
                            'content' => $cids['cid:' . $imageData['cid']],
                            'cid'     => $imageData['cid'],
                        );
                    }
                }
                unset($cids);
            }

            $response = $this->getClient()->messagesSend($message);
            
            if ($this->getClient()->errorCode) {
                throw new Exception($this->getClient()->errorMessage);
            }
            
            if (!empty($response) && !empty($response[0])) {
                if ($response[0]['status'] == 'sent') {
                    $sent = array('message_id' => $response[0]['id']);
                    $this->getMailer()->addLog('OK');
                } elseif (!empty($response[0]['reject_reason'])) {
                    $reason = $response[0]['reject_reason'];
                    $this->getMailer()->addLog($reason);
                    if (stripos($reason, 'blacklist') !== false || stripos($reason, 'hard') !== false) {
                        EmailBlacklist::addToBlacklist($toEmail, $response[0]['reject_reason']);
                    }
                }
            } else {
                throw new Exception(Yii::t('servers', 'Unable to make the delivery!'));
            }

        } catch (Exception $e) {
            $this->getMailer()->addLog($e->getMessage());
        }

        if ($sent) {
            $this->logUsage();
        }

        Yii::app()->hooks->doAction('delivery_server_after_send_email', $params, $this, $sent);

        return $sent;
    }

    public function getParamsArray(array $params = array())
    {
        $params['transport'] = self::TRANSPORT_LEADERSEND_WEB_API;
        return parent::getParamsArray($params);
    }

    public function getClient()
    {
        static $clients = array();
        $id = (int)$this->server_id;
        if (!empty($clients[$id])) {
            return $clients[$id];
        }
        require_once(Yii::getPathOfAlias('common.vendors.Leadersend.Leadersend') . '.class.php');
        return $clients[$id] = new Leadersend($this->password);
    }
}
