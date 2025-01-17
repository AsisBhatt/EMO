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

$subscriberUpdateUrl = Yii::app()->apps->getAppUrl('customer', sprintf('lists/%s/subscribers/%s/update', $list->list_uid, $subscriber->subscriber_uid), true); 
?>

<div class="notification">
    <?php echo Yii::t('lists', 'A new subscriber has been added to your list.');?><br />
    <?php echo Yii::t('lists', 'List name');?>: <?php echo $list->name;?><br />
    <?php echo Yii::t('lists', 'Details url');?>: <?php echo CHtml::link($subscriberUpdateUrl, $subscriberUpdateUrl);?><br />
    <br />
    <?php echo Yii::t('lists', 'Submitted data');?>:<br />
    <?php foreach ($fields as $fieldLabel => $fieldValue) { ?>
    <?php echo $fieldLabel; ?>: <?php echo $fieldValue;?><br />
    <?php } ?>
</div>