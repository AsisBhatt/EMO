<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @subpackage Payment Gateway Offline
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */

echo CHtml::form(array('price_plans/order'), 'post', array());
?>
<p class="text-muted well well-sm no-shadow" style="margin-top: 10px; padding: 16px">
    <?php echo Yii::t('ext_payment_gateway_offline', 'Offline payment');?><br />
</p>
<p><?php echo nl2br($model->description);?></p>
<p><button class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> <?php echo Yii::t('price_plans', 'Place offline order')?></button></p>
<?php echo CHtml::endForm(); ?>