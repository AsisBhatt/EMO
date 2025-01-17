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

<div class="callout callout-info">
    <?php echo $fromText; ?>
</div>

<div class="box box-primary">
    <div class="box-header">
        <div class="pull-left">
            <h3 class="box-title">				
                <span class="glyphicon glyphicon-share"></span> <?php echo $pageHeading;?> 
            </h3>
        </div>
        <div class="pull-right">
            <?php echo CHtml::link(Yii::t('lists', 'Back to tools'), array('list_tools/index', 'list_uid' => $list->list_uid), array('class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Back')));?>
            <?php echo CHtml::link(Yii::t('lists', 'Back to list overview'), array('lists/overview', 'list_uid' => $list->list_uid), array('class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Back')));?>
        </div>
        <div class="clearfix"><!-- --></div>
    </div>
    <div class="box-body" id="copy-list-subscribers-box" data-attributes='<?php echo $jsonAttributes;?>'>
        <span class="counters">
            <?php echo Yii::t('list_import', 'From a total of {total} subscribers, so far {totalProcessed} have been processed, {successfullyProcessed} successfully and {errorProcessing} with errors. {percentage} completed.', array(
                '{total}'                   => '<span class="total" data-bind="text: total">0</span>',
                '{totalProcessed}'          => '<span class="total-processed" data-bind="text: processedTotal">0</span>',
                '{successfullyProcessed}'   => '<span class="success" data-bind="text: processedSuccess">0</span>',
                '{errorProcessing}'         => '<span class="error" data-bind="text: processedError">0</span>',
                '{percentage}'              => '<span class="percentage" data-bind="text: percentage">0</span>%',
            ));?>
        </span>
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-bind="style: {width: widthPercentage()}">
                <span class="sr-only"><span data-bind="text: percentage">0</span>% <?php echo Yii::t('app', 'Complete');?></span>
            </div>
        </div>
        <div class="alert alert-info log-info" data-bind="text: progressText">
        </div>
        <div class="log-errors"></div>
    </div>
    <div class="box-footer"></div>
</div>