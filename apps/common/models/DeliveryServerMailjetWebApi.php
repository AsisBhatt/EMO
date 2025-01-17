<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * DeliveryServerMailjetWebApi
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.3
 *
 */

class DeliveryServerMailjetWebApi extends DeliveryServer
{
    protected $serverType = 'mailjet-web-api';

    protected $_initStatus;

    protected $_preCheckError;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            array('username, password', 'required'),
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
            'username'   => Yii::t('servers', 'Api key'),
            'password'   => Yii::t('servers', 'Api secret'),
        );
        return CMap::mergeArray(parent::attributeLabels(), $labels);
    }

    public function attributeHelpTexts()
    {
        $texts = array(
            'username'    => Yii::t('servers', 'Your mailjet api key'),
            'password'    => Yii::t('servers', 'Your mailjet api secret'),
            'force_from'  => Yii::t('servers', 'When to force the FROM address. Please note that if you set this option to Never and you send from a unverified domain, all your emails will fail delivery. It is best to leave this option as is unless you really know what you are doing.'),
        );

        return CMap::mergeArray(parent::attributeHelpTexts(), $texts);
    }

    public function attributePlaceholders()
    {
        $placeholders = array(
            'username'   => '124d28f660d808e0ea7bc19fc5cda116',
            'password'   => '0f1105ac9bc5ecd3f88ec8a172d25d22',
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

        list($toEmail, $toName)     = $this->getMailer()->findEmailAndName($params['to']);
        list($fromEmail, $fromName) = $this->getMailer()->findEmailAndName($params['from']);

        if (!empty($params['fromName'])) {
            $fromName = $params['fromName'];
        }

        $replyToEmail = null;
        if (!empty($params['replyTo'])) {
            list($replyToEmail) = $this->getMailer()->findEmailAndName($params['replyTo']);
        }

        $headerPrefix = Yii::app()->params['email.custom.header.prefix'];
        $headers = array();
        if (!empty($params['headers'])) {
            $headers = $this->parseHeadersIntoKeyValue($params['headers']);
        }
        $headers['X-Sender']   = $fromEmail;
        $headers['X-Receiver'] = $toEmail;
        $headers['Reply-To']   = $replyToEmail;
        $headers[$headerPrefix . 'Mailer'] = 'Mailjet Web API';
        
        $metaData   = array();
        if (isset($headers[$headerPrefix . 'Campaign-Uid'])) {
            $metaData['campaign_uid'] = $headers[$headerPrefix . 'Campaign-Uid'];
        }
        if (isset($headers[$headerPrefix . 'Subscriber-Uid'])) {
            $metaData['subscriber_uid'] = $headers[$headerPrefix . 'Subscriber-Uid'];
        }
        
        $headers['X-Mailjet-TrackOpen'] = "0";
        $headers['X-Mailjet-TrackClick'] = "0";

        $sent = false;

        try {
            if (!$this->preCheckWebHook()) {
                throw new Exception($this->_preCheckError);
            }
            
            $sendParams = array(
                'FromEmail' => $fromEmail,
                'FromName'  => sprintf('=?%s?B?%s?=', strtolower(Yii::app()->charset), base64_encode($fromName)),
                'Subject'   => sprintf('=?%s?B?%s?=', strtolower(Yii::app()->charset), base64_encode($params['subject'])),
                'Text-Part' => !empty($params['plainText']) ? $params['plainText'] : CampaignHelper::htmlToText($params['body']),
                'Html-Part' => $params['body'],
                'Recipients'=> array(
                    array(
                        'Email' => $toEmail,
                        'Name'  => sprintf('=?%s?B?%s?=', strtolower(Yii::app()->charset), base64_encode($toName)),
                    )
                ),
                'Headers'       => $headers,
                'Mj-trackopen'  => false,
                'Mj-trackclick' => false,
                'Vars'          => $metaData,
            );

            if (!empty($params['attachments']) && is_array($params['attachments'])) {
                $sendParams['Attachments'] = array();
                $_attachments = array_unique($params['attachments']);
                foreach ($_attachments as $attachment) {
                    if (is_file($attachment)) {
                        $fileName = basename($attachment);
                        $sendParams['Attachments'][] = array(
                            'Content-type' => "application/octet-stream",
                            'Filename'     => $fileName,
                            'content'      => base64_encode(file_get_contents($attachment)),
                        );
                    }
                }
            }

            $response = $this->getClient()->post(array('send', ''), array('body' => $sendParams));
            $data     = $response->getData();
                
            if ($response->success() && isset($data['Sent'], $data['Sent'][0])) {
                $this->getMailer()->addLog('OK');
                $sent = array('message_id' => $data['Sent'][0]['MessageID']);
            } else {
                if (empty($data)) {
                    $data = (array)$response;
                }
                throw new Exception(print_r($data, 1));
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

    public function getClient()
    {
        static $clients = array();
        $id = (int)$this->server_id;
        if (!empty($clients[$id])) {
            return $clients[$id];
        }
        $className = '\Mailjet\Client';
        return $clients[$id] = new $className($this->username, $this->password);
    }

    public function requirementsFailed()
    {
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            return Yii::t('servers', 'The server type {type} requires your php version to be at least {version}!', array(
                '{type}'    => $this->serverType,
                '{version}' => 5.4,
            ));
        }
        return false;
    }

    public function getParamsArray(array $params = array())
    {
        $params['transport'] = self::TRANSPORT_MAILJET_WEB_API;
        return parent::getParamsArray($params);
    }

    protected function afterConstruct()
    {
        parent::afterConstruct();
        $this->_initStatus = $this->status;
        $this->hostname    = 'web-api.mailjet.com';
    }

    protected function afterFind()
    {
        $this->_initStatus = $this->status;
        parent::afterFind();
    }

    protected function preCheckWebHook()
    {
        if (MW_IS_CLI || $this->isNewRecord || $this->_initStatus !== self::STATUS_INACTIVE) {
            return true;
        }
        
        try {
            
            foreach (array("bounce", "spam", "unsub") as $eventType) {
                $response = $this->getClient()->post(array('eventcallbackurl', ''), array(
                    'body' => array(
                        'EventType' => $eventType,
                        'Url'       => $this->getDswhUrl(),
                        'Version'   => "2"
                    )
                ));
                $data = $response->getData();
                if (!$response->success() && stripos($data['ErrorMessage'], 'already exists') === false) {
                    throw new Exception(Yii::t('servers', 'Please do not validate the delivery server until you fix this error') . ': '  .  $data['ErrorMessage']);
                }
            }

            // try to activate the sender.
            $response = $this->getClient()->post(array('sender', ''),  array(
                'body' => array('Email' => $this->from_email)
            ));
            $data = $response->getData();
            
            // flag
            $validate = $response->success();

            // email has been added, must validate it.
            if (!$response->success() && $data['StatusCode'] != 200) {
                if (stripos($data['ErrorMessage'], '"validate" action') !== false) {
                    $validate = true;
                } elseif(stripos($data['ErrorMessage'], 'already exists') !== false) {
                    $validate = true;
                } elseif (stripos($data['ErrorMessage'], 'already active') !== false) {
                    $validate = false;
                } else {
                    throw new Exception($data['ErrorMessage']);
                }
            }

            if ($validate) {
                $response = $this->getClient()->post(array('sender', 'validate'),  array(
                    'id' => $this->from_email,
                ));
                
                $data  = $response->getData();
                $error = !$response->success();
                $note  = true;
                
                if ($error && stripos($data['ErrorMessage'], 'already active')) {
                    $error = false;
                    $note  = false;
                }
                
                if ($error) {
                    throw new Exception($data['ErrorMessage']);
                }
                
                if ($note) {
                    throw new Exception(Yii::t('servers', 'We just sent a mailjet.com verification email at "{email}". Please check it then try again.', array(
                        '{email}' => $this->from_email,
                    )));
                }
            }
            
        } catch (Exception $e) {
            $this->_preCheckError = $e->getMessage();
        }

        if ($this->_preCheckError) {
            return false;
        }

        return $this->save(false);
    }
}
