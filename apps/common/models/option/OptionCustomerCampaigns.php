<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * OptionCustomerCampaigns
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */

class OptionCustomerCampaigns extends OptionBase
{
    // settings category
    protected $_categoryName = 'system.customer_campaigns';

    // maximum number of campaigns a customer can have, -1 is unlimited
    public $max_campaigns = -1;

    // multiple lists
    public $send_to_multiple_lists = 'no';

    // email footer
    public $email_footer;

    // verify sending domain
    public $must_verify_sending_domain = 'no';

    // can the customer delete his campaigns?
    public $can_delete_own_campaigns = 'yes';

    // how many subscribers should we load at once for each sending campaign
    public $subscribers_at_once = 300;

    // after how many emails we should send at once
    public $send_at_once = 100;

    // how many seconds should we pause bettwen the batches
    public $pause = 30;

    // how many emails should we deliver within a minute
    public $emails_per_minute = 100;

    // after what number of emails we should change the delivery server.
    public $change_server_at = 100;

    // max allowed bounce rate per campaign
    public $max_bounce_rate = -1;

    // can export campaign stats?
    public $can_export_stats = 'yes';
    
    // feedback id header format
    public $feedback_id_header_format = '[CAMPAIGN_UID]:[SUBSCRIBER_UID]:[LIST_UID]:[CUSTOMER_UID]';
    
    
    public function afterConstruct()
    {
        parent::afterConstruct();

        $options    = Yii::app()->options;
        $attributes = array(
            'subscribers_at_once'       => 300,
            'send_at_once'              => 100,
            'pause'                     => 30,
            'emails_per_minute'         => 100,
            'change_server_at'          => 100,
            'max_bounce_rate'           => -1,
        );

        foreach ($attributes as $key => $value) {
            if ($this->$key == $value) {
                $this->$key = (int)$options->get('system.cron.send_campaigns.' . $key, $this->$key);
            }
        }
    }

    public function rules()
    {
        $rules = array(
            array('max_campaigns, send_to_multiple_lists, must_verify_sending_domain, can_delete_own_campaigns, max_bounce_rate, can_export_stats', 'required'),
            array('max_campaigns', 'numerical', 'integerOnly' => true, 'min' => -1),
            array('max_bounce_rate', 'numerical', 'integerOnly' => true, 'min' => -1, 'max' => 100),
            array('send_to_multiple_lists, must_verify_sending_domain, can_delete_own_campaigns, can_export_stats', 'in', 'range' => array_keys($this->getYesNoOptions())),

            array('subscribers_at_once, send_at_once, pause, emails_per_minute, change_server_at', 'required'),
            array('subscribers_at_once', 'numerical', 'min' => 5, 'max' => 10000),
            array('send_at_once', 'numerical', 'min' => 0, 'max' => 10000),
            array('pause', 'numerical', 'min' => 0, 'max' => 30),
            array('emails_per_minute', 'numerical', 'min' => 0, 'max' => 10000),
            array('change_server_at', 'numerical', 'min' => 0, 'max' => 10000),
            array('feedback_id_header_format', 'length', 'max' => 500),
            array('feedback_id_header_format', '_validateFeedbackIdHeaderFormat'),
            
            array('email_footer', 'safe'),
        );

        return CMap::mergeArray($rules, parent::rules());
    }

