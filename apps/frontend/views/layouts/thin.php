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
    <meta charset="<?php echo Yii::app()->charset;?>">
    <title><?php echo CHtml::encode($pageMetaTitle);?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo CHtml::encode($pageMetaDescription);?>">
    <!--[if lt IE 9]>
      <script src="//oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="//oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
    <body class="<?php echo $this->bodyClasses;?>">
        <?php $this->afterOpeningBodyTag;?>
        <header class="header">
            <?php echo OptionCustomization::buildHeaderLogoHtml();?>
            <nav class="navbar navbar-static-top" role="navigation"></nav>
        </header>
        <div class="wrapper">
            <div class="row" style="height: 50px;"><!-- --></div>
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <div id="notify-container">
                    <?php echo Yii::app()->notify->show();?>
                </div>
                <?php echo $content;?>
            </div>
            <div class="col-lg-3"></div>
            <div class="clearfix"><!-- --></div>
        </div>
        <footer>
            <?php $hooks->doAction('layout_footer_html', $this);?>
            <div class="clearfix"><!-- --></div>
        </footer>
    </body>
</html>