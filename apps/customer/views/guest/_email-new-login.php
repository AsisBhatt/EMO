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

<!-- START CONTENT -->
<?php $url = Yii::app()->createAbsoluteUrl('guest/index');?>
<?php echo Yii::t('customers', 'Your new login is:');?><br />
<?php echo Yii::t('customers', 'Email');?>: <?php echo CHtml::encode($customer->email);?><br />
<?php echo Yii::t('customers', 'Password');?>: <?php echo $randPassword;?><br /><br />
<?php echo Yii::t('customers', 'You can login by clicking <a href="{loginUrl}">here</a>.', array(
    '{loginUrl}' => $url,
));?><br />
<?php echo Yii::t('customers', 'If for some reason the link doesn\'t work, please copy the following url into your browser address bar:');?><br />
<?php echo $url;?>
<!-- END CONTENT-->
