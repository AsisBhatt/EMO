<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5
 */
 
?>
<ul class="nav nav-tabs" style="margin:0;">
    <li class="<?php echo $this->getAction()->getId() == 'redis_queue' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('settings/redis_queue')?>">
            <?php echo Yii::t('settings', 'Redis');?>
        </a>
    </li>
</ul>