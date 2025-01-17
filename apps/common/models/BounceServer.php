<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * BounceServer
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
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
 * @property DeliveryServer[] $deliveryServers
 * @property Customer $customer
 */
class BounceServer extends ActiveRecord
{
    public $settingsChanged = false;

    public $mailBox = 'INBOX';

    const STATUS_CRON_RUNNING = 'cron-running';

    const STATUS_HIDDEN = 'hidden';

    const STATUS_DISABLED = 'disabled';

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{bounce_server}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            array('hostname, username, password, port, service, protocol, validate_ssl', 'required'),

            array('hostname, username, password', 'length', 'min' => 3, 'max'=>150),
            array('email', 'email', 'validateIDN' => true),
            array('port', 'numerical', 'integerOnly'=>true),
            array('port', 'length', 'min'=> 2, 'max' => 5),
            array('protocol', 'in', 'range' => array_keys($this->getProtocolsArray())),
            array('customer_id', 'exist', 'className' => 'Customer', 'attributeName' => 'customer_id', 'allowEmpty' => true),
            array('locked', 'in', 'range' => array_keys($this->getYesNoOptions())),

            // since 1.3.5.5
            array('disable_authenticator, search_charset', 'length', 'max' => 50),
            array('delete_all_messages', 'in', 'range' => array_keys($this->getYesNoOptions())),
            //

