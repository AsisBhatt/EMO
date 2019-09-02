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
					<a class="dashboard-stat dashboard-stat-v2 blue-oleo margin-bottom-20" href="<?php echo $this->createUrl('campaigns/index');?>">
						<div class="visual">
							<i class="icon-settings"></i>
						</div>
						<div class="details">
							<!--<div class="number">
								<span data-bind="text: glance.campaignsCount"></span>
							</div>-->
							<div class="desc"><?php echo Yii::t('dashboard', 'Ez Email');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 blue-oleo margin-bottom-20" href="<?php echo $this->createUrl('campaigns/index');?>">
						<div class="visual">
							<i class="icon-settings"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.campaignsCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Campaigns');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 purple margin-bottom-20" href="<?php echo $this->createUrl('lists/index');?>">
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
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<?php if (!empty($canSegmentList)) {?>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 yellow margin-bottom-20" href="<?php echo $this->createUrl('lists/index');?>">
						<div class="visual">
							<i class="icon-globe"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.segmentsCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Segments');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<?php } ?>			
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 green orange margin-bottom-20" href="<?php echo $this->createUrl('lists/index');?>">
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
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 blue-sharp margin-bottom-20" href="<?php echo $this->createUrl('lists/index');?>">
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
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 green-steel margin-bottom-20" href="<?php echo $this->createUrl('templates/index');?>">
						<div class="visual">
							<i class="fa fa-columns"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.templatesCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Templates');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 grey-mint margin-bottom-20" href="<?php echo $this->createUrl('api_keys/index');?>">
						<div class="visual">
							<i class="fa fa-star-o"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.apiKeysCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'API keys');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 red margin-bottom-20" href="<?php echo $this->createUrl('email/management');?>">
						<div class="visual">
							<i class="fa fa-server"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.apiKeysCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Management');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 green-meadow margin-bottom-20" href="<?php echo $this->createUrl('email/financial');?>">
						<div class="visual">
							<i class="fa fa-money"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.apiKeysCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Financial');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>
				<div class="col-lg-4 col-xs-12">
					<a class="dashboard-stat dashboard-stat-v2 green-blue margin-bottom-20" href="<?php echo $this->createUrl('email/reports');?>">
						<div class="visual">
							<i class="fa fa-file"></i>
						</div>
						<div class="details">
							<div class="number">
								<span data-bind="text: glance.apiKeysCount"></span>
							</div>
							<div class="desc"><?php echo Yii::t('dashboard', 'Reports');?></div>
						</div>
						<div class="small-box">
							<div class="small-box-footer">
								More info <i class="fa fa-arrow-circle-right"></i>
							</div>
						</div>
					</a>
				</div>	
			</div>
			<div class="alert alert-success">
				Grow your business with the BeeLift do-it-yourself text & picture messaging platform, BeeLift Texting! Mass texting with SMS helps you connect with customers and employees BeeLift Texting is a refreshingly simple web-based text messaging service. We've spent years building an intuitive user interface that thousands of clients use every day. Point and click to create groups of contacts. Whenever you want to send out a mass text message.
			</div>
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