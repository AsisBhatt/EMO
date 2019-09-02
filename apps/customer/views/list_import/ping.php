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


    <div class="portlet-title">
        <div class="caption">
			<span class="glyphicon glyphicon-list-alt"></span>
            <span class="caption-subject font-dark sbold uppercase">
                <?php echo Yii::t('list_import', 'Ping page');?>
            </span>
        </div>
    </div>
    <div class="portlet-body">
        <div id="ping">
            <?php echo Yii::t('list_import', 'This is the PING page, called via iframe when importing files. There is no reason for your to access it.');?>
        </div>
    </div>