            array('hostname, username, service, port, protocol, status, customer_id', 'safe', 'on' => 'search'),
        );

        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations = array(
            'deliveryServers'   => array(self::HAS_MANY, 'DeliveryServer', 'bounce_server_id'),
            'customer'          => array(self::BELONGS_TO, 'Customer', 'customer_id'),
        );

        return CMap::mergeArray($relations, parent::relations());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'server_id'     => Yii::t('servers', 'Server'),
            'customer_id'   => Yii::t('servers', 'Customer'),
            'hostname'      => Yii::t('servers', 'Hostname'),
            'username'      => Yii::t('servers', 'Username'),
            'password'      => Yii::t('servers', 'Password'),
            'email'         => Yii::t('servers', 'Email'),
            'service'       => Yii::t('servers', 'Service'),
            'port'          => Yii::t('servers', 'Port'),
            'protocol'      => Yii::t('servers', 'Protocol'),
            'validate_ssl'  => Yii::t('servers', 'Validate ssl'),
            'locked'        => Yii::t('servers', 'Locked'),

            // since 1.3.5.5
            'disable_authenticator' => Yii::t('servers', 'Disable authenticator'),
            'search_charset'        => Yii::t('servers', 'Search charset'),
            'delete_all_messages'   => Yii::t('servers', 'Delete all messages'),
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

        if (!empty($this->customer_id)) {
            if (is_numeric($this->customer_id)) {
                $criteria->compare('t.customer_id', $this->customer_id);
            } else {
                $criteria->with = array(
                    'customer' => array(
                        'joinType'  => 'INNER JOIN',
                        'condition' => 'CONCAT(customer.first_name, " ", customer.last_name) LIKE :name',
                        'params'    => array(
                            ':name'    => '%' . $this->customer_id . '%',
                        ),
                    )
                );
            }
        }

        $criteria->compare('t.hostname', $this->hostname, true);
        $criteria->compare('t.username', $this->username, true);
        $criteria->compare('t.email', $this->email, true);
        $criteria->compare('t.service', $this->service);
        $criteria->compare('t.port', $this->port);
        $criteria->compare('t.protocol', $this->protocol);
        $criteria->compare('t.status', $this->status);

        $criteria->addNotInCondition('t.status', array(self::STATUS_HIDDEN));

        return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize'  => $this->paginationOptions->getPageSize(),
                'pageVar'   => 'page',
            ),
            'sort'  => array(
                'defaultOrder'  => array(
                    'server_id' => CSort::SORT_DESC,
                ),
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BounceServer the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function afterValidate()
    {
        $this->settingsChanged = false;

        if (!$this->isNewRecord && !MW_IS_CLI) {
            if (empty($this->customer_id)) {
                $this->locked = self::TEXT_NO;
            }

            $model = self::model()->findByPk((int)$this->server_id);
            $keys = array('hostname', 'username', 'password', 'email', 'service', 'port', 'protocol', 'validate_ssl');
            foreach ($keys as $key) {
                if (!empty($this->$key) && $this->$key != $model->$key) {
                    $this->settingsChanged = true;
                    break;
                }
            }

            if ($this->settingsChanged) {
                if (!empty($this->deliveryServers)) {
                    $deliveryServers = $this->deliveryServers;
                    foreach ($deliveryServers as $server) {
                        $server->status = DeliveryServer::STATUS_INACTIVE;
                        $server->save(false);
                    }
                }
            }
        }

        return parent::afterValidate();
    }

    protected function beforeSave()
    {
        return parent::beforeSave();
    }

    protected function beforeDelete()
    {
        if (!$this->getCanBeDeleted()) {
            return false;
        }

        return parent::beforeDelete();
    }

    public function attributeHelpTexts()
    {
        $texts = array(
            'hostname'      => Yii::t('servers', 'The hostname of your IMAP/POP3 server.'),
            'username'      => Yii::t('servers', 'The username of your IMAP/POP3 server, usually something like you@domain.com.'),
            'password'      => Yii::t('servers', 'The password of your IMAP/POP3 server, used in combination with your username to authenticate your request.'),
            'email'         => Yii::t('servers', 'Only if your login username to this server is not an email address. If left empty, the username will be used.'),
            'service'       => Yii::t('servers', 'The type of your server.'),
            'port'          => Yii::t('servers', 'The port of your IMAP/POP3 server, usually for IMAP this is 143 and for POP3 it is 110. If you are using SSL, then the port for IMAP is 993 and for POP3 it is 995.'),
            'protocol'      => Yii::t('servers', 'The security protocol used to access this server. If unsure, select NOTLS.'),
            'validate_ssl'  => Yii::t('servers', 'When using SSL/TLS, whether to validate the certificate or not.'),
            'locked'        => Yii::t('servers', 'Whether this server is locked and assigned customer cannot change or delete it'),

            // since 1.3.5.5
            'disable_authenticator' => Yii::t('servers', 'If in order to establish the connection you need to disable an authenticator, you can type it here. I.E: GSSAPI.'),
            'search_charset'        => Yii::t('servers', 'Search charset, defaults to UTF-8 but might require to leave empty for some servers or explictly use US-ASCII.'),
            'delete_all_messages'   => Yii::t('servers', 'By default only messages related to the application are deleted. If this is enabled, all messages from the box will be deleted.'),
        );

        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }

    public function getServicesArray()
    {
        return array(
            'imap' => 'IMAP',
            'pop3' => 'POP3',
        );
    }

    public function getServiceName()
    {
        $services = $this->getServicesArray();
        return !empty($this->service) && !empty($services[$this->service]) ? $services[$this->service] : '---';
    }

    public function getProtocolsArray()
    {
        return array(
            'tls'   => 'TLS',
            'ssl'   => 'SSL',
            'notls' => 'NOTLS',
        );
    }

    public function getProtocolName()
    {
        $protocols = $this->getProtocolsArray();
        return !empty($this->protocol) && !empty($protocols[$this->protocol]) ? $protocols[$this->protocol] : Yii::t('app', 'Default');
    }

    public function getValidateSslOptions()
    {
        return array(
            self::TEXT_NO   => Yii::t('app', 'No'),
            self::TEXT_YES  => Yii::t('app', 'Yes'),
        );
    }

    public function getConnectionString()
    {
        $searchReplace = array(
            '[HOSTNAME]'        => $this->hostname,
            '[PORT]'            => $this->port,
            '[SERVICE]'         => $this->service,
            '[PROTOCOL]'        => $this->protocol,
            '[MAILBOX]'         => $this->mailBox,
            '[/VALIDATE_CERT]'  => '',
        );

        if (($this->protocol == 'ssl' || $this->protocol == 'tls') && $this->validate_ssl == self::TEXT_NO) {
            $searchReplace['[/VALIDATE_CERT]'] = '/novalidate-cert';
        }

        $connectionString = '{[HOSTNAME]:[PORT]/[SERVICE]/[PROTOCOL][/VALIDATE_CERT]}[MAILBOX]';
        $connectionString = str_replace(array_keys($searchReplace), array_values($searchReplace), $connectionString);
        return $connectionString;
    }

    public function getCanBeDeleted()
    {
        return !in_array($this->status, array(self::STATUS_CRON_RUNNING));
    }

    public function getCanBeUpdated()
    {
        return !in_array($this->status, array(self::STATUS_CRON_RUNNING, self::STATUS_HIDDEN));
    }

    public function getIsLocked()
    {
        return $this->locked === self::TEXT_YES;
    }

    public function getStatusesList()
    {
        return array(
            self::STATUS_ACTIVE         => ucfirst(Yii::t('app', self::STATUS_ACTIVE)),
            self::STATUS_CRON_RUNNING   => ucfirst(Yii::t('app', self::STATUS_CRON_RUNNING)),
            self::STATUS_INACTIVE       => ucfirst(Yii::t('app', self::STATUS_INACTIVE)),
            self::STATUS_DISABLED       => ucfirst(Yii::t('app', self::STATUS_DISABLED)),
        );
    }

    public function getImapOpenParams()
    {
        $params = array();
        if (!empty($this->disable_authenticator)) {
            $params['DISABLE_AUTHENTICATOR'] = $this->disable_authenticator;
        }
        return $params;
    }

    public function getSearchCharset()
    {
        return !empty($this->search_charset) ? strtoupper($this->search_charset) : null;
    }

    public function getDeleteAllMessages()
    {
        return (bool)(!empty($this->delete_all_messages) && $this->delete_all_messages == self::TEXT_YES);
    }

    public function testConnection()
    {
        $this->validate();
        if ($this->hasErrors()) {
            return false;
        }

        if (!CommonHelper::functionExists('imap_open')) {
            $this->addError('hostname', Yii::t('servers', 'The IMAP extension is missing from your PHP installation.'));
            return false;
        }

        $conn   = @imap_open($this->getConnectionString(), $this->username, $this->password, null, 1, $this->getImapOpenParams());
        $errors = imap_errors();
        $error  = null;

        if (!empty($errors) && is_array($errors)) {
            $errors = array_unique(array_values($errors));
            $error  = implode('<br />', $errors);

            // since 1.3.5.8
            if (stripos($error, 'insecure server advertised') !== false) {
                $error = null;
            }
        }

        if (empty($error) && empty($conn)) {
            $error = Yii::t('servers', 'Unknown error while opening the connection!');
        }

        // since 1.3.5.9
        if (!empty($error) && stripos($error, 'Mailbox is empty') !== false) {
            $error = null;
        }

        if (!empty($error)) {
            $this->addError('hostname', $error);
            return false;
        }

        $results = @imap_search($conn, "NEW", null, $this->getSearchCharset());
        $errors  = imap_errors();
        $error   = null;
        if (!empty($errors) && is_array($errors)) {
            $errors = array_unique(array_values($errors));
            $error = implode('<br />', $errors);
        }
        @imap_close($conn);

        // since 1.3.5.7
        if (!empty($error) && stripos($error, 'Mailbox is empty') !== false) {
            $error = null;
        }

        if (!empty($error)) {
            $this->addError('hostname', $error);
            return false;
        }

        return true;
    }

    public function saveStatus($status = null)
    {
        if (empty($this->server_id)) {
            return false;
        }
        if ($status) {
            $this->status = $status;
        }
        return Yii::app()->getDb()->createCommand()->update($this->tableName(), array('status' => $this->status), 'server_id = :sid', array(':sid' => (int)$this->server_id));
    }

    public function copy()
    {
        $copied = false;

        if ($this->isNewRecord) {
            return $copied;
        }

        $transaction = Yii::app()->db->beginTransaction();

        try {

            $server = clone $this;
            $server->isNewRecord  = true;
            $server->server_id    = null;
            $server->status       = self::STATUS_DISABLED;
            $server->date_added   = new CDbExpression('NOW()');
            $server->last_updated = new CDbExpression('NOW()');

            if (!$server->save(false)) {
                throw new CException($server->shortErrors->getAllAsString());
            }

            $transaction->commit();
            $copied = $server;
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        return $copied;
    }

    public function getIsDisabled()
    {
        return $this->status == self::STATUS_DISABLED;
    }

    public function getIsActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function enable()
    {
        if (!$this->getIsDisabled()) {
            return false;
        }
        $this->status = self::STATUS_ACTIVE;
        return $this->save(false);
    }

    public function disable()
    {
        if (!$this->getIsActive()) {
            return false;
        }
        $this->status = self::STATUS_DISABLED;
        return $this->save(false);
    }
}
