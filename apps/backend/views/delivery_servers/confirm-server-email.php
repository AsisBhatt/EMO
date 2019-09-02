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

<?php 
echo Yii::t('servers', 'Your confirmation key for the server "<em>{hostname}</em>" is:', array(
    '{hostname}' => $server->hostname,
));
?>
<br />
<a href="<?php echo ($url = $this->createAbsoluteUrl('delivery_servers/confirm', array('key' => $server->confirmation_key)));?>"><?php echo $server->confirmation_key;?></a><br />
<br />
<?php echo Yii::t('servers', 'If for some reason the above link does not work, please type the following url in your browser address bar:');?><br />
<?php echo $url;?>