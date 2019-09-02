<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.2
 */

?>

<div class="form-group">
    <?php echo CHtml::activeLabelEx($trackUnsubscribe, 'reason');?>
    <?php echo CHtml::activeTextArea($trackUnsubscribe, 'reason', $trackUnsubscribe->getHtmlOptions('reason', array(
        'name' => 'unsubscribe_reason',
        'id'   => 'unsubscribe_reason',
    ))); ?>
    <?php echo CHtml::error($trackUnsubscribe, 'reason');?>
</div>
