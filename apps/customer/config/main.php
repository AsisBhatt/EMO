<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Customer application main configuration file
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
    'basePath' => Yii::getPathOfAlias('customer'),
    'defaultController' => 'dashboard',

    'preload' => array(
        'customerSystemInit'
    ),

    // autoloading model and component classes
    'import' => array(
        'customer.components.*',
        'customer.components.db.*',
        'customer.components.db.ar.*',
        'customer.components.db.behaviors.*',
        'customer.components.field-builder.*',
        'customer.components.utils.*',
        'customer.components.web.*',
        'customer.components.web.auth.*',
        'customer.models.*',
		'customer.modules.hybridauth.controllers.*',
    ),

    'components' => array(

        'urlManager' => array(
            'rules' => array(
                array('guest/forgot_password', 'pattern' => 'guest/forgot-password'),
                array('guest/reset_password', 'pattern' => 'guest/reset-password/<reset_key:([a-zA-Z0-9]{40})>'),
                array('guest/confirm_registration', 'pattern' => 'guest/confirm-registration/<key:([a-zA-Z0-9]{40})>'),

                array('lists/index', 'pattern' => 'lists/index/*'),

                array('list_subscribers/index', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/subscribers'),
                array('list_subscribers/create', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/subscribers/create'),
                array('list_subscribers/bulk_action', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/subscribers/bulk-action'),
                array('list_subscribers/<action>', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/subscribers/<subscriber_uid:([a-z0-9]+)>/<action:(update|subscribe|unsubscribe|disable|delete|campaigns)>'),
                array('list_segments/index', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/segments'),
                array('list_segments/create', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/segments/create'),
                array('list_segments/<action>', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/segments/<segment_uid:([a-z0-9]+)>/<action:(update|delete|copy|subscribers)>'),
                array('list_fields/index', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/fields'),
                array('list_page/index', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/page/<type:([a-zA-Z0-9_\-]+)>'),
                array('list_forms/index', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/forms'),

                array('list_import/index', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/import'),
                array('list_import/<action>', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/import/<action>'),
                array('list_export/index', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/export'),
                array('list_export/<action>', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/export/<action>'),
                array('list_segments_export/index', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/segments/<segment_uid:([a-z0-9]+)>/export'),
                array('list_segments_export/<action>', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/segments/<segment_uid:([a-z0-9]+)>/export/<action>'),

                array('lists_tools/<action>', 'pattern' => 'lists/tools/<action>'),

                array('list_tools/copy_subscribers', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/tools/copy-subscribers'),
                array('list_tools/<action>', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/tools/<action>'),
                
                array('lists/all_subscribers', 'pattern' => 'lists/all-subscribers/*'),
                array('lists/all_subscribers', 'pattern' => 'lists/all-subscribers'),
                array('lists/<action>', 'pattern' => 'lists/<list_uid:([a-z0-9]+)>/<action:([a-z0-9]+)>'),
                array('templates/gallery_import', 'pattern' => 'templates/gallery-import/<template_uid:([a-z0-9]+)>'),
                array('templates/update_sort_order', 'pattern' => 'templates/update-sort-order'),
                array('templates/<action>', 'pattern' => 'templates/<template_uid:([a-z0-9]+)>/<action:(update|test|delete|preview|copy)>'),

                array('campaign_reports/open_by_subscriber', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports/open-by-subscriber/<subscriber_uid:([a-z0-9]+)>'),
                array('campaign_reports/click_by_subscriber_unique', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports/click-by-subscriber-unique/<subscriber_uid:([a-z0-9]+)>'),
                array('campaign_reports/click_by_subscriber', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports/click-by-subscriber/<subscriber_uid:([a-z0-9]+)>'),
                array('campaign_reports/open_unique', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports/open-unique'),
                array('campaign_reports/click_url', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports/click-url'),
                array('campaign_reports/forward_friend', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports/forward-friend'),
                array('campaign_reports/abuse_reports', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports/abuse-reports'),
                array('campaign_reports/<action>', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports/<action:(\w+)>/*'),
                array('campaign_reports/<action>', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports/<action:(\w+)>'),

                array('campaign_groups/<action>', 'pattern' => 'campaigns/groups/<group_uid:([a-z0-9]+)>/<action:(\w+)>'),
                array('campaign_groups/<action>', 'pattern' => 'campaigns/groups/<action:(\w+)>'),
                array('campaign_groups/index', 'pattern' => 'campaigns/groups'),

                array('campaign_tags/<action>', 'pattern' => 'campaigns/tags/<tag_uid:([a-z0-9]+)>/<action:(\w+)>'),
                array('campaign_tags/<action>', 'pattern' => 'campaigns/tags/<action:(\w+)>'),
                array('campaign_tags/index', 'pattern' => 'campaigns/tags'),

                array('messages/view', 'pattern' => 'messages/<message_uid:([a-z0-9]+)>/view'),
                array('messages/delete', 'pattern' => 'messages/<message_uid:([a-z0-9]+)>/delete'),
                array('messages/mark_all_as_seen', 'pattern' => 'messages/mark-all-as-seen'),

                array('campaigns/pause_unpause', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/pause-unpause'),
                array('campaigns/merge_lists', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/merge-lists'),
                array('campaigns/<action>', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/<action:(\w+)>'),

                array('api_keys/<action>', 'pattern' => 'api-keys/<action>/*'),
                array('api_keys/<action>', 'pattern' => 'api-keys/<action>'),

                array('dashboard/delete_log', 'pattern' => 'dashboard/delete-log/id/<id:(\d+)>'),
                array('dashboard/delete_logs', 'pattern' => 'dashboard/delete-logs'),

                array('campaign_reports_export/basic', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports-export/basic'),
                array('campaign_reports_export/click_url', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports-export/click-url'),
                array('campaign_reports_export/click_by_subscriber', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports-export/click-by-subscriber/<subscriber_uid:([a-z0-9]+)>'),
                array('campaign_reports_export/click_by_subscriber_unique', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports-export/click-by-subscriber-unique/<subscriber_uid:([a-z0-9]+)>'),
                array('campaign_reports_export/<action>', 'pattern' => 'campaigns/<campaign_uid:([a-z0-9]+)>/reports-export/<action:(\w+)>'),

                array('delivery_servers/<action>', 'pattern' => 'delivery-servers/<action:(\w+)>/*'),
                array('delivery_servers/<action>', 'pattern' => 'delivery-servers/<action:(\w+)>'),
                array('delivery_servers', 'pattern' => 'delivery-servers'),

                array('bounce_servers/<action>', 'pattern' => 'bounce-servers/<action:(\w+)>/*'),
                array('bounce_servers/<action>', 'pattern' => 'bounce-servers/<action:(\w+)>'),
                array('bounce_servers', 'pattern' => 'bounce-servers'),

                array('feedback_loop_servers/<action>', 'pattern' => 'feedback-loop-servers/<action:(\w+)>/*'),
                array('feedback_loop_servers/<action>', 'pattern' => 'feedback-loop-servers/<action:(\w+)>'),
                array('feedback_loop_servers', 'pattern' => 'feedback-loop-servers'),

                array('price_plans/order_detail', 'pattern' => 'price-plans/orders/<order_uid:([a-z0-9]+)>'),
                array('price_plans/order_pdf', 'pattern' => 'price-plans/orders/<order_uid:([a-z0-9]+)>/pdf'),
                array('price_plans/email_invoice', 'pattern' => 'price-plans/orders/<order_uid:([a-z0-9]+)>/email-invoice'),
                array('price_plans/<action>', 'pattern' => 'price-plans/<action:(\w+)>/*'),
                array('price_plans/<action>', 'pattern' => 'price-plans/<action>'),

                array('tracking_domains/<action>', 'pattern' => 'tracking-domains/<action:(\w+)>/*'),
                array('tracking_domains/<action>', 'pattern' => 'tracking-domains/<action:(\w+)>'),
                array('tracking_domains', 'pattern' => 'tracking-domains'),

                array('sending_domains/<action>', 'pattern' => 'sending-domains/<action:(\w+)>/*'),
                array('sending_domains/<action>', 'pattern' => 'sending-domains/<action:(\w+)>'),
                array('sending_domains', 'pattern' => 'sending-domains'),

                array('email_blacklist/delete_all', 'pattern' => 'email-blacklist/delete-all'),
                array('email_blacklist/<action>', 'pattern' => 'email-blacklist/<action:(\w+)>/*'),
                array('email_blacklist/<action>', 'pattern' => 'email-blacklist/<action:(\w+)>'),
            ),
        ),
		'hybridAuth'=>array(
            'class'=>'customer.extensions.widgets.hybridAuth.CHybridAuth',
            'enabled'=>true, // enable or disable this component
            'config'=>array(
                 "base_url" => "https://new2managevip.beelift.com/customer/index.php/hybridauth/endpoint", 
                 "providers" => array(
                       /*"Google" => array(
                            "enabled" => false,
                            "keys" => array("id" => "", "secret" => ""),
                        ),*/
                       "Facebook" => array(
                            "enabled" => true,
                            "keys" => array("id" => "132559594135583", "secret" => "7a83558e4e9dfe9c62fab0128bb90b56"),
                        ),
                       /*"Twitter" => array(
                            "enabled" => false,
                            "keys" => array("key" => "", "secret" => "")
                       ),*/
                 ),
                 "debug_mode" => false,
                 "debug_file" => "",
             ),
         ),//end hybridAuth

        'assetManager' => array(
            'basePath'  => Yii::getPathOfAlias('root.customer.assets.cache'),
            'baseUrl'   => AppInitHelper::getBaseUrl('assets/cache')
        ),

        'themeManager' => array(
            'class'     => 'common.components.managers.ThemeManager',
            'basePath'  => Yii::getPathOfAlias('root.customer.themes'),
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
            'authTimeout'       => 7200,
            'identityCookie'    => array(
                'httpOnly'      => true,
            )
        ),

        'customer' => array(
            'class'             => 'customer.components.web.auth.WebCustomer',
            'allowAutoLogin'    => true,
            'loginUrl'          => array('guest/index'),
            'returnUrl'         => array('dashboard/index'),
            'authTimeout'       => 7200,
            'identityCookie'    => array(
                'httpOnly'      => true,
            )
        ),

        'customerSystemInit' => array(
            'class' => 'customer.components.init.CustomerSystemInit',
        ),
    ),

    'modules' => array(
		'hybridauth' => array(
            'baseUrl' => 'http://new2managevip.beelift.com/customer/index.php/hybridauth', 
			//'baseUrl' => 'http://'. $_SERVER['SERVER_NAME'] . '/newmanagevip/customer/index.php/hybridauth', 
            'withYiiUser' => false, // Set to true if using yii-user
            "providers" => array ( 
                "openid" => array (
                    "enabled" => true
                ),
 
                "yahoo" => array ( 
                    "enabled" => true 
                ),
 
                "google" => array ( 
                    "enabled" => true,
                    "keys"    => array ( "id" => "", "secret" => "" ),
                    "scope"   => ""
                ),
 
                "facebook" => array ( 
                    "enabled" => true,
                    "keys"    => array ( "id" => "132559594135583", "secret" => "7a83558e4e9dfe9c62fab0128bb90b56" ),
                    "scope"   => "email,publish_stream", 
                    "display" => "" 
                ),
 
                "twitter" => array ( 
                    "enabled" => true,
                    "keys"    => array ( "key" => "XJ875zIpKEd2afULmNFM8qanF", "secret" => "xUuitejH2OAUbxBpnmHibt3400zb5ecDIbL4rD63YJlPss9PJ8" ) 
                )
            )
        ),
	),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        // list of controllers where the user doesn't have to be logged in.
        'unprotectedControllers' => array('guest')
    ),
);
