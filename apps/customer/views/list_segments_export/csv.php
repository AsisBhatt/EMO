<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.8
 */
 
?>
	<div class="alert alert-succes">
		<?php 
		$text = 'The export process will start shortly. <br />
		While the export is running it is recommended you leave this page as it is and wait for the export to finish.<br />
		The exporter runs in batches of {subscribersPerBatch} subscribers per file with a pause of {pause} seconds between the batches, therefore 
		the export process might take a while depending on your list size and number of subscribers to export.<br />
		This is a tedious process, so sit tight and wait for it to finish.';
		echo Yii::t('list_export', StringHelper::normalizeTranslationString($text), array(
			'{subscribersPerBatch}' => $maxFileRecords,
			'{pause}' => $pause,
		));
		?>
	</div>
    <div class="portlet-title">
        <div class="caption">
			<span class="glyphicon glyphicon-export"></span>
            <span class="caption-subject font-dark sbold uppercase">
                <?php echo Yii::t('list_export', 'CSV export progress');?> 
            </span>
        </div>
        <div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('list_export', 'Back to export options'), array('list_segments_export/index', 'list_uid' => $list->list_uid, 'segment_uid' => $segment->segment_uid), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Back')));?>
			</div>
        </div>
    </div>
    <div class="portlet-body" id="csv-export" data-pause="<?php echo (int)$pause;?>" data-iframe="<?php echo $this->createUrl('list_import/ping');?>" data-attributes='<?php echo CJSON::encode($export->attributes);?>'>
        <span class="counters">
            <?php echo Yii::t('list_export', 'From a total of {total} subscribers, so far {totalProcessed} have been processed, {successfullyProcessed} successfully and {errorProcessing} with errors. {percentage} completed.', array(
                '{total}' => '<span class="total">0</span>',
                '{totalProcessed}' => '<span class="total-processed">0</span>',
                '{successfullyProcessed}' => '<span class="success">0</span>',
                '{errorProcessing}' => '<span class="error">0</span>',
                '{percentage}'  => '<span class="percentage">0%</span>',
            ));?>
        </span>
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                <span class="sr-only">0% <?php echo Yii::t('app', 'Complete');?></span>
            </div>
        </div>
        <div class="alert alert-info log-info">
             <?php echo Yii::t('list_export', 'The export process is starting, please wait...');?>
        </div>
        <div class="log-errors"></div>
    </div>
   