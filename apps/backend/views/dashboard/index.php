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
	<script>
	$(document).ready(function(){
		$("#toggle_form").click(function(){
			$("#advance_form").toggle("slow");
		});
		$("#frm_date").datetimepicker({
			format: 'dd-mm-yyyy',
			minView: 2,
			maxView: 4,
			autoclose: true
		});

		$("#to_date").datetimepicker({
			format: 'dd-mm-yyyy',
			minView: 2,
			maxView: 4,
			autoclose: true
		});
	});
	</script>
	<div class="margin-20">
		<div class="top_bars">
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 gray margin-bottom-20" href="<?php echo $this->createUrl('dashboard/smsdashboard'); ?>">
						<div class="visual">
							<i class="icon-bubbles"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/guitar.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc">Sms</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>					
				</div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 green orange margin-bottom-20" href="<?php echo $this->createUrl('dashboard/emaildashboard'); ?>">
						<div class="visual">
							<i class="icon-envelope"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/microphone.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.listsCount"></span>
							</div>
							<div class="desc">Email</div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 blue margin-bottom-20" href="<?php echo $this->createUrl('dashboard/socialdashboard'); ?>">
						<div class="visual">
							<i class="icon-share"></i>
						</div>
						<div class="dashboard-image">
							<img class="img-responsive" src="<?php echo Yii::app()->baseUrl.'/assets/img/music.png'; ?>" alt="" />
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.segmentsCount"></span>
							</div>
							<div class="desc">Social Media</div>
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
	</div>
   <div class="portlet-title email_title custom_title">
		<div class="caption">
			<span class="glyphicon glyphicon-envelope"></span>
			<span class="caption-subject sbold uppercase">
				Email
			</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<section class="col-lg-6" id="chatter-box" data-source="<?php echo $this->createUrl('dashboard/chatter');?>" data-deleteall="<?php echo $this->createUrl('dashboard/delete_logs');?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="chatter-header">
						<div class="caption">
							<i class="fa fa-bullhorn"></i> 
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('dashboard', 'Recent customer activity');?>
							</span>
						</div>
						<div class="actions">
							<div class="btn-group btn-group-devided">
								<a class="btn green btn-outline btn-circle btn-sm" data-bind="click: chatter.load"><i class="fa fa-refresh"></i> <?php echo Yii::t('app', 'Refresh');?></a>
								<a class="btn green btn-outline btn-circle btn-sm" data-bind="click: chatter.removeAll" id="chatter-remove-all" data-confirm="<?php echo Yii::t('dashboard', 'Are you sure you want to remove all activity messages?');?>"><i class="fa fa-times-circle-o"></i> <?php echo Yii::t('app', 'Delete all');?></a>
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
										<span class="time"><i class="fa fa-clock-o"></i> <span data-bind="text: time"></span> <a data-bind="click: $root.chatter.remove" class="btn btn-danger btn-xs"><?php echo Yii::t('app', 'Delete')?></a></span>
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
					<div class="clearfix"><!-- --></div>
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
			<section class="col-lg-6" id="deliverybounce-growth-box" data-source="<?php echo $this->createUrl('dashboard/delivery_bounce_growth');?>">
				<div class="portlet light bordered no-margin">
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
				<div class="portlet light bordered no-margin">
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
						<div id="unsubscribe">
							<div id="unsubscribe-growth-chart" style="height: 350px;"></div>
						</div>
					</div>
					<div class="overlay" data-bind="visible: unsubscribeGrowthChart.loading"></div>
					<div class="loading-img" data-bind="visible: unsubscribeGrowthChart.loading"></div>
				</div>
			</section>
		</div>
	</div>
	<div class="portlet-title sms_title custom_title">
		<div class="caption">
			<span class="fa fa-comments"></span>
			<span class="caption-subject sbold uppercase">
				SMS
			</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<?php
				$form = $this->beginWidget('CActiveForm', array(
					'action'        => array('dashboard/index'),
					'htmlOptions'   => array(
						'id'        => 'sms_search_form',
					),
				));
			?>
				<div class="form-group col-lg-6 text-left">
					<label class="control-label">&nbsp;</label><br>
					<button type="button" class="btn green" id="toggle_form">Advance Filter</button>
				</div>
				<div id="advance_form" style="display:none;">
					<div class="form-group col-lg-2">
						<label class="control-label">From :</label>
						<?php echo CHtml::textField('frm_date','',array('class'=> 'form-control')); ?>
					</div>		
					<div class="form-group col-lg-2">
						<label class="control-label">To :</label>
						<?php echo CHtml::textField('to_date','',array('class'=> 'form-control')); ?>
					</div>
					<div class="form-group col-lg-2">
						<label class="control-label">&nbsp;</label><br>
						<button type="submit" class="btn green btn-submit"><?php echo Yii::t('app', 'Submit');?></button>
					</div>
				</div>
			<?php $this->endWidget(); ?>
		</div>
		<div class="row">
			<?php
				$this->widget('customer.extensions.widgets.highcharts.HighchartsWidget', array(
					'scripts' => array(
						'modules/exporting',
						'themes/epcGray',
					),
					'options' => array(
						'title' => array(
							'text' => '',
						),
						'xAxis' => array(
							'categories' => $date_array['date'],
						),
						'labels' => array(
							'items' => array(
								array(
									'html' => 'Total Send and Not Send SMS',
									'style' => array(
										'left' => '50px',
										'top' => '18px',
										'color' => 'js:(Highcharts.theme && Highcharts.theme.textColor) || \'black\'',
									),
								),
							),
						),
						'credits' => array('enabled' => false),
						'series' => array(
							array(
								'type' => 'column',
								'name' => 'Send',
								'data' => $date_array['send_count'],
								//$date_array['send_count']
							),
							array(
								'type' => 'column',
								'name' => 'Not Send',
								'data' => $date_array['notsend_count'],
							),
							/*array(
								'type' => 'column',
								'name' => 'Joe',
								'data' => array(4, 3, 3, 9, 8),
							),*/
							/*array(
								'type' => 'spline',
								'name' => 'Average',
								'data' => array(3, 2.67, 3, 6.33, 3.33),
								'marker' => array(
									'lineWidth' => 2,
									'lineColor' => 'js:Highcharts.getOptions().colors[3]',
									'fillColor' => 'white',
								),
							),*/
						),
					)
				));
			?>
		</div>
	</div>
	<div class="portlet-title social_title custom_title">
		<div class="caption">
			<span class="fa fa-share-alt"></span>
			<span class="caption-subject sbold uppercase">
				Social Media
			</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-md-12">
				<img src="<?php echo Yii::app()->baseUrl.'/assets/img/social-banner.jpg'; ?>" class="img-responsive dash-bnr" />
				<h2>How We Do Social</h2>
				<p>
				Sure, there's other tools out there that let you update your Bee Lift group channels. But they aren't helping you grow your fan base or increase the engagement levels of your existing followers. 
				</p>
				<p>
				We designed DoSocial to not only make the management of your Bee Lift group accounts as easy as possible, but also to help you supercharge your marketing efforts to reach as many people as possible. What's the point of using social media if nobody is listening? 	
				</p>
				<h3>Bee Lift Group Social Media Management</h3>
				<ul>
					<li>Unified Smart Inbox to streamline Management.</li>
					<li>Social CRM tools including shared customer records.</li>
					<li>Advanced scheduling tools including ViralPost.</li>
					<li>Sophisticated analytics and unlimited custom reports.</li>
					<li>Customer support features like tasks and Helpdesk.</li>
					<li>Team collaboration tools including live activity updates.</li>
				</ul>
			</div>
		</div>
	</div>
    <div class="clearfix" id="dashboard-update" data-checkupdateenabled="<?php //echo (int)$checkVersionUpdate;?>" data-checkupdateurl="<?php echo $this->createUrl('dashboard/check_update');?>"><!-- --></div>
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