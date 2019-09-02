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
if ($viewCollection->renderContent) { ?>
    <div class="margin-left-20 margin-right-20 margin-top-20">
		<div class="row">
			<section class="col-lg-6" id="chatter-box" data-source="<?php echo $this->createUrl('dashboard/chatter');?>" data-deleteall="<?php echo $this->createUrl('dashboard/delete_logs');?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="chatter-header">
						<div class="caption">
							<i class="fa fa-bullhorn"></i> 
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('dashboard', 'Recent activity');?>
							</h3>
						</div>
						<div class="actions">
							<div class="btn-group btn-group-devided">
								<a class="btn green btn-outline btn-circle btn-sm" data-bind="click: chatter.load"><i class="fa fa-refresh"></i> <?php echo Yii::t('app', 'Refresh');?></a>
							</div>
						</div>
					</div>
					<div class="portlet-body" style="height: 350px; padding:0;">
						<div id="chatter">
							<ul class="timeline" data-bind="foreach: { data: chatter.days, as: 'day' }">
								<li class="time-label"><span data-bind="css: $root.chatter.randomTimeClass, text: day.date"></span></li>
								<!-- ko foreach: { data: items, as: 'item' } -->
								<li>
									<i data-bind="css: $root.chatter.randomIconBg"></i>
									<div class="timeline-item">
										<span class="time"><i class="fa fa-clock-o"></i> <span data-bind="text: time"></span></span>
										<h3 class="timeline-header"><a data-bind="text: customerName, attr: {href: customerUrl}"></a></h3>
										<div class="timeline-body" data-bind="html: message"></div>
									</div>
								</li>
								<!-- /ko -->
							</ul>
						</div>
					</div>
					<div class="overlay" data-bind="visible: chatter.loading"></div>
					<div class="loading-img" data-bind="visible: chatter.loading"></div>
				</div>
			</section>
			<section class="col-lg-6" id="subscribers-growth-box" data-source="<?php echo $this->createUrl('dashboard/subscribers_growth');?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="subscribers-growth-header">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i>
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('dashboard', 'Subscribers growth');?>
							</span>
						</div>
						<div class="box-tools actions">
							<a href="javascript:;" class="btn green btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo Yii::t('app', 'The information is refreshed once at {n} minutes.', 10);?>"><span class="fa fa-info-circle"></span></a>
						</div>
					</div>
					<div class="portlet-body" style="height: 350px; padding:0;">
						<div id="subscribers">
							<div id="subscribers-growth-chart" style="height: 350px;"></div>
						</div>
					</div>
					<div class="overlay" data-bind="visible: subscribersGrowthChart.loading"></div>
					<div class="loading-img" data-bind="visible: subscribersGrowthChart.loading"></div>
				</div>
			</section>
		</div>
	</div>
	<div class="margin-left-20 margin-right-20">
		<div class="row">
			<section class="col-lg-6" id="lists-growth-box" data-source="<?php echo $this->createUrl('dashboard/lists_growth');?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="lists-growth-header">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i>
							<span class="caption-subject font-dark sbold uppercase">
								 <?php echo Yii::t('dashboard', 'Lists growth');?>
							</span>
						</div>
						<div class="box-tools actions">
							<a href="javascript:;" class="btn green btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo Yii::t('app', 'The information is refreshed once at {n} minutes.', 10);?>"><span class="fa fa-info-circle"></span></a>
						</div>
					</div>
					<div class="portlet-body" style="height: 350px; padding:0;">
						<div id="lists">
							<div id="lists-growth-chart" style="height: 350px;"></div>
						</div>
					</div>
					<div class="overlay" data-bind="visible: listsGrowthChart.loading"></div>
					<div class="loading-img" data-bind="visible: listsGrowthChart.loading"></div>
				</div>
			</section>
			<section class="col-lg-6" id="campaigns-growth-box" data-source="<?php echo $this->createUrl('dashboard/campaigns_growth');?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="campaigns-growth-header">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i>
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('dashboard', 'Campaigns growth');?>
							</span>
						</div>
						<div class="box-tools actions">
							<a href="javascript:;" class="btn green btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo Yii::t('app', 'The information is refreshed once at {n} minutes.', 10);?>"><span class="fa fa-info-circle"></span></a>
						</div>
					</div>
					<div class="portlet-body" style="height: 350px; padding:0;">
						<div id="campaigns">
							<div id="campaigns-growth-chart" style="height: 350px;"></div>
						</div>
					</div>
					<div class="overlay" data-bind="visible: campaignsGrowthChart.loading"></div>
					<div class="loading-img" data-bind="visible: campaignsGrowthChart.loading"></div>
				</div>
			</section>
		</div>
    </div>
	<div class="margin-left-20 margin-right-20">
		<div class="row">
			<section class="col-lg-6" id="deliverybounce-growth-box" data-source="<?php echo $this->createUrl('dashboard/delivery_bounce_growth');?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="deliverybounce-growth-header">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i>
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('dashboard', 'Delivery vs Bounces');?>
							</span>
						</div>
						<div class="box-tools actions">
							<a href="javascript:;" class="btn green btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo Yii::t('app', 'The information is refreshed once at {n} minutes.', 10);?>"><span class="fa fa-info-circle"></span></a>
						</div>
					</div>
					<div class="portlet-body" style="height: 350px; padding:0;">
						<div id="deliverybounce">
							<div id="deliverybounce-growth-chart" style="height: 350px;"></div>
						</div>
					</div>
					<div class="overlay" data-bind="visible: deliveryBounceGrowthChart.loading"></div>
					<div class="loading-img" data-bind="visible: deliveryBounceGrowthChart.loading"></div>
				</div>
			</section>
			<section class="col-lg-6" id="unsubscribe-growth-box" data-source="<?php echo $this->createUrl('dashboard/unsubscribe_growth');?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="unsubscribe-growth-header">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i>
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('dashboard', 'Unsubscribe growth');?>
							</span>
						</div>
						<div class="box-tools actions">
							<a href="javascript:;" class="btn green btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo Yii::t('app', 'The information is refreshed once at {n} minutes.', 10);?>"><span class="fa fa-info-circle"></span></a>
						</div>
					</div>
					<div class="portlet-body" style="height: 350px; padding:0;">
						<div class="col-lg-12" id="unsubscribe">
							<div id="unsubscribe-growth-chart" style="height: 350px;"></div>
						</div>						
					</div>
					<div class="overlay" data-bind="visible: unsubscribeGrowthChart.loading"></div>
					<div class="loading-img" data-bind="visible: unsubscribeGrowthChart.loading"></div>
				</div>
			</section>
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