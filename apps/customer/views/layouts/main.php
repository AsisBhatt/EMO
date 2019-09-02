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

?>
<!DOCTYPE html>
<html dir="<?php echo $this->htmlOrientation;?>">
<head>
	<link rel="icon" type="image/ico" href="<?php echo Yii::app()->baseUrl.'/assets/img/favicon.ico'; ?>">
    <meta charset="<?php echo Yii::app()->charset;?>">
    <title><?php echo CHtml::encode($pageMetaTitle);?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo CHtml::encode($pageMetaDescription);?>">
    <!--[if lt IE 9]>
      <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="//oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<?php 
//Old "$this->bodyClasses" Body Class Change this class as designer Requirment .
//echo $this->bodyClasses;
?>
	<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
		<div class="page-wrapper">
			<?php 
				$this->afterOpeningBodyTag;
				$customer   = Yii::app()->customer->getModel(); 
				$customer_validity = $customer->getRemainingDay();
			?>
			<header class="page-header navbar navbar-fixed-top">
				<div class="page-header-inner">
					<div class="page-logo">
						<?php echo OptionCustomization::buildHeaderLogoHtml();?>
						<div class="menu-toggler sidebar-toggler">
							<span></span>
						</div>
					</div>
					<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
                        <span></span>
                    </a>
					<ul class="plan-content">
						<li>
							<h5 data-toggle="tooltip" data-placement="bottom" title="<?php echo $customer->getPlan(); ?>"><?php echo $customer->getPlan(); ?></h5>
						</li>
						<li>
							<div class="progress-info">
								<div class="progress">
									<span class="progress-bar progress-bar-<?php echo $customer_validity['prog_css_class']; ?>" style="width:<?php echo $customer_validity['get_percentage']; ?>%;">
										<span class="sr-only"><?php echo $customer_validity['get_percentage']; ?>% progress</span>
									</span>
								</div>
								<div class="status">
									<div class="status-title"><span <?php echo ($customer_validity['remaining_day'] == 0 ? 'style="color:red;"' : ''); ?>><?php echo $customer_validity['remaining_day']; ?></span> Day remaining</div> 
								</div>
							</div>
						</li>
						<li>
							<div class="btn-progress">
								<a class="btn btn-warning" href="https://support.beelift.com/" target="_blank">Upgrade Plan</a>
							</div>
						</li>
					</ul>
					<div class="top-menu">
						
						<ul class="nav navbar-nav pull-right">
						 

						<?php if($customer->company->portal_customer == 'EDATA'){ ?>
						
							<li class="dropdown tasks-menu">
								<a href="https://support.edatamediagroup.com/" target="_blank" class="header-messages dropdown-toggle">
									<i class="icon-support"></i>
									<span class="username username-hide-on-mobile">
										Support
									</span>
								</a>
							</li> 
						<?php 
							//if($customer->company->portal_customer == 'BEELIFT')
							}else{  
						?>
							<li class="dropdown tasks-menu">
								<a href="https://support.beelift.com/" target="_blank" class="header-messages dropdown-toggle">
									<i class="icon-support"></i>
									<span class="username username-hide-on-mobile">
										Support
									</span>
								</a>
							</li>
						<?php } ?>
						
						<li class="dropdown messages-menu">
							<a href="javascript:;" class="header-messages dropdown-toggle" data-url="<?php echo Yii::app()->createUrl('messages/header');?>" data-toggle="dropdown" title="<?php echo Yii::t('customers', 'Messages');?>">
								<i class="fa fa-envelope"></i>
								<span class="label label-success badge badge-default"></span>
							</a>
							<ul class="dropdown-menu">
								<li class="header"> <!----> </li>
								<li>
									<ul class="menu">
										<li></li>
									</ul>
								</li>
								<li class="footer">
									<a href="<?php echo Yii::app()->createUrl('messages/index');?>"><?php echo Yii::t('messages', 'See all messages');?></a>
								</li>
							</ul>
						</li>
							<li class="dropdown tasks-menu">
								<a href="javascript;;" class="header-account-stats dropdown-toggle" data-url="<?php echo Yii::app()->createUrl('account/usage');?>" data-toggle="dropdown" title="<?php echo Yii::t('customers', 'Account usage');?>">
									<i class="fa fa-tasks"></i>
								</a>
								<ul class="dropdown-menu">
									<li class="header"><?php echo Yii::t('customers', 'Account usage');?></li>
									<li>
										<ul class="menu">
											<li>
												<a href="#"><h3><?php echo Yii::t('app', 'Please wait, processing...');?></h3></a>
											</li>
										</ul>
									</li>
									<li class="footer">
										<a href="javascript:;" class="header-account-stats-refresh"><?php echo Yii::t('app', 'Refresh');?></a>
									</li>
								</ul>
							</li>	
							<li class="dropdown dropdown-user">
								<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
									<img src="<?php echo Yii::app()->customer->getModel()->getAvatarUrl(90, 90);?>" class="img-circle"/>
									<span class="username username-hide-on-mobile"><?php echo ($fullName = Yii::app()->customer->getModel()->getFullName()) ? CHtml::encode($fullName) : Yii::t('app', 'Welcome');?></span>
									<i class="fa fa-angle-down"></i>
								</a>
								<ul class="dropdown-menu dropdown-menu-default">
									<li>
										<a href="<?php echo $this->createUrl('account/index');?>"><i class="icon-user"></i><?php echo Yii::t('app', 'My Account');?></a>
									</li>
									<li>
										<a href="<?php echo $this->createUrl('price-plans/index');?>">
										<i class="icon-user"></i><?php echo Yii::t('app', 'My Plans');?></a>
									</li>
									<li>
										<a href="<?php echo $this->createUrl('account/logout');?>"><i class="icon-key"></i><?php echo Yii::t('app', 'Logout');?></a>
									</li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</header>
			<div class="clearfix"> </div>
			<div class="page-container">
				<div class="page-sidebar-wrapper">
					<div class="page-sidebar navbar-collapse collapse">
						<!-- <div class="user-panel"> -->
							<!-- <div class="pull-left image"> -->
								<!-- <img src="<?php echo Yii::app()->customer->getModel()->getAvatarUrl(90, 90);?>" class="img-circle" /> -->
							<!-- </div> -->
							<!-- <div class="pull-left info"> -->
								<!-- <p><?php echo ($fullName = Yii::app()->customer->getModel()->getFullName()) ? CHtml::encode($fullName) : Yii::t('app', 'Welcome');?></p> -->
							<!-- </div> -->
						<!-- </div> -->
						<div class="navbar_content">
							<?php $this->widget('customer.components.web.widgets.LeftSideNavigationWidget');?>
							
							<?php if (Yii::app()->options->get('system.common.show_customer_timeinfo', 'no') == 'yes' && version_compare(MW_VERSION, '1.3.4.4', '>=')) { ?>
							<button type="button" class="btn btn-secondary time-tooltip" data-toggle="tooltip" data-html="true" title="<?php echo Yii::t('app', 'EST time')?>: <?php echo Yii::app()->customer->getModel()->dateTimeFormatter->formatDateTime();?><br/><?php echo Yii::t('app', 'Server time')?>: <?php echo date('Y-m-d H:i:s');?>">
							  <i class="icon-info"></i>
							</button>
							<div class="timeinfo">
								<!--<div class="pull-left"><?php //echo Yii::t('app', 'EST time')?></div>-->
								<!--<div class="pull-right"><?php //echo Yii::app()->customer->getModel()->dateTimeFormatter->formatDateTime();?></div>-->
								<div class="clearfix"></div>
								<div class="pull-left"><?php echo Yii::t('app', 'Server time')?></div>
								<div class="pull-right"><?php echo Yii::app()->customer->getModel()->dateTimeFormatter->formatDateTime();?>
								<?php //echo date('Y-m-d H:i:s');?></div>
								<div class="clearfix"></div>
							</div>
							<?php } ?>
						</div>
						<div>
							<div class="image">
								<img src="<?php echo Yii::app()->customer->getModel()->getAvatar1Url(230, 100);?>" class="img-responsive" style="margin-bottom: 10px;margin-left: 2px;" />
							</div>
						</div>
					</div>
				</div>
				<div class="page-content-wrapper">
					<div class="page-content">
						<div class="page-bar">
							<?php
							$this->widget('zii.widgets.CBreadcrumbs', array(
								'tagName'               => 'ul',
								'separator'             => '',
								'htmlOptions'           => array('class' => 'page-breadcrumb'),
								'activeLinkTemplate'    => '<li><a href="{url}">{label}</a><i class="fa fa-circle"></i></li>',
								'inactiveLinkTemplate'  => '<li class="active">{label} </li>',
								'homeLink'              => CHtml::tag('li', array(), CHtml::link(Yii::t('app', 'Dashboard'), $this->createUrl('dashboard/index')) . '<i class="fa fa-circle"></i>' ),
								'links'                 => $hooks->applyFilters('layout_page_breadcrumbs', $pageBreadcrumbs),
							));
							?>
						</div>
						<h1 class="page-title"><?php echo !empty($pageHeading) ? $pageHeading : '&nbsp;';?></h1>
						<div id="notify-container" class="margin-bottom-20">
							<?php echo Yii::app()->notify->show();?>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div class="portlet light portlet-fit bordered">
									<?php echo $content;?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="page-footer">
				<?php $hooks->doAction('layout_footer_html', $this);?>
				<div class="page-footer-inner">
					Copyright &copy; <?php echo date('Y'); ?> <a href="http://www.edatamediagroup.com">Edatamediagroup</a> & <a href="javascript:;">BeeLift</a>. All rights reserved.
				</div>
				<div class="scroll-to-top">
                    <i class="fa fa-angle-double-up"></i>
                </div>
			</div>
		</div>
		<div class="quick-nav-overlay"></div>
    </body>
</html>
