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
		<?php $this->afterOpeningBodyTag;?>
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
					<div class="top-menu">
						<ul class="nav navbar-nav pull-right">
							<li class="dropdown dropdown-user">
								<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
                                    <img src="<?php echo Yii::app()->user->getModel()->getAvatarUrl(90, 90);?>" class="img-circle"/>
                                    <span class="username username-hide-on-mobile"><?php echo ($fullName = Yii::app()->user->getModel()->getFullName()) ? CHtml::encode($fullName) : Yii::t('app', 'Welcome');?></span>
                                    <i class="fa fa-angle-down"></i>
                                </a>
								<ul class="dropdown-menu dropdown-menu-default">
                                    <li>
                                        <a  href="<?php echo $this->createUrl('account/index');?>"><i class="icon-user"></i><?php echo Yii::t('app', 'My Profile');?></a>
                                    </li>
                                    <li>
                                        <a href="<?php echo $this->createUrl('account/logout');?>"><i class="icon-key"></i><?php echo Yii::t('app', 'Log Out');?></a>
                                    </li>
                                </ul>
							</li>
						</ul>
						<!-- <nav class="navbar navbar-static-top" role="navigation"> -->
							<!-- <!--<a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button"> -->
								<!-- <span class="sr-only">Toggle navigation</span> -->
								<!-- <span class="icon-bar"></span> -->
								<!-- <span class="icon-bar"></span> -->
								<!-- <span class="icon-bar"></span> -->
							<!-- </a>--> 
							<!-- <div class="navbar-right"> -->
								<!-- <ul class="nav navbar-nav"> -->
									<!-- <li class="dropdown user user-menu"> -->
										<!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown"> -->
											<!-- <i class="glyphicon glyphicon-user"></i> -->
											<!-- <span><?php echo ($fullName = Yii::app()->user->getModel()->getFullName()) ? CHtml::encode($fullName) : Yii::t('app', 'Welcome');?> <i class="caret"></i></span> -->
										<!-- </a> -->
										<!-- <ul class="dropdown-menu"> -->
											<!-- <li class="user-header bg-light-blue"> -->
												<!-- <img src="<?php echo Yii::app()->user->getModel()->getAvatarUrl(90, 90);?>" class="img-circle"/> -->
												<!-- <p> -->
													<!-- <?php echo ($fullName = Yii::app()->user->getModel()->getFullName()) ? CHtml::encode($fullName) : Yii::t('app', 'Welcome');?> -->
												<!-- </p> -->
											<!-- </li> -->
											<!-- <li class="user-footer"> -->
												<!-- <div class="pull-left"> -->
													<!-- <a href="<?php echo $this->createUrl('account/index');?>" class="btn btn-default btn-flat"><?php echo Yii::t('app', 'My Account');?></a> -->
												<!-- </div> -->
												<!-- <div class="pull-right"> -->
													<!-- <a href="<?php echo $this->createUrl('account/logout');?>" class="btn btn-default btn-flat"><?php echo Yii::t('app', 'Logout');?></a> -->
												<!-- </div> -->
											<!-- </li> -->
										<!-- </ul> -->
									<!-- </li> -->
								<!-- </ul> -->
							<!-- </div> -->
						<!-- </nav> -->
					</div>
				</div>
			</header>
			<div class="clearfix"> </div>
			<div class="page-container">
				<div class="page-sidebar-wrapper">
					<div class="page-sidebar navbar-collapse collapse">
							<!-- <div class="user-panel"> -->
								<!-- <div class="pull-left image"> -->
									<!-- <img src="<?php echo Yii::app()->user->getModel()->getAvatarUrl(90, 90);?>" class="img-circle" /> -->
								<!-- </div> -->
								<!-- <div class="pull-left info"> -->
									<!-- <p><?php echo ($fullName = Yii::app()->user->getModel()->getFullName()) ? CHtml::encode($fullName) : Yii::t('app', 'Welcome');?></p> -->
								<!-- </div> -->
							<!-- </div> -->
							<?php $this->widget('backend.components.web.widgets.LeftSideNavigationWidget');?>      
							<?php //if (Yii::app()->options->get('system.common.show_backend_timeinfo', 'no') == 'yes' && version_compare(MW_VERSION, '1.3.4.4', '>=')) { ?> 
							<!-- <div class="timeinfo"> -->
								<!-- <div class="pull-left"><?php// echo Yii::t('app', 'Local time')?></div> -->
								<!-- <div class="pull-right"><?php //echo Yii::app()->user->getModel()->dateTimeFormatter->formatDateTime();?></div> -->
								<!-- <div class="clearfix"></div> 
								<!-- <div class="pull-left"><?php //echo Yii::t('app', 'System time')?></div> -->
								<!-- <div class="pull-right"><?php// echo date('Y-m-d H:i:s');?></div> -->
								<!-- <div class="clearfix"></div> -->
							<!-- </div>              -->
							<?php //} ?> 
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
			<!-- <div class="wrapper row-offcanvas row-offcanvas-left"> -->
				<!-- <aside class="left-side sidebar-offcanvas"> -->
					
				<!-- </aside> -->
				<!-- <aside class="right-side"> -->
					
				<!-- </aside> -->
			<!-- </div> -->
			<div class="page-footer">
				<?php $hooks->doAction('layout_footer_html', $this);?>
				<div class="page-footer-inner">
					<?php echo Yii::t('app', 'Copyright &copy; 2017 <a href="javascript:;">Edatamediagroup</a> & <a href="javascript:;">BeeLift</a>. All rights reserved.', array(
						'{version}' => MW_VERSION,
						'{seconds}' => round(Yii::getLogger()->getExecutionTime(), 3),
						'{memory}'  => round(Yii::getLogger()->getMemoryUsage() / 1024 / 1024, 3),
					));?>
				</div>
				<div class="scroll-to-top">
                    <i class="fa fa-angle-double-up"></i>
                </div>
			</div>
		</div>
		<div class="quick-nav-overlay"></div>
    </body>
</html>