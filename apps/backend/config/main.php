<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Backend application main configuration file
 *
 * This file should not be altered in any way!
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

return array(
    'basePath'          => Yii::getPathOfAlias('backend'),
    'defaultController' => 'dashboard',

    'preload' => array(
        'backendSystemInit'
    ),

    // autoloading model and component classes
    'import' => array(
        'backend.components.*',
        'backend.components.db.*',
        'backend.components.db.ar.*',
        'backend.components.db.behaviors.*',
        'backend.components.utils.*',
        'backend.components.web.*',
        'backend.components.web.auth.*',
        'backend.models.*',
        'backend.models.customer-group.*',
    ),

    'components' => array(

        'urlManager' => array(
            'rules' => array(
                array('guest/forgot_password', 'pattern' => 'guest/forgot-password'),
                array('guest/reset_password', 'pattern' => 'guest/reset-password/<reset_key:([a-zA-Z0-9]{40})>'),

                array('article_categories/<action>', 'pattern' => 'article/categories/<action:(\w+)>/*'),
                array('article_categories/<action>', 'pattern' => 'article/categories/<action:(\w+)>'),

                array('list_page_type/<action>', 'pattern' => 'list-page-type/<action:(\w+)>/*'),
                array('list_page_type/<action>', 'pattern' => 'list-page-type/<action:(\w+)>'),
                array('list_page_type', 'pattern' => 'list-page-type'),

                array('delivery_servers/<action>', 'pattern' => 'delivery-servers/<action:(\w+)>/*'),
                array('delivery_servers/<action>', 'pattern' => 'delivery-servers/<action:(\w+)>'),
                array('delivery_servers', 'pattern' => 'delivery-servers'),

                array('bounce_servers/<action>', 'pattern' => 'bounce-servers/<action:(\w+)>/*'),
                array('bounce_servers/<action>', 'pattern' => 'bounce-servers/<action:(\w+)>'),
                array('bounce_servers', 'pattern' => 'bounce-servers'),

                array('feedback_loop_servers/<action>', 'pattern' => 'feedback-loop-servers/<action:(\w+)>/*'),
                array('feedback_loop_servers/<action>', 'pattern' => 'feedback-loop-servers/<action:(\w+)>'),
                array('feedback_loop_servers', 'pattern' => 'feedback-loop-servers'),

                array('settings/api_ip_access', 'pattern' => 'settings/api/ip-access'),
                array('settings/system_urls', 'pattern' => 'settings/system-urls'),
                array('settings/import_export', 'pattern' => 'settings/import-export'),
                array('settings/email_templates', 'pattern' => 'settings/email-templates/<type:([a-zA-Z0-9]+)>'),
                array('settings/email_templates', 'pattern' => 'settings/email-templates'),
                array('settings/email_blacklist_monitors', 'pattern' => 'settings/email-blacklist/monitors'),
                array('settings/email_blacklist', 'pattern' => 'settings/email-blacklist'),
                array('settings/campaign_attachments', 'pattern' => 'settings/campaigns/attachments'),
                array('settings/campaign_template_tags', 'pattern' => 'settings/campaigns/template-tags'),
                array('settings/campaign_exclude_ips_from_tracking', 'pattern' => 'settings/campaigns/exclude-ips-from-tracking'),
                array('settings/campaign_blacklist_words', 'pattern' => 'settings/campaigns/blacklist-words'),
                array('settings/campaign_template_engine', 'pattern' => 'settings/campaigns/template-engine'),
                array('settings/campaign_misc', 'pattern' => 'settings/campaigns/misc'),
                array('settings/campaign_options', 'pattern' => 'settings/campaign-options'),
                array('settings/customer_common', 'pattern' => 'settings/customers/common'),
                array('settings/customer_servers', 'pattern' => 'settings/customers/servers'),
                array('settings/customer_domains', 'pattern' => 'settings/customers/domains'),
                array('settings/customer_lists', 'pattern' => 'settings/customers/lists'),
                array('settings/customer_quota_counters', 'pattern' => 'settings/customers/quota-counters'),
                array('settings/customer_sending', 'pattern' => 'settings/customers/sending'),
                array('settings/customer_cdn', 'pattern' => 'settings/customers/cdn'),
                array('settings/customer_registration', 'pattern' => 'settings/customers/registration'),
                array('settings/customer_campaigns', 'pattern' => 'settings/customers/campaigns'),
                array('settings/monetization_orders', 'pattern' => 'settings/monetization/orders'),
                array('settings/monetization_invoices', 'pattern' => 'settings/monetization/invoices'),
                array('settings/redis_queue', 'pattern' => 'settings/queue/redis'),
                array('settings/spf_dkim', 'pattern' => 'settings/spf-dkim'),

                array('dashboard/delete_log', 'pattern' => 'dashboard/delete-log/id/<id:(\d+)>'),
                array('dashboard/delete_logs', 'pattern' => 'dashboard/delete-logs'),

                array('email_blacklist/delete_all', 'pattern' => 'email-blacklist/delete-all'),
                array('email_blacklist_monitors/<action>', 'pattern' => 'email-blacklist/monitors/<action:(\w+)>/*'),
                array('email_blacklist_monitors/<action>', 'pattern' => 'email-blacklist/monitors/<action:(\w+)>'),
                array('email_blacklist/<action>', 'pattern' => 'email-blacklist/<action:(\w+)>/*'),
                array('email_blacklist/<action>', 'pattern' => 'email-blacklist/<action:(\w+)>'),

                array('ip_location_services/<action>', 'pattern' => 'ip-location-services/<action:(index|create|update|delete)>'),

                array('misc/application_log', 'pattern' => 'misc/application-log'),
                array('misc/emergency_actions', 'pattern' => 'misc/emergency-actions'),
                array('misc/remove_sending_pid', 'pattern' => 'misc/remove-sending-pid'),
                array('misc/remove_bounce_pid', 'pattern' => 'misc/remove-bounce-pid'),
                array('misc/remove_fbl_pid', 'pattern' => 'misc/remove-fbl-pid'),
                array('misc/reset_campaigns', 'pattern' => 'misc/reset-campaigns'),
                array('misc/reset_bounce_servers', 'pattern' => 'misc/reset-bounce-servers'),
                array('misc/reset_fbl_servers', 'pattern' => 'misc/reset-fbl-servers'),

                array('misc/campaigns_delivery_logs', 'pattern' => 'misc/campaigns-delivery-logs/*'),
                array('misc/campaigns_delivery_logs', 'pattern' => 'misc/campaigns-delivery-logs'),
                array('misc/campaigns_bounce_logs', 'pattern' => 'misc/campaigns-bounce-logs/*'),
                array('misc/campaigns_bounce_logs', 'pattern' => 'misc/campaigns-bounce-logs'),
                array('misc/delivery_servers_usage_logs', 'pattern' => 'misc/delivery-servers-usage-logs/*'),
                array('misc/delivery_servers_usage_logs', 'pattern' => 'misc/delivery-servers-usage-logs'),
                array('misc/delete_delivery_temporary_errors', 'pattern' => 'misc/delete-delivery-temporary-errors'),
                array('misc/guest_fail_attempts', 'pattern' => 'misc/guest-fail-attempts/*'),
                array('misc/guest_fail_attempts', 'pattern' => 'misc/guest-fail-attempts'),
                array('misc/cron_jobs_list', 'pattern' => 'misc/cron-jobs-list/*'),
                array('misc/cron_jobs_list', 'pattern' => 'misc/cron-jobs-list'),

                array('customers/reset_sending_quota', 'pattern' => 'customers/reset-sending-quota/id/<id:(\d+)>'),
                array('customer_groups/reset_sending_quota', 'pattern' => 'customers/groups/reset-sending-quota/id/<id:(\d+)>'),
                array('customer_groups/<action>/*', 'pattern' => 'customers/groups/<action:(\w+)>/id/<id:(\d+)>'),
                array('customer_groups/<action>', 'pattern' => 'customers/groups/<action:(\w+)>'),
                array('customer_groups/index', 'pattern' => 'customers/groups'),
                array('customers_mass_emails/<action>/*', 'pattern' => 'customers/mass-emails/<action:(\w+)>/id/<id:(\d+)>'),
                array('customers_mass_emails/<action>', 'pattern' => 'customers/mass-emails/<action:(\w+)>'),
                array('customers_mass_emails/index', 'pattern' => 'customers/mass-emails'),

                array('customer_messages/index', 'pattern' => 'customers/messages'),
                array('customer_messages/<action>', 'pattern' => 'customers/messages/<action:(\w+)>/*'),
                array('customer_messages/<action>', 'pattern' => 'customers/messages/<action:(\w+)>'),

                array('customer_login_logs/index', 'pattern' => 'customers/login-logs'),
                array('customer_login_logs/<action>', 'pattern' => 'customers/login-logs/<action:(\w+)>/*'),
                array('customer_login_logs/<action>', 'pattern' => 'customers/login-logs/<action:(\w+)>'),

                array('payment_gateways/<action>', 'pattern' => 'payment-gateways/<action:(index|create|update|delete)>'),

                array('price_plans/<action>', 'pattern' => 'price-plans/<action:(\w+)>/*'),
                array('price_plans/<action>', 'pattern' => 'price-plans/<action>'),

                array('promo_codes/<action>', 'pattern' => 'promo-codes/<action:(\w+)>/*'),
                array('promo_codes/<action>', 'pattern' => 'promo-codes/<action>'),

                array('orders/delete_note', 'pattern' => 'orders/delete-note/id/<id:(\d+)>'),
                array('orders/email_invoice', 'pattern' => 'orders/email-invoice/id/<id:(\d+)>'),

                array('transactional_emails/<action>', 'pattern' => 'transactional-emails/<action:(\w+)>/*'),
                array('transactional_emails/<action>', 'pattern' => 'transactional-emails/<action>'),

                array('tracking_domains/<action>', 'pattern' => 'tracking-domains/<action:(\w+)>/*'),
                array('tracking_domains/<action>', 'pattern' => 'tracking-domains/<action:(\w+)>'),
                array('tracking_domains', 'pattern' => 'tracking-domains'),

                array('sending_domains/<action>', 'pattern' => 'sending-domains/<action:(\w+)>/*'),
                array('sending_domains/<action>', 'pattern' => 'sending-domains/<action:(\w+)>'),
                array('sending_domains', 'pattern' => 'sending-domains'),

                array('email_templates_gallery/<action>', 'pattern' => 'email-templates-gallery/<template_uid:([a-z0-9]+)>/<action:(update|delete|preview|copy)>'),
                array('email_templates_gallery/update_sort_order', 'pattern' => 'email-templates-gallery/update-sort-order'),
                array('email_templates_gallery/<action>', 'pattern' => 'email-templates-gallery/<action:(\w+)>/*'),
                array('email_templates_gallery/<action>', 'pattern' => 'email-templates-gallery/<action:(\w+)>'),
                array('email_templates_gallery', 'pattern' => 'email-templates-gallery'),

                array('company_types/<action>', 'pattern' => 'company-types/<action:(\w+)>/*'),
                array('company_types/<action>', 'pattern' => 'company-types/<action:(\w+)>'),
                array('company_types', 'pattern' => 'company-types'),

                array('user_groups/<action>/*', 'pattern' => 'users/groups/<action:(\w+)>/id/<id:(\d+)>'),
                array('user_groups/<action>', 'pattern' => 'users/groups/<action:(\w+)>'),
                array('user_groups/index', 'pattern' => 'users/groups'),

                array('campaign_abuse_reports/<action>', 'pattern' => 'campaign-abuse-reports/<action:(\w+)>/*'),
                array('campaign_abuse_reports/<action>', 'pattern' => 'campaign-abuse-reports/<action:(\w+)>'),
                array('campaign_abuse_reports', 'pattern' => 'campaign-abuse-reports'),

                array('campaigns/pause_unpause', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/pause-unpause'),
                array('campaigns/block_unblock', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/block-unblock'),
                array('campaigns/<action>', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/<action:(\w+)>'),
            ),
        ),

        'assetManager' => array(
            'basePath'  => Yii::getPathOfAlias('root.backend.assets.cache'),
            'baseUrl'   => AppInitHelper::getBaseUrl('assets/cache')
        ),

        'themeManager' => array(
            'class'     => 'common.components.managers.ThemeManager',
            'basePath'  => Yii::getPathOfAlias('root.backend.themes'),
            'baseUrl'   => AppInitHelper::getBaseUrl('themes'),
        ),

        'errorHandler' => array(
            'errorAction'   => 'guest/error',
        ),

        'session' => array(
            'class'             => 'system.web.CDbHttpSession',
            'connectionID'      => 'db',
            'sessionName'       => 'mwsid',
            'timeout'           => 7200,
            'sessionTableName'  => '{{session}}',
            'cookieParams'      => array(
                'httponly'      => true,
            ),
        ),

        'user' => array(
            'class'             => 'backend.components.web.auth.WebUser',
            'allowAutoLogin'    => true,
            'loginUrl'          => array('guest/index'),
            'returnUrl'         => array('dashboard/index'),
            'authTimeout'       => 7200,
            'identityCookie'    => array(
                'httpOnly'  => true,
            )
        ),

        'customer' => array(
            'class'             => 'customer.components.web.auth.WebCustomer',
            'allowAutoLogin'    => true,
            'authTimeout'       => 7200,
            'identityCookie'    => array(
                'httpOnly'  => true,
            )
        ),

        'backendSystemInit' => array(
            'class' => 'backend.components.init.BackendSystemInit',
        ),
    ),

    'modules' => array(),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // list of controllers where the user doesn't have to be logged in.
        'unprotectedControllers' => array('guest')
    ),
);
