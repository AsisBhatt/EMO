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
				<i class="fa fa-info-circle"></i>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo Yii::t('dashboard', 'At a glance');?>
				</span>       
			</div>
        </div>
        <div class="portlet-body">
            <div class="row">
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 yellow-crusta margin-bottom-20" href="<?php echo $this->createUrl('customers/index');?>">
						<div class="visual">
							<i class="icon-user"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.customersCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Customers');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								<?php echo Yii::t('dashboard', 'More info');?> <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 purple margin-bottom-20" href="javascript:;">
						<div class="visual">
							<i class="icon-list"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.listsCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Lists');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								<?php echo Yii::t('dashboard', 'No detailed info');?> <i class="fa fa-exclamation-circle"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 green orange margin-bottom-20" href="javascript:;">
						<div class="visual">
							<i class="icon-envelope"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.subscribersCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Unique subscribers');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								<?php echo Yii::t('dashboard', 'No detailed info');?> <i class="fa fa-exclamation-circle"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 blue-sharp margin-bottom-20" href="javascript:;">
						<div class="visual">
							<i class="icon-envelope"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.allSubscribersCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Total subscribers');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								<?php echo Yii::t('dashboard', 'No detailed info');?> <i class="fa fa-exclamation-circle"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 red margin-bottom-20" href="<?php echo $this->createUrl('sms/management'); ?>">
						<div class="visual">
							<i class="fa fa-server"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.allSubscribersCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Management');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								<?php echo Yii::t('dashboard', 'No detailed info');?> <i class="fa fa-exclamation-circle"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 green-meadow margin-bottom-20" href="<?php echo $this->createUrl('sms/financial'); ?>">
						<div class="visual">
							<i class="fa fa-money"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.allSubscribersCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Financial');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								<?php echo Yii::t('dashboard', 'No detailed info');?> <i class="fa fa-exclamation-circle"></i>
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