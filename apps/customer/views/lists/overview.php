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
    
	<div class="portlet-title" id="chatter-header">
		<div class="caption">
			<i class="glyphicon glyphicon-list-alt"></i>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('lists', 'Overview');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('app', 'Create new'), array('lists/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'New')));?>
				<?php if($list->customer_id != 0){ ?>
					<?php echo CHtml::link(Yii::t('app', 'Update'), array('lists/update', 'list_uid' => $list->list_uid), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Update')));?>
				<?php } ?>
			</div>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<div class="col-lg-4 col-xs-12">
				<div class="dashboard-stat dashboard-stat-v2 blue-sharp margin-bottom-20">
					<div class="visual">
						<i class="icon-bubbles"></i>
					</div>			
					<div class="details">
						<div class="number">
							<span><?php echo Yii::app()->format->formatNumber($subscribersCount);?></span>
						</div>
						<div class="desc"><?php echo Yii::t('list_subscribers', 'Subscribers');?></div>
					</div>
					<div class="small-box">
						<div class="small-box-footer">
							<?php if($list->customer_id != 0){ ?>
							&nbsp;<a href="<?php echo Yii::app()->createUrl("list_subscribers/create", array("list_uid" => $list->list_uid));?>" class="btn blue-sharp btn-flat btn-xs pull-left" style="margin-left:3px;"><span class="fa fa-plus-circle"></span> <?php echo Yii::t('app', 'Add');?></a>
							<?php } ?>
							<a href="<?php echo Yii::app()->createUrl("list_subscribers/index", array("list_uid" => $list->list_uid));?>" class="btn blue-sharp btn-flat btn-xs pull-right" style="margin-right:3px;"><span class="fa fa-eye"></span> <?php echo Yii::t('app', 'View');?></a>&nbsp;
						</div>
					</div>
				</div>
			</div>
			<?php if (!empty($canSegmentLists) && $list->customer_id != 0) { ?>
			<div class="col-lg-4 col-xs-12">
				<div class="dashboard-stat dashboard-stat-v2 yellow margin-bottom-20">
					<div class="visual">
						<i class="icon-globe"></i>
					</div>
					<div class="details">
						<div class="number">
							<span><?php echo Yii::app()->format->formatNumber($segmentsCount);?></span>
						</div>
						<div class="desc"><?php echo Yii::t('list_segments', 'Segments');?></div>
					</div>
					<div class="small-box">
						<div class="small-box-footer">
							&nbsp;<a href="<?php echo Yii::app()->createUrl("list_segments/create", array("list_uid" => $list->list_uid));?>" class="btn yellow btn-flat btn-xs pull-left" style="margin-left:3px;"><span class="fa fa-plus-circle"></span> <?php echo Yii::t('app', 'Add');?></a>
							<a href="<?php echo Yii::app()->createUrl("list_segments/index", array("list_uid" => $list->list_uid));?>" class="btn yellow btn-flat btn-xs pull-right" style="margin-right:3px;"><span class="fa fa-eye"></span> <?php echo Yii::t('app', 'View');?></a>&nbsp;
						</div>
					</div>
				</div>
			</div>
			<?php 
				}
				if($list->customer_id != 0){
			?>
			<div class="col-lg-4 col-xs-12">
				<div class="dashboard-stat dashboard-stat-v2 red-haze margin-bottom-20">
					<div class="visual">
						<i class="ion ion-android-storage"></i>
					</div>
					<div class="details">
						<div class="number">
							<span><?php echo Yii::app()->format->formatNumber($customFieldsCount);?></span>
						</div>
						<div class="desc"><?php echo Yii::t('list_segments', 'Custom fields');?></div>
					</div>
					<div class="small-box">
						<div class="small-box-footer">
							<a href="<?php echo Yii::app()->createUrl("list_fields/index", array("list_uid" => $list->list_uid));?>" class="btn red-haze btn-flat btn-xs pull-right" style="margin-right:3px;"><span class="fa fa-cog"></span> <?php echo Yii::t('app', 'Manage');?></a>&nbsp;
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="row">
			<?php if($list->customer_id != 0){  ?>
			<div class="col-lg-4 col-xs-12">
				<div class="dashboard-stat dashboard-stat-v2 grey-mint margin-bottom-20">
					<div class="visual">
						<i class="ion ion-folder"></i>
					</div>
					<div class="details">
						<div class="number">
							<span><?php echo Yii::app()->format->formatNumber($pagesCount);?></span>
						</div>
						<div class="desc"><?php echo Yii::t('list_segments', 'Pages');?></div>
					</div>
					<div class="small-box">
						<div class="small-box-footer">
							<a href="<?php echo Yii::app()->createUrl("list_page/index", array("list_uid" => $list->list_uid, 'type' => 'subscribe-form'));?>" class="btn grey-mint btn-flat btn-xs pull-right" style="margin-right:3px;"><span class="fa fa-cog"></span> <?php echo Yii::t('app', 'Manage');?></a>&nbsp;
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-xs-12">
				<div class="dashboard-stat dashboard-stat-v2 green-haze margin-bottom-20">
					<div class="visual">
						<i class="ion ion-ios7-photos"></i>
					</div>
					<div class="details">
						<div class="number">
							<span><?php echo Yii::t('list_forms', 'Forms');?></span>
						</div>
						<div class="desc"><?php echo Yii::t('app', 'Tools');?></div>
					</div>
					<div class="small-box">
						<div class="small-box-footer">
							<a href="<?php echo Yii::app()->createUrl("list_forms/index", array("list_uid" => $list->list_uid));?>" class="btn green-haze btn-flat btn-xs pull-right" style="margin-right:3px;"><span class="fa fa-eye"></span> <?php echo Yii::t('app', 'View');?></a>&nbsp;
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-4 col-xs-12">
				<div class="dashboard-stat dashboard-stat-v2 purple-plum margin-bottom-20">
					<div class="visual">
						<i class="ion ion-hammer"></i>
					</div>
					<div class="details">
						<div class="number">
							<span><?php echo Yii::t('lists', 'Tools');?></span>
						</div>
						<div class="desc"><?php echo Yii::t('lists', 'List tools');?></div>
					</div>
					<div class="small-box">
						<div class="small-box-footer">
							<a href="<?php echo Yii::app()->createUrl("list_tools/index", array("list_uid" => $list->list_uid));?>" class="btn purple-plum btn-flat btn-xs pull-right" style="margin-right:3px;"><span class="fa fa-eye"></span> <?php echo Yii::t('app', 'View');?></a>&nbsp;
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	
	<div class="margin-left-20 margin-right-20">
		<div class="row">
			<section class="col-lg-6" id="subscribers-growth-box" data-source="<?php echo $this->createUrl('lists/subscribers_growth', array('list_uid' => $list->list_uid));?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="subscribers-growth-header">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i>
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('lists', 'Subscribers growth');?>
							</span>
						</div>
						<div class="box-tools actions">
							<a href="javascript:;" class="btn green btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo Yii::t('app', 'The information is refreshed once at {n} minutes.', 10);?>">
								<span class="fa fa-info-circle"></span>
							</a>
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
			<section class="col-lg-6" id="campaigns-growth-box" data-source="<?php echo $this->createUrl('lists/campaigns_growth', array('list_uid' => $list->list_uid));?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="campaigns-growth-header">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i>
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('lists', 'Campaigns growth');?>
							</span>
						</div>					
						<div class="box-tools actions">
							<a href="javascript:;" class="btn green btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo Yii::t('app', 'The information is refreshed once at {n} minutes.', 10);?>">
								<span class="fa fa-info-circle"></span>
							</a>
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
			<section class="col-lg-6" id="deliverybounce-growth-box" data-source="<?php echo $this->createUrl('lists/delivery_bounce_growth', array('list_uid' => $list->list_uid));?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="deliverybounce-growth-header">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i>
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('lists', 'Delivery vs Bounces');?>
							</span>
						</div>						
						<div class="box-tools actions">
							<a href="javascript:;" class="btn green btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo Yii::t('app', 'The information is refreshed once at {n} minutes.', 10);?>">
								<span class="fa fa-info-circle"></span>
							</a>
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
			<section class="col-lg-6" id="unsubscribe-growth-box" data-source="<?php echo $this->createUrl('lists/unsubscribe_growth', array('list_uid' => $list->list_uid));?>">
				<div class="portlet light bordered no-margin margin-bottom-20">
					<div class="portlet-title" id="unsubscribe-growth-header">
						<div class="caption">
							<i class="fa fa-bar-chart-o"></i>
							<span class="caption-subject font-dark sbold uppercase">
								<?php echo Yii::t('lists', 'Unsubscribe growth');?>
							</span>
						</div>
						<div class="box-tools actions">
							<a href="javascript:;" class="btn green btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo Yii::t('app', 'The information is refreshed once at {n} minutes.', 10);?>">
								<span class="fa fa-info-circle"></span>
							</a>
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