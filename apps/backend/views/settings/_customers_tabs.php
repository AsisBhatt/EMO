<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4
 */
 
?>
<div class="alert alert-success margin-bottom-15">
    <?php echo Yii::t('settings', 'Please note that most of the customer settings will also be found in customer groups allowing you a fine graned control over your customers and their limits/permissions.');?>
</div>
<ul class="nav nav-tabs" style="margin:0;">
    <li class="<?php echo $this->getAction()->getId() == 'customer_common' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/customer_common')?>">
            <?php echo Yii::t('settings', 'Common');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'customer_servers' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/customer_servers')?>">
            <?php echo Yii::t('settings', 'Servers');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'customer_domains' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/customer_domains')?>">
            <?php echo Yii::t('settings', 'Domains');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'customer_lists' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/customer_lists')?>">
            <?php echo Yii::t('settings', 'Lists');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'customer_campaigns' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/customer_campaigns')?>">
            <?php echo Yii::t('settings', 'Campaigns');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'customer_quota_counters' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/customer_quota_counters')?>">
            <?php echo Yii::t('settings', 'Quota counters');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'customer_sending' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/customer_sending')?>">
            <?php echo Yii::t('settings', 'Sending');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'customer_cdn' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/customer_cdn')?>">
            <?php echo Yii::t('settings', 'CDN');?>
        </a>
    </li>
    <li class="<?php echo $this->getAction()->getId() == 'customer_registration' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/customer_registration')?>">
            <?php echo Yii::t('settings', 'Registration');?>
        </a>
    </li>
</ul>