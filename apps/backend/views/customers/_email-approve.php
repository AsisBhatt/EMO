<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */
 
?>

<!-- START CONTENT -->
<?php echo Yii::t('customers', 'Congratulations, your account on {site} has been approved and you can now login using the url below:', array('{site}' => Yii::app()->options->get('system.common.site_name')));?><br />
<?php $url = Yii::app()->apps->getAppUrl('customer', 'guest/index', true);?>
<a href="<?php echo $url;?>"><?php echo $url;?></a><br /><br />
<?php echo Yii::t('customers', 'If for some reason you cannot click the above url, please paste this one into your browser address bar:')?><br />
<?php echo $url;?>
<!-- END CONTENT-->