<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * LeftSideNavigationWidget
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class LeftSideNavigationWidget extends CWidget
{
    /**
     * @return array
     */
    public function getMenuItems()
    {

        $controller = $this->controller;
        $route      = $controller->route;
        $user       = Yii::app()->user->getModel();
		
        $supportUrl = Yii::app()->options->get('system.common.support_url');
        if ($supportUrl === null) {
            $supportUrl = MW_SUPPORT_KB_URL;
        }

        $menuItems = array(
            // 'support' => array(
                // 'name'        => Yii::t('app', 'Support'),
                // 'icon'        => 'glyphicon-question-sign',
                // 'active'      => '',
                // 'route'       => $supportUrl,
                // 'linkOptions' => array('target' => '_blank'),
            // ),
            'dashboard' => array(
                'name'      => Yii::t('app', 'Dashboard'),
                'icon'      => 'icon-home',
                'active'    => 'dashboard',
                'route'     => array('dashboard/index'),
				//'route'     => null,
				'linkOptions' => array('class' => 'nav-link home_selected'),
            ),
			'social_media' => array(
				'name'		=> Yii::t('app', 'Social Media Marketing'),
				'icon'		=> 'icon-share',
				'active'	=> 'socialsetting',
				'route'		=> null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
				'items'		=> array(
					array('url' => array('socialmedia/reports'), 'label' => Yii::t('app', 'Reports'), 'active' => strpos($route, 'smsreports') === 0),
					array('url' => array('socialmedia/template'), 'label' => Yii::t('app', 'Templates'), 'active' => strpos($route, 'sms_template/index') === 0),
					array('url' => array('socialsetting/index'), 'label' => Yii::t('app', 'Social API Settings'), 'active' => strpos($route, 'socialsetting') === 0),
					array('url' => 'javascript:;', 'label' => Yii::t('app', 'Social Media Template'), 'active' => strpos($route, 'social_media_template') === 0),
				),
				
			),
			'email' => array(
				'name' 		=> Yii::t('app', 'Email Marketing'),
				'icon' 		=> 'icon-envelope',
				'active' 	=> array('settings/index', 'socialsetting/index', 'settings/system_urls', 'settings/import_export', 'misc/campaigns_bounce_logs', 'languages/index'),
				'route'		=> null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
				'items'		=> array(
					array('url' => array('campaigns/index'), 'label' => Yii::t('app', 'Campaigns'), 'active' => strpos($route, 'campaigns') === 0),
                    array('url' => array('customers_mass_emails/index'), 'label' => Yii::t('app', 'Mass emails'), 'active' => strpos($route, 'customers_mass_emails') === 0),
					array('url' => array('email/reports'), 'label' => Yii::t('app', 'Reports'), 'active' => strpos($route, 'email/reports') === 0),
					'manage_email_template' => array(
						'label'		=> Yii::t('app', 'Manage Email Template'),
						'icon'		=> 'icon-share',
						'active'	=> array('email','email_templates_gallery'),
						'route'		=> null,
						'linkOptions' => array('class' => 'nav-toggle'),
						'items'		=> array(
							array('url' => array('email-templates-gallery/index'), 'label' => Yii::t('app', 'Email templates gallery'), 'active' => strpos($route, 'email_templates_gallery') === 0),
						),
					),
					'email_marketing_setting' => array(
						'label'		=> Yii::t('app', 'Setting'),
						'icon'      => 'icon-settings',
						'active'	=> array('settings/index','socialsetting/index', 'settings/system_urls', 'settings/import_export','settings/email_templates','settings/cron','settings/email_blacklist', 'settings/campaign_', 'settings/customer_', 'settings/api_ip_access', 'settings/monetization', 'settings/customization', 'settings/cdn', 'settings/spf_dkim', 'settings/redis_queue'),
						'route'		=> null,
						'linkOptions' => array('class' => 'nav-toggle'),
						'items'		=> array(
							array('url' => array('settings/index'), 'label' => Yii::t('app', 'Common'), 'active' => strpos($route, 'settings/index') === 0),
							array('url' => array('socialsetting/index'), 'label' => Yii::t('app', 'Social API Settings'), 'active' => strpos($route, 'socialsetting/index') === 0),
							//array('url' => array('smssetting/index'), 'label' => Yii::t('app', 'SMS API Settings'), 'active' => strpos($route, 'smssetting/index') === 0),
							array('url' => array('settings/system_urls'), 'label' => Yii::t('app', 'System urls'), 'active' => strpos($route, 'settings/system_urls') === 0),
							array('url' => array('settings/import_export'), 'label' => Yii::t('app', 'Import/Export'), 'active' => strpos($route, 'settings/import_export') === 0),
							array('url' => array('settings/email_templates'), 'label' => Yii::t('app', 'Email templates'), 'active' => strpos($route, 'settings/email_templates') === 0),
							array('url' => array('settings/cron'), 'label' => Yii::t('app', 'Cron'), 'active' => strpos($route, 'settings/cron') === 0),
							array('url' => array('settings/email_blacklist'), 'label' => Yii::t('app', 'Email blacklist'), 'active' => strpos($route, 'settings/email_blacklist') === 0),
							array('url' => array('settings/campaign_attachments'), 'label' => Yii::t('app', 'Campaigns'), 'active' => strpos($route, 'settings/campaign_') === 0),
							array('url' => array('settings/customer_common'), 'label' => Yii::t('app', 'Customers'), 'active' => strpos($route, 'settings/customer_') === 0),
							array('url' => array('settings/api_ip_access'), 'label' => Yii::t('app', 'Api'), 'active' => strpos($route, 'settings/api_ip_access') === 0),
							array('url' => array('settings/monetization'), 'label' => Yii::t('app', 'Monetization'), 'active' => strpos($route, 'settings/monetization') === 0),
							array('url' => array('settings/customization'), 'label' => Yii::t('app', 'Customization'), 'active' => strpos($route, 'settings/customization') === 0),
							array('url' => array('settings/cdn'), 'label' => Yii::t('app', 'CDN'), 'active' => strpos($route, 'settings/cdn') === 0),
							array('url' => array('settings/spf_dkim'), 'label' => Yii::t('app', 'SPF/DKIM'), 'active' => strpos($route, 'settings/spf_dkim') === 0),
							array('url' => array('settings/redis_queue'), 'label' => Yii::t('app', 'Queue'), 'active' => strpos($route, 'settings/redis_queue') === 0),
						),
					),
					'email_miscellaneous' => array(
						'label'		=> Yii::t('app', 'Miscellaneous'),
						'icon'		=> 'glyphicon-bookmark',
						'active'	=> array('misc/campaigns_delivery_logs','misc/campaigns_bounce_logs', 'campaign_abuse_reports/index', 'transactional_emails', 'misc/delivery_servers_usage_logs', 'company_types', 'misc/application_log', 'misc/emergency_actions', 'misc/guest_fail_attempts', 'misc/cron_jobs_list', 'misc/phpinfo'),
						'route'		=> null,
						'linkOptions' => array('class' => 'nav-toggle'),
						'items'		=> array(
							array('url' => array('misc/campaigns_delivery_logs'), 'label' => Yii::t('app', 'Campaigns delivery logs'), 'active' => strpos($route, 'misc/campaigns_delivery_logs') === 0),
							array('url' => array('misc/campaigns_bounce_logs'), 'label' => Yii::t('app', 'Campaigns bounce logs'), 'active' => strpos($route, 'misc/campaigns_bounce_logs') === 0),
							array('url' => array('campaign_abuse_reports/index'), 'label' => Yii::t('app', 'Campaign abuse reports'), 'active' => strpos($route, 'campaign_abuse_reports/index') === 0),
							array('url' => array('transactional_emails/index'), 'label' => Yii::t('app', 'Transactional emails'), 'active' => strpos($route, 'transactional_emails') === 0),
							array('url' => array('misc/delivery_servers_usage_logs'), 'label' => Yii::t('app', 'Delivery servers usage logs'), 'active' => strpos($route, 'misc/delivery_servers_usage_logs') === 0),
							array('url' => array('company_types/index'), 'label' => Yii::t('app', 'Company types'), 'active' => strpos($route, 'company_types') === 0),
							array('url' => array('misc/application_log'), 'label' => Yii::t('app', 'Application log'), 'active' => strpos($route, 'misc/application_log') === 0),
							array('url' => array('misc/emergency_actions'), 'label' => Yii::t('app', 'Emergency actions'), 'active' => strpos($route, 'misc/emergency_actions') === 0),
							array('url' => array('misc/guest_fail_attempts'), 'label' => Yii::t('app', 'Guest fail attempts'), 'active' => strpos($route, 'misc/guest_fail_attempts') === 0),
							array('url' => array('misc/cron_jobs_list'), 'label' => Yii::t('app', 'Cron jobs list'), 'active' => strpos($route, 'misc/cron_jobs_list') === 0),
							array('url' => array('misc/phpinfo'), 'label' => Yii::t('app', 'PHP info'), 'active' => strpos($route, 'misc/phpinfo') === 0),
						),
					),
					'manage' => array(
						'label'		=> Yii::t('app', 'Manage'),
						'icon'		=> 'icon-globe',
						'active'	=> array('languages', 'ip_location_services', 'countries'),
						'route'		=> null,
						'linkOptions' => array('class' => 'nav-toggle', 'zones', 'payment_gateway', 'price_plans', 'orders', 'promo_codes', 'currencies', 'taxes', 'articles', 'article_categories'),
						'items'		=> array(
							array('url' => array('languages/index'), 'label' => Yii::t('app', 'Languages'), 'active' => strpos($route, 'languages/index') === 0),
							array('url' => array('ip_location_services/index'), 'label' => Yii::t('app', 'Ip location services'), 'active' => strpos($route, 'ip_location_services') === 0),
							array('url' => array('countries/index'), 'label' => Yii::t('app', 'Countries'), 'active' => strpos($route, 'countries') === 0),
							array('url' => array('zones/index'), 'label' => Yii::t('app', 'Zones'), 'active' => strpos($route, 'zones') === 0),
							array('url' => array('payment_gateways/index'), 'label' => Yii::t('app', 'Payment gateways'), 'active' => strpos($route, 'payment_gateway') === 0),
							array('url' => array('price_plans/index'), 'label' => Yii::t('app', 'Price plans'), 'active' => strpos($route, 'price_plans') === 0),
							array('url' => array('orders/index'), 'label' => Yii::t('app', 'Orders'), 'active' => strpos($route, 'orders') === 0),
							array('url' => array('promo_codes/index'), 'label' => Yii::t('app', 'Promo codes'), 'active' => strpos($route, 'promo_codes') === 0),
							array('url' => array('currencies/index'), 'label' => Yii::t('app', 'Currencies'), 'active' => strpos($route, 'currencies') === 0),
							array('url' => array('taxes/index'), 'label' => Yii::t('app', 'Taxes'), 'active' => strpos($route, 'taxes') === 0),
							array('url' => array('articles/index'), 'label' => Yii::t('app', 'View all articles'), 'active' => strpos($route, 'articles') === 0),
							array('url' => array('article_categories/index'), 'label' => Yii::t('app', 'View all categories'), 'active' => strpos($route, 'article_categories') === 0),
						),
						
					),
					'manage_email_servers' => array(
						'label'		=> Yii::t('app', 'Email Server'),
						'icon'		=> 'icon-share',
						'active'	=> array('delivery_servers', 'bounce_servers', 'feedback_loop_servers'),
						'route'		=> null,
						'linkOptions' => array('class' => 'nav-toggle'),
						'items'		=> array(
							array('url' => array('delivery_servers/index'), 'label' => Yii::t('app', 'Delivery servers'), 'active' => strpos($route, 'delivery_servers') === 0),
							array('url' => array('bounce_servers/index'), 'label' => Yii::t('app', 'Bounce servers'), 'active' => strpos($route, 'bounce_servers') === 0),
							array('url' => array('feedback_loop_servers/index'), 'label' => Yii::t('app', 'Feedback loop servers'), 'active' => strpos($route, 'feedback_loop_servers') === 0),
						),
					),
					'manage_domains' => array(
						'label'		 => Yii::t('app', 'Email Domains'),
						'icon'		 => 'icon-share',
						//'active'	 => array('sending_domains', 'tracking_domains'),
						'route' 	 => null,
						'linkOptions' => array('class' => 'nav-toggle'),
						'items' 	 => array(
							array('url' => array('sending_domains/index'), 'label' => Yii::t('app', 'Sending domains'), 'active' => strpos($route, 'sending_domains') === 0),
							array('url' => array('tracking_domains/index'), 'label' => Yii::t('app', 'Tracking domains'), 'active' => strpos($route, 'tracking_domains') === 0),
						),
					),
					'email_blacklist' => array(
						'label'		=> Yii::t('app', 'Blacklist'),
						'icon'		=> 'icon-bubbles',
						//'active'	=> array('email_blacklist', 'email-templates-gallery'),
						'route'		=> null,
						'linkOptions' => array('class' => 'nav-toggle'),
						'items'		=> array(
							array('url' => array('email_blacklist/index'), 'label' => Yii::t('app', 'Email blacklist'), 'active' => strpos($route,  'email_blacklist') === 0),
							array('url' => array('email_blacklist_monitors/index'), 'label' => Yii::t('app', 'Blacklist monitors'), 'active' => strpos($route, 'email_blacklist_monitors') === 0),
						),
					),
					/*'email_plans' => array(
						'label'		=> Yii::t('app', 'Plans'),
						'icon'		=> 'icon-home',
						//'active'	=> array('plan_create', 'plan_index', 'plan_type'),
						'route'		=> null,
						'linkOptions' => array('class' => 'nav-toggle'),
						'items' 	=> array(
							array('url' => array('plan/create'), 'label' => Yii::t('app', 'Create new Plans'), 'active' => strpos($route, 'plan_create') === 0),
							array('url' => array('plan/index'), 'label' => Yii::t('app', 'List Plans'), 'active' => strpos($route, 'plan_index') === 0),
							array('url' => array('list_page_type/index'), 'label' => Yii::t('app', 'List page types'), 'active' => strpos($route,'plan_type') === 0),
						),
					),*/
				),
			),
			'sms' => array(
				'name'		=> Yii::t('app', 'SMS Marketing'),
				'icon'		=> 'icon-bubbles',
				'active'	=> array('sms','smssetting','plan'),
				'route'		=> null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
				'items'		=> array(
					array('url' => array('sms/reports'), 'label' => Yii::t('app', 'Reports'), 'active' => strpos($route, 'smsreports') === 0),
					array('url' => array('sms_template/index'), 'label' => Yii::t('app', 'Templates'), 'active' => strpos($route, 'sms_template/index') === 0),
					array('url' => array('smssetting/index'), 'label' => Yii::t('app', 'SMS API Settings'), 'active' => strpos($route, 'smssetting') === 0),
					array('url' => array('plan/index'), 'label' => Yii::t('app', 'List Plans'), 'active' => strpos($route, 'plan/index') === 0),
                    array('url' => array('plan/create'), 'label' => Yii::t('app', 'Create new Plans'), 'active' => strpos($route, 'plan/create') === 0),
					array('url' => array('autoreply_template/index'), 'label' => Yii::t('app', 'Auto Reply Template'), 'active' => strpos($route, 'autoreply_template/create') === 0),
					array('url' => array('text_to_pay/index'), 'label' => Yii::t('app', 'Text To Pay'), 'active' => strpos($route, 'text_to_pay/index') === 0),
				),
			),
			'customer_&_user' => array(
				'name'		=> Yii::t('app', 'Customers & User'),
				'icon'		=> 'icon-user',
				'active'	=> array('users', 'user_groups', 'customers', 'customer_groups', 'campaigns', 'customers_mass_emails', 'customer_messages', 'customer_login_logs'),
				'route'		=> null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
				'items'		=> array(
					array('url' => array('users/index'), 'label' => Yii::t('app', 'Users'), 'active' => strpos($route, 'users') === 0),
                    array('url' => array('user_groups/index'), 'label' => Yii::t('app', 'Groups'), 'active' => strpos($route, 'user_groups') === 0),
					array('url' => array('customers/index'), 'label' => Yii::t('app', 'Merchant Customer'), 'active' => strpos($route, 'customers') === 0 && strpos($route, 'customers_mass_emails') === false),
                    array('url' => array('customer_groups/index'), 'label' => Yii::t('app', 'Merchant Payment Plans'), 'active' => strpos($route, 'customer_groups') === 0),
                    array('url' => array('customer_messages/index'), 'label' => Yii::t('app', 'Messages'), 'active' => strpos($route, 'customer_messages') === 0),
                    array('url' => array('customer_login_logs/index'), 'label' => Yii::t('app', 'Login logs'), 'active' => strpos($route, 'customer_login_logs') === 0),
					array('url' => array('activity_logs/index'), 'label' => Yii::t('app', 'Activity logs'), 'active' => strpos($route, 'activity_logs/index') === 0),
				),
			),
			'management_section' => array(
				'name'		=> Yii::t('app', 'Management'),
				'icon'		=> 'fa fa-tasks',
				'active'	=> 'management',
				'route'     => array('management/index'),
				'linkOptions'	=> array('class' => 'nav-link nav-toggle'),
			),
			/*'manage' => array(
				'name' 		=> 'Manage',
				'icon'		=> 'icon-globe',
				'active'    => array('manage', 'theme', 'languages', 'countries', 'ip_location_services', 'zones','payment_gateway', 'price_plans', 'orders', 'promo_codes', 'currencies', 'taxes', 'articles', 'article_categories'),
				'route'		=> null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
				'items'		=> array(
					//array('url' => array('theme/index'), 'label' => Yii::t('app', 'Themes'), 'active' => strpos($route, 'theme') === 0),
                    array('url' => array('languages/index'), 'label' => Yii::t('app', 'Languages'), 'active' => strpos($route, 'languages') === 0),
					array('url' => array('ip_location_services/index'), 'label' => Yii::t('app', 'Ip location services'), 'active' => strpos($route, 'ip_location_services') === 0),
                    array('url' => array('countries/index'), 'label' => Yii::t('app', 'Countries'), 'active' => strpos($route, 'countries') === 0),
                    array('url' => array('zones/index'), 'label' => Yii::t('app', 'Zones'), 'active' => strpos($route, 'zones') === 0),
					array('url' => array('payment_gateways/index'), 'label' => Yii::t('app', 'Payment gateways'), 'active' => strpos($route, 'payment_gateway') === 0),
                    array('url' => array('price_plans/index'), 'label' => Yii::t('app', 'Price plans'), 'active' => strpos($route, 'price_plans') === 0),
                    array('url' => array('orders/index'), 'label' => Yii::t('app', 'Orders'), 'active' => strpos($route, 'orders') === 0),
                    array('url' => array('promo_codes/index'), 'label' => Yii::t('app', 'Promo codes'), 'active' => strpos($route, 'promo_codes') === 0),
                    array('url' => array('currencies/index'), 'label' => Yii::t('app', 'Currencies'), 'active' => strpos($route, 'currencies') === 0),
                    array('url' => array('taxes/index'), 'label' => Yii::t('app', 'Taxes'), 'active' => strpos($route, 'taxes') === 0),
					array('url' => array('articles/index'), 'label' => Yii::t('app', 'View all articles'), 'active' => strpos($route, 'articles') === 0),
                    array('url' => array('article_categories/index'), 'label' => Yii::t('app', 'View all categories'), 'active' => strpos($route, 'article_categories') === 0),
				),
			),
			'settings' => array(
                'name'      => Yii::t('app', 'Settings'),
                'icon'      => 'icon-settings',
                'active'    => 'settings',
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('settings/index'), 'label' => Yii::t('app', 'Common'), 'active' => strpos($route, 'settings/index') === 0),
					array('url' => array('socialsetting/index'), 'label' => Yii::t('app', 'Social API Settings'), 'active' => strpos($route, 'socialsetting/index') === 0),
					//array('url' => array('smssetting/index'), 'label' => Yii::t('app', 'SMS API Settings'), 'active' => strpos($route, 'smssetting/index') === 0),
                    array('url' => array('settings/system_urls'), 'label' => Yii::t('app', 'System urls'), 'active' => strpos($route, 'settings/system_urls') === 0),
                    array('url' => array('settings/import_export'), 'label' => Yii::t('app', 'Import/Export'), 'active' => strpos($route, 'settings/import_export') === 0),
                    array('url' => array('settings/email_templates'), 'label' => Yii::t('app', 'Email templates'), 'active' => strpos($route, 'settings/email_templates') === 0),
                    array('url' => array('settings/cron'), 'label' => Yii::t('app', 'Cron'), 'active' => strpos($route, 'settings/cron') === 0),
                    array('url' => array('settings/email_blacklist'), 'label' => Yii::t('app', 'Email blacklist'), 'active' => strpos($route, 'settings/email_blacklist') === 0),
                    array('url' => array('settings/campaign_attachments'), 'label' => Yii::t('app', 'Campaigns'), 'active' => strpos($route, 'settings/campaign_') === 0),
                    array('url' => array('settings/customer_common'), 'label' => Yii::t('app', 'Customers'), 'active' => strpos($route, 'settings/customer_') === 0),
                    array('url' => array('settings/api_ip_access'), 'label' => Yii::t('app', 'Api'), 'active' => strpos($route, 'settings/api_ip_access') === 0),
                    array('url' => array('settings/monetization'), 'label' => Yii::t('app', 'Monetization'), 'active' => strpos($route, 'settings/monetization') === 0),
                    array('url' => array('settings/customization'), 'label' => Yii::t('app', 'Customization'), 'active' => strpos($route, 'settings/customization') === 0),
                    array('url' => array('settings/cdn'), 'label' => Yii::t('app', 'CDN'), 'active' => strpos($route, 'settings/cdn') === 0),
                    array('url' => array('settings/spf_dkim'), 'label' => Yii::t('app', 'SPF/DKIM'), 'active' => strpos($route, 'settings/spf_dkim') === 0),
                    array('url' => array('settings/redis_queue'), 'label' => Yii::t('app', 'Queue'), 'active' => strpos($route, 'settings/redis_queue') === 0),

                    array('url' => array('settings/license'), 'label' => Yii::t('app', 'License'), 'active' => strpos($route, 'settings/license') === 0), 
                ),
            ),
			'misc' => array(
                'name'      => Yii::t('app', 'Miscellaneous'),
                'icon'      => 'glyphicon glyphicon-bookmark',
                'active'    => array('misc', 'transactional_emails', 'company_types', 'campaign_abuse_reports'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('misc/campaigns_delivery_logs'), 'label' => Yii::t('app', 'Campaigns delivery logs'), 'active' => strpos($route, 'misc/campaigns_delivery_logs') === 0),
                    array('url' => array('misc/campaigns_bounce_logs'), 'label' => Yii::t('app', 'Campaigns bounce logs'), 'active' => strpos($route, 'misc/campaigns_bounce_logs') === 0),
                    array('url' => array('campaign_abuse_reports/index'), 'label' => Yii::t('app', 'Campaign abuse reports'), 'active' => strpos($route, 'campaign_abuse_reports/index') === 0),
                    array('url' => array('transactional_emails/index'), 'label' => Yii::t('app', 'Transactional emails'), 'active' => strpos($route, 'transactional_emails') === 0),
                    array('url' => array('misc/delivery_servers_usage_logs'), 'label' => Yii::t('app', 'Delivery servers usage logs'), 'active' => strpos($route, 'misc/delivery_servers_usage_logs') === 0),
                    array('url' => array('company_types/index'), 'label' => Yii::t('app', 'Company types'), 'active' => strpos($route, 'company_types') === 0),
                    array('url' => array('misc/application_log'), 'label' => Yii::t('app', 'Application log'), 'active' => strpos($route, 'misc/application_log') === 0),
                    array('url' => array('misc/emergency_actions'), 'label' => Yii::t('app', 'Emergency actions'), 'active' => strpos($route, 'misc/emergency_actions') === 0),
                    array('url' => array('misc/guest_fail_attempts'), 'label' => Yii::t('app', 'Guest fail attempts'), 'active' => strpos($route, 'misc/guest_fail_attempts') === 0),
                    array('url' => array('misc/cron_jobs_list'), 'label' => Yii::t('app', 'Cron jobs list'), 'active' => strpos($route, 'misc/cron_jobs_list') === 0),
                    array('url' => array('misc/phpinfo'), 'label' => Yii::t('app', 'PHP info'), 'active' => strpos($route, 'misc/phpinfo') === 0),
                ),
            ),*/
            /*'articles' => array(
                'name'      => Yii::t('app', 'Articles'),
                'icon'      => 'glyphicon-book',
                'active'    => 'article',
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('articles/index'), 'label' => Yii::t('app', 'View all articles'), 'active' => strpos($route, 'articles/index') === 0),
                    array('url' => array('article_categories/index'), 'label' => Yii::t('app', 'View all categories'), 'active' => strpos($route, 'article_categories') === 0),
                ),
            ),
			'plans' => array(
                'name'      => Yii::t('app', 'Plans'),
                'icon'      => 'glyphicon-book',
                'active'    => 'plan',
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('plan/index'), 'label' => Yii::t('app', 'List Plans'), 'active' => strpos($route, 'plan/index') === 0),
                    array('url' => array('plan/create'), 'label' => Yii::t('app', 'Create new Plans'), 'active' => strpos($route, 'plan/create') === 0),
                ),
            ),
			
            'users' => array(
                'name'      => Yii::t('app', 'Users'),
                'icon'      => 'glyphicon-user',
                'active'    => array('users', 'user_groups'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('users/index'), 'label' => Yii::t('app', 'Users'), 'active' => strpos($route, 'users') === 0),
                    array('url' => array('user_groups/index'), 'label' => Yii::t('app', 'Groups'), 'active' => strpos($route, 'user_groups') === 0),
                ),
            ),
            'monetization' => array(
                'name'      => Yii::t('app', 'Monetization'),
                'icon'      => 'glyphicon-credit-card',
                'active'    => array('payment_gateway', 'price_plans', 'orders', 'promo_codes', 'currencies', 'taxes'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('payment_gateways/index'), 'label' => Yii::t('app', 'Payment gateways'), 'active' => strpos($route, 'payment_gateway') === 0),
                    array('url' => array('price_plans/index'), 'label' => Yii::t('app', 'Price plans'), 'active' => strpos($route, 'price_plans') === 0),
                    array('url' => array('orders/index'), 'label' => Yii::t('app', 'Orders'), 'active' => strpos($route, 'orders') === 0),
                    array('url' => array('promo_codes/index'), 'label' => Yii::t('app', 'Promo codes'), 'active' => strpos($route, 'promo_codes') === 0),
                    array('url' => array('currencies/index'), 'label' => Yii::t('app', 'Currencies'), 'active' => strpos($route, 'currencies') === 0),
                    array('url' => array('taxes/index'), 'label' => Yii::t('app', 'Taxes'), 'active' => strpos($route, 'taxes') === 0),
                ),
            ),
            'customers' => array(
                'name'      => Yii::t('app', 'Customers'),
                'icon'      => 'glyphicon-user',
                'active'    => array('customer', 'campaign'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('customers/index'), 'label' => Yii::t('app', 'Customers'), 'active' => strpos($route, 'customers') === 0 && strpos($route, 'customers_mass_emails') === false),
                    array('url' => array('customer_groups/index'), 'label' => Yii::t('app', 'Groups'), 'active' => strpos($route, 'customer_groups') === 0),
                    array('url' => array('campaigns/index'), 'label' => Yii::t('app', 'Campaigns'), 'active' => strpos($route, 'campaigns') === 0),
                    array('url' => array('customers_mass_emails/index'), 'label' => Yii::t('app', 'Mass emails'), 'active' => strpos($route, 'customers_mass_emails') === 0),
                    array('url' => array('customer_messages/index'), 'label' => Yii::t('app', 'Messages'), 'active' => strpos($route, 'customer_messages') === 0),
                    array('url' => array('customer_login_logs/index'), 'label' => Yii::t('app', 'Login logs'), 'active' => strpos($route, 'customer_login_logs') === 0),
                ),
            ),
            'servers'       => array(
                'name'      => Yii::t('app', 'Servers'),
                'icon'      => 'glyphicon-transfer',
                'active'    => array('delivery_servers', 'bounce_servers', 'feedback_loop_servers'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('delivery_servers/index'), 'label' => Yii::t('app', 'Delivery servers'), 'active' => strpos($route, 'delivery_servers') === 0),
                    array('url' => array('bounce_servers/index'), 'label' => Yii::t('app', 'Bounce servers'), 'active' => strpos($route, 'bounce_servers') === 0),
                    array('url' => array('feedback_loop_servers/index'), 'label' => Yii::t('app', 'Feedback loop servers'), 'active' => strpos($route, 'feedback_loop_servers') === 0),
                ),
            ),
            'domains' => array(
                'name'      => Yii::t('app', 'Domains'),
                'icon'      => 'glyphicon-globe',
                'active'    => array('sending_domains', 'tracking_domains'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('sending_domains/index'), 'label' => Yii::t('app', 'Sending domains'), 'active' => strpos($route, 'sending_domains') === 0),
                    array('url' => array('tracking_domains/index'), 'label' => Yii::t('app', 'Tracking domains'), 'active' => strpos($route, 'tracking_domains') === 0),
                ),
            ),
            'list-page-type' => array(
                'name'      => Yii::t('app', 'List page types'),
                'icon'      => 'glyphicon-list-alt',
                'active'    => 'list_page_type',
				'linkOptions' => array('class' => 'nav-link'),
                'route'     => array('list_page_type/index'),
            ),
            'email-templates-gallery' => array(
                'name'      => Yii::t('app', 'Email templates gallery'),
                'icon'      => 'glyphicon-text-width',
                'active'    => 'email_templates_gallery',
                'route'     => array('email_templates_gallery/index'),
				'linkOptions' => array('class' => 'nav-link'),
            ),
            'blacklist' => array(
                'name'      => Yii::t('app', 'Email blacklist'),
                'icon'      => 'glyphicon-ban-circle',
                'active'    => 'email_blacklist',
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('email_blacklist/index'), 'label' => Yii::t('app', 'Email blacklist'), 'active' => $route == 'email_blacklist' || strpos($route, 'email_blacklist/') === 0),
                    array('url' => array('email_blacklist_monitors/index'), 'label' => Yii::t('app', 'Blacklist monitors'), 'active' => strpos($route, 'email_blacklist_monitors') === 0),
                ),
            ),


           'extend' => array(
                'name'      => Yii::t('app', 'Extend'),
                'icon'      => 'glyphicon-plus-sign',
                'active'    => array('extensions', 'theme', 'languages', 'ext'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                  array('url' => array('extensions/index'), 'label' => Yii::t('app', 'Extensions'), 'active' => strpos($route, 'ext') === 0), 
                    array('url' => array('theme/index'), 'label' => Yii::t('app', 'Themes'), 'active' => strpos($route, 'theme') === 0),
                    array('url' => array('languages/index'), 'label' => Yii::t('app', 'Languages'), 'active' => strpos($route, 'languages') === 0),
                ),
                  ), 

			
            'locations' => array(
                'name'      => Yii::t('app', 'Locations'),
                'icon'      => 'glyphicon-globe',
                'active'    => array('ip_location_services', 'countries', 'zones'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('ip_location_services/index'), 'label' => Yii::t('app', 'Ip location services'), 'active' => strpos($route, 'ip_location_services') === 0),
                    array('url' => array('countries/index'), 'label' => Yii::t('app', 'Countries'), 'active' => strpos($route, 'countries') === 0),
                    array('url' => array('zones/index'), 'label' => Yii::t('app', 'Zones'), 'active' => strpos($route, 'zones') === 0),
                ),
            ),
            'settings' => array(
                'name'      => Yii::t('app', 'Settings'),
                'icon'      => 'glyphicon-cog',
                'active'    => 'settings',
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(		
				
                    array('url' => array('settings/index'), 'label' => Yii::t('app', 'Common'), 'active' => strpos($route, 'settings/index') === 0),
                    
					array('url' => array('socialsetting/index'), 'label' => Yii::t('app', 'Social API Settings'), 'active' => strpos($route, 'socialsetting/index') === 0),
                    
					array('url' => array('smssetting/index'), 'label' => Yii::t('app', 'SMS API Settings'), 'active' => strpos($route, 'smssetting/index') === 0),
                    array('url' => array('settings/system_urls'), 'label' => Yii::t('app', 'System urls'), 'active' => strpos($route, 'settings/system_urls') === 0),
                    array('url' => array('settings/import_export'), 'label' => Yii::t('app', 'Import/Export'), 'active' => strpos($route, 'settings/import_export') === 0),
                    array('url' => array('settings/email_templates'), 'label' => Yii::t('app', 'Email templates'), 'active' => strpos($route, 'settings/email_templates') === 0),
                    array('url' => array('settings/cron'), 'label' => Yii::t('app', 'Cron'), 'active' => strpos($route, 'settings/cron') === 0),
                    array('url' => array('settings/email_blacklist'), 'label' => Yii::t('app', 'Email blacklist'), 'active' => strpos($route, 'settings/email_blacklist') === 0),
                    array('url' => array('settings/campaign_attachments'), 'label' => Yii::t('app', 'Campaigns'), 'active' => strpos($route, 'settings/campaign_') === 0),
                    array('url' => array('settings/customer_common'), 'label' => Yii::t('app', 'Customers'), 'active' => strpos($route, 'settings/customer_') === 0),
                    array('url' => array('settings/api_ip_access'), 'label' => Yii::t('app', 'Api'), 'active' => strpos($route, 'settings/api_ip_access') === 0),
                    array('url' => array('settings/monetization'), 'label' => Yii::t('app', 'Monetization'), 'active' => strpos($route, 'settings/monetization') === 0),
                    array('url' => array('settings/customization'), 'label' => Yii::t('app', 'Customization'), 'active' => strpos($route, 'settings/customization') === 0),
                    array('url' => array('settings/cdn'), 'label' => Yii::t('app', 'CDN'), 'active' => strpos($route, 'settings/cdn') === 0),
                    array('url' => array('settings/spf_dkim'), 'label' => Yii::t('app', 'SPF/DKIM'), 'active' => strpos($route, 'settings/spf_dkim') === 0),
                    array('url' => array('settings/redis_queue'), 'label' => Yii::t('app', 'Queue'), 'active' => strpos($route, 'settings/redis_queue') === 0),

                    array('url' => array('settings/license'), 'label' => Yii::t('app', 'License'), 'active' => strpos($route, 'settings/license') === 0), 
                ),
            ),
            'misc' => array(
                'name'      => Yii::t('app', 'Miscellaneous'),
                'icon'      => 'glyphicon-bookmark',
                'active'    => array('misc', 'transactional_emails', 'company_types', 'campaign_abuse_reports'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('misc/campaigns_delivery_logs'), 'label' => Yii::t('app', 'Campaigns delivery logs'), 'active' => strpos($route, 'misc/campaigns_delivery_logs') === 0),
                    array('url' => array('misc/campaigns_bounce_logs'), 'label' => Yii::t('app', 'Campaigns bounce logs'), 'active' => strpos($route, 'misc/campaigns_bounce_logs') === 0),
                    array('url' => array('campaign_abuse_reports/index'), 'label' => Yii::t('app', 'Campaign abuse reports'), 'active' => strpos($route, 'campaign_abuse_reports/index') === 0),
                    array('url' => array('transactional_emails/index'), 'label' => Yii::t('app', 'Transactional emails'), 'active' => strpos($route, 'transactional_emails') === 0),
                    array('url' => array('misc/delivery_servers_usage_logs'), 'label' => Yii::t('app', 'Delivery servers usage logs'), 'active' => strpos($route, 'misc/delivery_servers_usage_logs') === 0),
                    array('url' => array('company_types/index'), 'label' => Yii::t('app', 'Company types'), 'active' => strpos($route, 'company_types') === 0),
                    array('url' => array('misc/application_log'), 'label' => Yii::t('app', 'Application log'), 'active' => strpos($route, 'misc/application_log') === 0),
                    array('url' => array('misc/emergency_actions'), 'label' => Yii::t('app', 'Emergency actions'), 'active' => strpos($route, 'misc/emergency_actions') === 0),
                    array('url' => array('misc/guest_fail_attempts'), 'label' => Yii::t('app', 'Guest fail attempts'), 'active' => strpos($route, 'misc/guest_fail_attempts') === 0),
                    array('url' => array('misc/cron_jobs_list'), 'label' => Yii::t('app', 'Cron jobs list'), 'active' => strpos($route, 'misc/cron_jobs_list') === 0),
                    array('url' => array('misc/phpinfo'), 'label' => Yii::t('app', 'PHP info'), 'active' => strpos($route, 'misc/phpinfo') === 0),
                ),
            ),*/
            /*'store' => array(
                'name'      => Yii::t('app', 'Store'),
                'icon'      => 'glyphicon-shopping-cart',
                'active'    => 'store',
                'route'     => array('store/index'),
            ),
			*/
			
			
        );

        if ($supportUrl == '') {
            unset($menuItems['support']);
        }
        
        if (!Yii::app()->params['store.enabled']) {
            unset($menuItems['store']);
        }

        $menuItems = (array)Yii::app()->hooks->applyFilters('backend_left_navigation_menu_items', $menuItems);

        // since 1.3.5

        foreach ($menuItems as $key => $data) {
            if (!empty($data['route']) && !$user->hasRouteAccess($data['route'])) {
                unset($menuItems[$key]);
                continue;
            }
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $index => $item) {
                    if (isset($item['url']) && !$user->hasRouteAccess($item['url'])) {
                        unset($menuItems[$key]['items'][$index], $data['items'][$index]);
                    }
                }
            }
            if (empty($data['route']) && empty($data['items'])) {
                unset($menuItems[$key]);
            }
        }
        
        return $menuItems;
    }

    /**
     * @throws CException
     */
    public function buildMenu()
    {
        $controller = $this->controller;
        $route      = $controller->route;

        Yii::import('zii.widgets.CMenu');

        $menu = new CMenu();
        $menu->htmlOptions          = array('class' => 'page-sidebar-menu page-header-fixed', 'data-keep-expanded' => 'false', 'data-auto-scroll' => 'true', 'data-slide-speed' => 200,);
        $menu->submenuHtmlOptions   = array('class' => 'sub-menu');
        $menuItems                  = $this->getMenuItems();
		
        foreach ($menuItems as $key => $data) {

            $_route  = !empty($data['route']) ? $data['route'] : 'javascript:;';
            $active  = false;
			
            if (!empty($data['active']) && is_string($data['active']) && strpos($route, $data['active']) === 0) {
                $active = true;
            } elseif (!empty($data['active']) && is_array($data['active'])) {

                foreach ($data['active'] as $in) {
					if (strpos($route, $in) === 0) {
						$active = true;
						break;
					}
                }
            }
            $item = array(
                'url'         => $_route,
                'label'       => '<i class="'.$data['icon'].'"></i> <span class="title">'.$data['name'].'</span>' . (!empty($data['items']) ? '<span class="arrow"></span>' : ''),
                'active'      => $active,
                'linkOptions' => !empty($data['linkOptions']) && is_array($data['linkOptions']) ? $data['linkOptions'] : array(),
            );
			
            if (!empty($data['items'])) {

                foreach ($data['items'] as $index => $i) {
                    if (isset($i['label'])) {
                        $data['items'][$index]['label'] = '' . $i['label'];
                    }
                }
				// echo '<pre>';
				// print_r($data['items']);
				// echo '</pre>';
                $item['items']       = $data['items'];
                $item['itemOptions'] = array('class' => 'nav-item');
            }

            $menu->items[] = $item;
        }

        $menu->run();
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->buildMenu();
    }
}
