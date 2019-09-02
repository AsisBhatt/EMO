<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false 
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));
$url =  Yii::app()->apps->getAppUrl('customer', 'assets/files/Guidance For Wordpress Subscriber Popup.pdf');
// and render if allowed
if ($viewCollection->renderContent) { ?>
	<div class="portlet-body">
		<div id="glance-box" data-source="<?php echo $this->createUrl('dashboard/glance');?>">
			<ul class="list-item">
				<!-- <li> -->
					<!-- <a href="http://www.edata-social-media.us" target="_blank">Edata Socialmedia</a> -->
				<!-- </li> -->
				<!-- <li> -->
					<!-- <a href="http://www.edatamedia-shopping-carts.us" target="_blank">Edatamedia Shopping carts</a> -->
				<!-- </li> -->
				<!-- <li> -->
					<!-- <a href="http://www.edatamedia-seo-search-engine-marketing.us" target="_blank">Edatamedia Seo Search Engine Marketing</a> -->
				<!-- </li> -->
				<!-- <li> -->
					<!-- <a href="http://www.edatamedia-resellers-affiliates.us" target="_blank">Edatamedia Resellers Affiliates</a> -->
				<!-- </li> -->
				<!-- <li> -->
					<!-- <a href="http://www.edatamedia-payment.us" target="_blank">Edatamedia Payment</a> -->
				<!-- </li> -->
				<!-- <li> -->
					<!-- <a href="http://www.edatamedia-mobile-sms.us" target="_blank">Edatamedia Mobile Sms</a> -->
				<!-- </li> -->
				<!-- <li> -->
					<!-- <a href="http://www.edatamedia-email-marketing.us" target="_blank">Edatamedia Email Marketing</a> -->
				<!-- </li> -->
				<!-- <li> -->
					<!-- <a href="http://www.edatamedia-crowdfunding.us" target="_blank">Edatamedia Crowdfunding</a> -->
				<!-- </li> -->
				<!-- <li> -->
					<!-- <a href="http://www.edatamedia-crm.us" target="_blank">Edatamedia Crm</a> -->
				<!-- </li> -->
				<!-- <li> -->
					<!-- <a href="http://www.edatamedia-billing.us" target="_blank">Edatamedia Billing</a> -->
				<!-- </li> -->
				<li>
					<a href="http://beelift-online-risk-management.com" target="_blank">Beelift Online Risk Management</a>
				</li>
				<li>
					<a href="http://beelift-branding-internet-marketing.com" target="_blank">Beelift Branding Internet Marketing</a>
				</li>
				<li>
					<a href="http://beelift-online-merchant-global-billing-services.com" target="_blank">Beelift Online Merchant Global Billing Services</a>
				</li>
				<li>
					<a href="http://beelift-producers-agent-manager-record-label.com" target="_blank">Beelift Producers Agent Manager Record Label</a>
				</li>
				<li>
					<a href="http://beelift-email-marketing-platform.com" target="_blank">Beelift Email Marketing Platform</a>
				</li>
				<li>
					<a href="http://beelift-crowdfunding.com" target="_blank">Beelift Crowdfunding</a>
				</li>
				<li>
					<a href="http://beelift-shopping-cart.com" target="_blank">Beelift Shopping Cart</a>
				</li>
				<li>
					<a href="http://beelift-mobile-sms-platform.com" target="_blank">Beelift Mobile Sms Platform</a>
				</li>
				<li>
					<a href="http://beelift-social-media-platform.com" target="_blank">Beelift Social Media Platform</a>
				</li>
				<li>
					<a href="http://beelift-crm-customer-service-platform.com" target="_blank">Beelift CRM Customer Service Platform</a>
				</li>
				<li>
					<?php
						$text = '<a href="{exampleArchiveHref}" target="_blank">Guidance For Wordpress Subscriber Popup</a>';
						echo Yii::t('list_import', StringHelper::normalizeTranslationString($text), array(
							'{exampleArchiveHref}'  => Yii::app()->apps->getAppUrl('customer', 'assets/files/Guidance For Wordpress Subscriber Popup.pdf', false, true),
						));
					?>
				</li>
			</ul>
		</div>
	</div>
<?php 
}
/**
 * This hook gives a chance to append content after the view file default content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * @since 1.3.3.1
 */
$hooks->doAction('after_view_file_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));