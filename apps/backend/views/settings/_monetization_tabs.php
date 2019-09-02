<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.4
 */
 
?>
<ul class="nav nav-tabs" style="margin:0;">
    <li class="<?php echo $this->getAction()->getId() == 'monetization' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/monetization')?>">
            <?php echo Yii::t('settings', 'Monetization');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'monetization_orders' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/monetization_orders')?>">
            <?php echo Yii::t('settings', 'Orders');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'monetization_invoices' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/monetization_invoices')?>">
            <?php echo Yii::t('settings', 'Invoices');?>
        </a>
    </li>
</ul>