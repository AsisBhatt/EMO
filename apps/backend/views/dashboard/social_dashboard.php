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

// and render if allowed
if ($viewCollection->renderContent) {
    ?>
    <div id="glance-box" data-source="<?php echo $this->createUrl('dashboard/glance');?>">
        <div class="portlet-title" id="chatter-header">
            <div class="caption">
				<i class="ion ion-information-circled"></i>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo Yii::t('dashboard', 'At a glance');?>
				</span>
			</div>
        </div>
        <div class="portlet-body">
			<div class="row">
			   <div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 fb_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="icon-social-facebook"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/facebook.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Facebook</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 tw_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="icon-social-twitter"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/twitter.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Twitter</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 in_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="icon-social-linkedin"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/linkedin.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Linkedin</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 youtube_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="icon-social-youtube"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/youtube.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Youtube</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 instagram_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="icon-social-instagram"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/instagram.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Instagram</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>	
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 google_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="icon-social-google"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/google.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Google Plus</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 myspace_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="fa fa-users"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/myspace.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">MySpace</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 pinterest_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="icon-social-pinterest"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/pinterest.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Pinterest</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 tumblr_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="icon-social-tumblr"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/tumblr.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Tumblr</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 snapchat_bg margin-bottom-20" href="javascript:void(0);">
						<div class="visual">
							<i class="fa fa-snapchat" aria-hidden="true"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/snapchat.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Snapchat</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 red margin-bottom-20" href="<?php echo $this->createUrl('socialmedia/management'); ?>">
						<div class="visual">
							<i class="fa fa-server"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Management</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 green-meadow margin-bottom-20" href="<?php echo $this->createUrl('socialmedia/financial'); ?>">
						<div class="visual">
							<i class="fa fa-money"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Financial</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
			</div>
        </div>
        <div class="overlay" data-bind="visible: glance.loading"></div>
        <div class="loading-img" data-bind="visible: glance.loading"></div>
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