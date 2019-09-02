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
		<?php
		$baseUrl = Yii::app()->baseUrl; 
		$cs = Yii::app()->getClientScript();
		$cs->registerCssFile($baseUrl.'/assets/css/login.min.css');
	?>
    <!--[if lt IE 9]>
      <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="//oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
    <body class="login">
        <?php $this->afterOpeningBodyTag;?>
        <div class="logo">
            <?php echo OptionCustomization::buildHeaderLogoHtml();?>
        </div>
        <div class="content">		
			<div id="notify-container">
				<?php echo Yii::app()->notify->show();?>
			</div>
			<?php echo $content;?>
        </div>
        <div class="copyright">
            <?php $hooks->doAction('layout_footer_html', $this);?>
            <div class="page-footer-inner">
				Copyright &copy; 2017 <a href="https://www.beelift.com/" target="_blank">BeeLift</a>. All rights reserved.
			</div>
        </div>
    </body>
</html>