    public function attributeLabels()
    {
        $labels = array(
            'max_campaigns'              => Yii::t('settings', 'Max. campaigns'),
            'send_to_multiple_lists'     => Yii::t('settings', 'Send to multiple lists'),
            'email_footer'               => Yii::t('settings', 'Email footer'),
            'must_verify_sending_domain' => Yii::t('settings', 'Must verify sending domain'),
            'can_delete_own_campaigns'   => Yii::t('settings', 'Delete own campaigns'),
            'can_export_stats'           => Yii::t('settings', 'Export stats'),
            'feedback_id_header_format'  => Yii::t('settings', 'Feedback-ID header format'),
            
            'subscribers_at_once' => Yii::t('settings', 'Subscribers at once'),
            'send_at_once'        => Yii::t('settings', 'Send at once'),
            'pause'               => Yii::t('settings', 'Pause'),
            'emails_per_minute'   => Yii::t('settings', 'Emails per minute'),
            'change_server_at'    => Yii::t('settings', 'Change server at'),
            'max_bounce_rate'     => Yii::t('settings', 'Max. bounce rate'),
        );

        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    public function attributePlaceholders()
    {
        $placeholders = array(
            'max_campaigns' => '',
        );

        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
    }

    public function attributeHelpTexts()
    {
        $texts = array(
            'max_campaigns'              => Yii::t('settings', 'Maximum number of campaigns a customer can have, set to -1 for unlimited'),
            'send_to_multiple_lists'     => Yii::t('settings', 'Whether customers are allowed to select multiple lists when creating a campaign'),
            'email_footer'               => Yii::t('settings', 'The email footer that should be appended to each campaign. It will be inserted exactly before the ending body tag and it can also contain template tags, which will pe parsed. Make sure you style it accordingly'),
            'must_verify_sending_domain' => Yii::t('settings', 'Whether customers must verify the domain name used in the FROM email address of a campaign'),
            'feedback_id_header_format'  => Yii::t('settings', 'The format of the Feedback-ID header.'),
            
            'can_delete_own_campaigns' => Yii::t('settings', 'Whether customers are allowed to delete their own campaigns'),
            'can_export_stats'         => Yii::t('settings', 'Whether customer can export campaign stats'),
    
            'subscribers_at_once' => Yii::t('settings', 'How many subscribers to process at once for each loaded campaign.'),
            'send_at_once'        => Yii::t('settings', 'How many emails should we send before pausing(this avoids server flooding and getting blacklisted). Set this to 0 to disable it.'),
            'pause'               => Yii::t('settings', 'How many seconds to sleep after sending a batch of emails.'),
            'emails_per_minute'   => Yii::t('settings', 'Limit the number of emails sent in one minute. This avoids getting blacklisted by various providers. Set this to 0 to disable it.'),
            'change_server_at'    => Yii::t('settings', 'After how many sent emails we should change the delivery server. This only applies if there are multiple delivery servers. Set this to 0 to disable it.'),
            'max_bounce_rate'     => Yii::t('settings', 'When a campaign reaches this bounce rate, it will be blocked. Set to -1 to disable this check or between 1 and 100 to set the percent of allowed bounce rate.'),
        );

        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }

    // since 1.3.6.6
    public function _validateFeedbackIdHeaderFormat($attribute, $params)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        
        $value = $this->$attribute;
        if (empty($value)) {
            return;
        }

        $values = explode(':', $value);
        if (count($values) != 4) {
            $this->addError($attribute, Yii::t('settings', 'The feedback-ID header format is invalid! Please refer to the {link}.', array(
                '{link}' => CHtml::link(Yii::t('settings', 'documentation'), 'https://support.google.com/mail/answer/6254652?hl=en', array('target' => '_blank')),
            )));
            return;
        }
    }
    
    // since 1.3.6.6
    public function getFeedbackIdFormatTagsInfo()
    {
        $tags = array(
            '[CAMPAIGN_UID]'    => Yii::t('settings', 'The campaign unique 13 characters id.'),
            '[SUBSCRIBER_UID]'  => Yii::t('settings', 'The subscriber unique 13 characters id.'),
            '[LIST_UID]'        => Yii::t('settings', 'The list unique 13 characters id.'),
            '[CUSTOMER_UID]'    => Yii::t('settings', 'The customer unique 13 characters id.'),
            '[CUSTOMER_NAME]'   => Yii::t('settings', 'The customer name, lowercased and urlified.'),
        );
        return Yii::app()->hooks->applyFilters('feedback_id_header_format_tags_info', $tags);
    }

    // since 1.3.6.6
    public function getFeedbackIdFormatTagsInfoHtml()
    {
        $out = array();
        foreach ($this->getFeedbackIdFormatTagsInfo() as $tag => $info) {
            $out[] = '&raquo; ' . sprintf('<b>%s</b>', $tag) . ' - ' . $info;
        }
        return $out;
    }
}
