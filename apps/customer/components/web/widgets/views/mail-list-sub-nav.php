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

<div class="btn-group">
    <button type="button" class="btn green btn-circle btn-sm dropdown-toggle" data-toggle="dropdown">
        <?php echo Yii::t('app', 'Quick links');?> <span class="caret"></span>
    </button>
    <?php $this->controller->widget('zii.widgets.CMenu', array(
        'items'         => $this->getNavItems(),
        'htmlOptions'   => array(
            'class' => 'dropdown-menu',
            'role'  => 'menu'
        ),
    ));?>
</div>    