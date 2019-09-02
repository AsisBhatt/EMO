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
        $customer   = Yii::app()->customer->getModel();
		
		$supportUrl = Yii::app()->options->get('system.common.support_url');
        if ($supportUrl === null) {
            $supportUrl = MW_SUPPORT_KB_URL;
        }
        
        $menuItems = array(
			/*'support' => array(
                'name'        => Yii::t('app', 'Support'),
                'icon'        => 'glyphicon-question-sign',
                'active'      => '',
                'route'       => $supportUrl,
                'linkOptions' => array('target' => '_blank'),
            ),*/
            'dashboard' => array(
                'name'      => Yii::t('app', 'Dashboard'),
                'icon'      => 'icon-home',
                'active'    => 'dashboard',
                'route'     => array('dashboard/index'),
				'linkOptions' => array('class' => 'nav-link home_selected'),
            ),
			'social' => array(
                'name'      => Yii::t('app', 'Social Marketing'),
                'icon'      => 'icon-share',
                'active'    => 'social',
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
					array('url' => array('dashboard/socialdashboard'), 'label' => Yii::t('app', 'Social Dashboard'), 'active' => strpos($route, 'dashboard/socialdashboard') === 0),
					array('url' => array('socialmedia/reports'), 'label' => Yii::t('app', 'Reports'), 'active' => strpos($route, 'socialmedia/reports') === 0 || strpos($route, 'price_plans/payment') === 0),
					array('url' => array('socialmedia/template'), 'label' => Yii::t('app', 'Templates'), 'active' => strpos($route, 'price_plans/index') === 0 || strpos($route, 'price_plans/payment') === 0),
					'price_plans' => array(
						'label'      => Yii::t('app', 'Price plans'),
						'icon'      => 'glyphicon-credit-card',
						'active'    => 'price_plans',
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('price_plans/index'), 'label' => Yii::t('app', 'Price plans'), 'active' => strpos($route, 'price_plans/index') === 0 || strpos($route, 'price_plans/payment') === 0),
							array('url' => array('price_plans/orders'), 'label' => Yii::t('app', 'Orders history'), 'active' => strpos($route, 'price_plans/order') === 0),
						),
					),
					'lists' => array(
						'label'      => Yii::t('app', 'Social Media Lists'),
						'icon'      => 'glyphicon-list-alt',
						'active'    => array('lists', 'email_blacklist'),
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('lists/index'), 'label' => Yii::t('app', 'Lists'), 'active' => strpos($route, 'lists') === 0 && strpos($route, 'lists_tools') === false),
							array('url' => array('lists_tools/index'), 'label' => Yii::t('app', 'Tools'), 'active' => strpos($route, 'lists_tools') === 0),
							//array('url' => array('email_blacklist/index'), 'label' => Yii::t('app', 'Email blacklist'), 'active' => strpos($route, 'email_blacklist') === 0),
						),
					),
					'googleanalytics' => array(
						'label'      => Yii::t('app', 'Google Analytics'),
						'icon'      => 'glyphicon-book',
						'active'    => 'googleanalytics',
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							//array('url' => array('account/googleanalyticssetting'), 'label' => Yii::t('app', 'Update Setting'), 'active' => strpos($route, 'account/googleanalyticssetting') === 0),
							//array('url' => array('account/viewanalytics'), 'label' => Yii::t('app', 'Track Analytics Activity'), 'active' => strpos($route, 'account/view') === 0),
							array('url' => array('analytic/analyticsinit'), 'label' => Yii::t('app', 'Track Analytics report'), 'active' => strpos($route, 'analytic/analyticsinit') === 0),
						   
						),
					),
                    //array('url' => array('account/socialsetting'), 'label' => Yii::t('app', 'Update Setting'), 'active' => strpos($route, 'account/socialsetting') === 0),
					//array('url' => array('account/view'), 'label' => Yii::t('app', 'Tracks Social Activity'), 'active' => strpos($route, 'account/view') === 0),
					array('url' => array('socialsetting/connect'), 'label' => Yii::t('app', 'Connect'), 'active' => strpos($route, 'socialsetting/view') === 0),
					array('url' => array('socialsetting/posting'), 'label' => Yii::t('app', 'Status Post'), 'active' => strpos($route, 'socialsetting/view') === 0),
					array('url' => array('socialsetting/postlist'), 'label' => Yii::t('app', 'Post List'), 'active' => strpos($route, 'socialsetting/view') === 0),
                    array('url' => array('socialsetting/twitterdashboard'), 'label' => Yii::t('app', 'Twitter'), 'active' => strpos($route, 'socialsetting/view') === 0),
                    array('url' => array('socialsetting/linkedindashboard'), 'label' => Yii::t('app', 'Linkedin'), 'active' => strpos($route, 'socialsetting/view') === 0),
					array('url' => array('api_keys/index'),'label' => Yii::t('app', 'Api keys'),'active' => strpos($route, 'api_keys') === 0),
                ),
            ),
			'email_marketing' => array(
				'name'		=> Yii::t('app', 'Email Marketing'),
				'icon'		=> 'icon-envelope',
				'active'	=> 'email',
				'route'		=> null,
				'linkOptions'	=> array('class' => 'nav-link nav-toggle'),
				'items'		=> array(
					array('url' => array('email/reports'), 'label' => Yii::t('app', 'Reports'), 'active' => strpos($route, 'price_plans/index') === 0 || strpos($route, 'price_plans/payment') === 0),
					'templates' => array(
						'label'      => Yii::t('app', 'Templates'),
						'icon'      => 'glyphicon-text-width',
						'active'    => 'templates',
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('templates/index'), 'label' => Yii::t('app', 'My templates'), 'active' => in_array($route, array('templates/index', 'templates/create', 'templates/update'))),
							array('url' => array('templates/gallery'), 'label' => Yii::t('app', 'Gallery'), 'active' => strpos($route, 'templates/gallery') === 0),
						),
					),
					'campaigns' => array(
						'label'      => Yii::t('app', 'Campaigns'),
						'icon'      => 'glyphicon-envelope',
						'active'    => 'campaign',
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('campaigns/index'), 'label' => Yii::t('app', 'Campaigns'), 'active' => strpos($route, 'campaigns') === 0),
							array('url' => array('campaign_groups/index'), 'label' => Yii::t('app', 'Groups'), 'active' => strpos($route, 'campaign_groups') === 0),
							array('url' => array('campaign_tags/index'), 'label' => Yii::t('app', 'Custom tags'), 'active' => strpos($route, 'campaign_tags') === 0),
						),
					),
					'templates' => array(
						'label'      => Yii::t('app', 'Templates'),
						'icon'      => 'glyphicon-text-width',
						'active'    => 'templates',
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('templates/index'), 'label' => Yii::t('app', 'My templates'), 'active' => in_array($route, array('templates/index', 'templates/create', 'templates/update'))),
							array('url' => array('templates/gallery'), 'label' => Yii::t('app', 'Gallery'), 'active' => strpos($route, 'templates/gallery') === 0),
						),
					),
					'servers'       => array(
						'label'      => Yii::t('app', 'Servers'),
						'icon'      => 'glyphicon-transfer',
						'active'    => array('delivery_servers', 'bounce_servers', 'feedback_loop_servers'),
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('delivery_servers/index'), 'label' => Yii::t('app', 'Delivery servers'), 'active' => strpos($route, 'delivery_servers') === 0),
							//array('url' => array('bounce_servers/index'), 'label' => Yii::t('app', 'Bounce servers'), 'active' => strpos($route, 'bounce_servers') === 0),
							//array('url' => array('feedback_loop_servers/index'), 'label' => Yii::t('app', 'Feedback loop servers'), 'active' => strpos($route, 'feedback_loop_servers') === 0),
						),
					),
					'domains' => array(
						'label'      => Yii::t('app', 'Domains'),
						'icon'      => 'glyphicon-globe',
						'active'    => array('sending_domains', 'tracking_domains'),
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('sending_domains/index'), 'label' => Yii::t('app', 'Sending domains'), 'active' => strpos($route, 'sending_domains') === 0),
							array('url' => array('tracking_domains/index'), 'label' => Yii::t('app', 'Tracking domains'), 'active' => strpos($route, 'tracking_domains') === 0),
						),
					),
					'price_plans' => array(
						'label'      => Yii::t('app', 'Price plans'),
						'icon'      => 'glyphicon-credit-card',
						'active'    => 'price_plans',
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('price_plans/index'), 'label' => Yii::t('app', 'Price plans'), 'active' => strpos($route, 'price_plans/index') === 0 || strpos($route, 'price_plans/payment') === 0),
							array('url' => array('price_plans/orders'), 'label' => Yii::t('app', 'Orders history'), 'active' => strpos($route, 'price_plans/order') === 0),
						),
					),
					'lists' => array(
						'label'      => Yii::t('app', 'Email Marketing Lists'),
						'icon'      => 'glyphicon-list-alt',
						'active'    => array('lists', 'email_blacklist'),
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('lists/index'), 'label' => Yii::t('app', 'Lists'), 'active' => strpos($route, 'lists') === 0 && strpos($route, 'lists_tools') === false),
							array('url' => array('lists_tools/index'), 'label' => Yii::t('app', 'Tools'), 'active' => strpos($route, 'lists_tools') === 0),
							array('url' => array('email_blacklist/index'), 'label' => Yii::t('app', 'Email blacklist'), 'active' => strpos($route, 'email_blacklist') === 0),
						),
					),
				),
			),
			'sms' => array(
                'name'      => Yii::t('app', 'SMS Marketing'),
                'icon'      => 'icon-bubbles',
                'active'    => 'sms',
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
					array('url' => array('sms/reports'), 'label' => Yii::t('app', 'Reports'), 'active' => strpos($route, 'sms/sendsms') === 0),
					array('url' => array('sms_template/index'), 'label' => Yii::t('app', 'Sms Templates'), 'active' => strpos($route, 'sms_template/index') === 0),
					array('url' => array('image_gallery/index'), 'label' => Yii::t('app', 'MMS Gallery'), 'active' => strpos($route, 'image_gallery/index') === 0),
					'price_plans' => array(
						'label'      => Yii::t('app', 'Price plans'),
						'icon'      => 'glyphicon-credit-card',
						'active'    => 'price_plans',
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('price_plans/index'), 'label' => Yii::t('app', 'Price plans'), 'active' => strpos($route, 'price_plans/index') === 0 || strpos($route, 'price_plans/payment') === 0),
							array('url' => array('price_plans/orders'), 'label' => Yii::t('app', 'Orders history'), 'active' => strpos($route, 'price_plans/order') === 0),
						),
					),
					'lists' => array(
						'label'      => Yii::t('app', 'SMS Marketing Lists'),
						'icon'      => 'glyphicon-list-alt',
						'active'    => array('lists', 'email_blacklist'),
						'route'     => null,
						'linkOptions' => array('class' => 'nav-link nav-toggle'),
						'items'     => array(
							array('url' => array('lists/index'), 'label' => Yii::t('app', 'Lists'), 'active' => strpos($route, 'lists') === 0 && strpos($route, 'lists_tools') === false),
							array('url' => array('lists_tools/index'), 'label' => Yii::t('app', 'Tools'), 'active' => strpos($route, 'lists_tools') === 0),
							//array('url' => array('email_blacklist/index'), 'label' => Yii::t('app', 'Email blacklist'), 'active' => strpos($route, 'email_blacklist') === 0),
						),
					),
					array('url' => array('sms/sendsms'), 'label' => Yii::t('app', 'Send SMS'), 'active' => strpos($route, 'sms/sendsms') === 0),
					array('url' => array('sms/sendmms'), 'label' => Yii::t('app', 'Send MMS'), 'active' => strpos($route, 'sms/sendmms') === 0),
                    /*array('url' => array('sms/sendsmslist'), 'label' => Yii::t('app', 'Send SMS To List'), 'active' => strpos($route, 'sms/sendsmslist') === 0),*/
					array('url' => array('sms_campaign/index/type/sms'), 'label' => Yii::t('app', 'Send Marketing SMS'), 'active' => strpos($route, 'sms_campaign/index/type/sms') === 0),
					array('url' => array('sms_campaign/index/type/mms'), 'label' => Yii::t('app', 'Send Marketing MMS'), 'active' => strpos($route, 'sms_campaign/index/type/mms') === 0),
					array('url' => array('sms/index'), 'label' => Yii::t('app', 'Track SMS Activity'), 'active' => strpos($route, 'sms/index') === 0),
					array('url' => array('sms/index/mms'), 'label' => Yii::t('app', 'Track MMS Activity'), 'active' => strpos($route, 'sms/index') === 0),
					array('url' => array('sms/smsrplylist'), 'label' => Yii::t('app', 'SMS Reply Activity'), 'active' => strpos($route, 'sms/smsrplylist') === 0),
					//array('url' => array('link/index'), 'label' => Yii::t('app', 'Track Urls'), 'active' => strpos($route, 'link/index') === 0),
					array('url' => array('sms/texttopay'), 'label' => Yii::t('app', 'Text to pay'), 'active' => strpos($route, 'sms/text_topay') === 0),
					array('url' => array('sms/stopcounter'), 'label' => Yii::t('app', 'Stop Report'), 'active' => strpos($route, 'sms/stopcounter') === 0),
					array('url' => array('autoreply_template/index/type/join'), 'label' => Yii::t('app', 'Auto Reply Join Template'), 'active' => strpos($route, 'autoreply_template/index') === 0),
					array('url' => array('autoreply_template/index/type/buy'), 'label' => Yii::t('app', 'Auto Reply Buy Template'), 'active' => strpos($route, 'autoreply_template/index') === 0),
				),
            ),
			'management_section' => array(
				'name'		=> Yii::t('app', 'Management'),
				'icon'		=> 'fa fa-tasks',
				'active'	=> 'management',
				'route'     => array('management/index'),
				'linkOptions'	=> array('class' => 'nav-link nav-toggle'),
			),
            /*'plan' => array(
                'name'      => Yii::t('app', 'Plan'),
                'icon'      => 'glyphicon-book',
                'active'    => 'plan',
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('plan/index'), 'label' => Yii::t('app', 'View Plans'), 'active' => strpos($route, 'plan/index') === 0 || strpos($route, 'plan/index') === 0),
                    //array('url' => array('plan/orders'), 'label' => Yii::t('app', 'Orders history'), 'active' => strpos($route, 'price_plans/order') === 0),
                ),
            ),
			
			'price_plans' => array(
                'name'      => Yii::t('app', 'Price plans'),
                'icon'      => 'glyphicon-credit-card',
                'active'    => 'price_plans',
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('price_plans/index'), 'label' => Yii::t('app', 'Price plans'), 'active' => strpos($route, 'price_plans/index') === 0 || strpos($route, 'price_plans/payment') === 0),
                    array('url' => array('price_plans/orders'), 'label' => Yii::t('app', 'Orders history'), 'active' => strpos($route, 'price_plans/order') === 0),
                ),
            ),
            'lists' => array(
                'name'      => Yii::t('app', 'Lists'),
                'icon'      => 'glyphicon-list-alt',
                'active'    => array('lists', 'email_blacklist'),
                'route'     => null,
				'linkOptions' => array('class' => 'nav-link nav-toggle'),
                'items'     => array(
                    array('url' => array('lists/index'), 'label' => Yii::t('app', 'Lists'), 'active' => strpos($route, 'lists') === 0 && strpos($route, 'lists_tools') === false),
                    array('url' => array('lists_tools/index'), 'label' => Yii::t('app', 'Tools'), 'active' => strpos($route, 'lists_tools') === 0),
                    array('url' => array('email_blacklist/index'), 'label' => Yii::t('app', 'Email blacklist'), 'active' => strpos($route, 'email_blacklist') === 0),
                ),
            ),*/
            'articles' => array(
                'name'      => Yii::t('app', 'Articles'),
                'icon'      => 'glyphicon-book',
                'active'    => 'article',
                'route'     => Yii::app()->apps->getAppUrl('frontend', 'articles', true),
                'items'     => array(),
            ),
            'settings' => array(
                'name'      => Yii::t('app', 'Settings'),
                'icon'      => 'glyphicon-cog',
                'active'    => 'settings',
                'route'     => null,
                'items'     => array(),
            ),
        );

        if (!Yii::app()->options->get('system.customer.action_logging_enabled', true)) {
            unset($menuItems['dashboard']);
        }

        $maxDeliveryServers = $customer->getGroupOption('servers.max_delivery_servers', 0);
        $maxBounceServers   = $customer->getGroupOption('servers.max_bounce_servers', 0);
        $maxFblServers      = $customer->getGroupOption('servers.max_fbl_servers', 0);

        if (!$maxDeliveryServers && !$maxBounceServers && !$maxFblServers) {
            unset($menuItems['servers']);
        } else {
            foreach (array($maxDeliveryServers, $maxBounceServers, $maxFblServers) as $index => $value) {
                if (!$value && isset($menuItems['servers']['items'][$index])) {
                    unset($menuItems['servers']['items'][$index]);
                }
            }
        }

        if (SendingDomain::model()->getRequirementsErrors() || $customer->getGroupOption('sending_domains.can_manage_sending_domains', 'no') != 'yes') {
            unset($menuItems['domains']['items'][0]);
        }

        if ($customer->getGroupOption('tracking_domains.can_manage_tracking_domains', 'no') != 'yes') {
            unset($menuItems['domains']['items'][1]);
        }

        if ($customer->getGroupOption('lists.can_use_own_blacklist', 'no') != 'yes') {
            unset($menuItems['lists']['items'][2]);
        }

        if ($customer->getGroupOption('common.show_articles_menu', 'no') != 'yes') {
            unset($menuItems['articles']);
        }

        if (count($menuItems['domains']['items']) == 0) {
            unset($menuItems['domains']);
        }

        if (Yii::app()->options->get('system.monetization.monetization.enabled', 'no') == 'no') {
            unset($menuItems['price_plans']);
        }

        if (Yii::app()->options->get('system.common.api_status') != 'online') {
            unset($menuItems['api-keys']);
        }

        $menuItems = (array)Yii::app()->hooks->applyFilters('customer_left_navigation_menu_items', $menuItems);

        if (empty($menuItems['settings']['items'])) {
            unset($menuItems['settings']);
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
