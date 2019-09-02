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

<div class="alert alert-success">
    <?php
    $text = 'The import process will start shortly. <br />
    While the import is running it is recommended you leave this page as it is and wait for the import to finish.<br />
    The importer runs in batches of {subscribersPerBatch} subscribers with a pause of {pause} seconds between the batches, therefore 
    the import process might take a while depending on your file size and number of subscribers to import.<br />
    This is a tedious process, so sit tight and wait for it to finish.';
    echo Yii::t('list_import', StringHelper::normalizeTranslationString($text), array(
        '{subscribersPerBatch}' => $importAtOnce,
        '{pause}' => $pause,
    ));
    ?>
</div>


    <div class="portlet-title">
        <div class="caption">
			<span class="glyphicon glyphicon-import"></span>
            <span class="caption-subject font-dark sbold uppercase">
                <?php echo Yii::t('list_import', 'Database import progress');?> 
            </span>
        </div>
        <div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('list_import', 'Back to import options'), array('list_import/index', 'list_uid' => $list->list_uid), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Back')));?>
			</div>
        </div>
    </div>
    <div class="portlet-body" id="database-import" data-model="<?php echo $import->modelName;?>" data-pause="<?php echo (int)$pause;?>" data-iframe="<?php echo $this->createUrl('list_import/ping');?>" data-attributes='<?php echo CJSON::encode($import->attributes);?>'>
        <span class="counters">
            <?php echo Yii::t('list_import', 'From a total of {total} subscribers, so far {totalProcessed} have been processed, {successfullyProcessed} successfully and {errorProcessing} with errors. {percentage} completed.', array(
                '{total}' => '<span class="total">0</span>',
                '{totalProcessed}' => '<span class="total-processed">0</span>',
                '{successfullyProcessed}' => '<span class="success">0</span>',
                '{errorProcessing}' => '<span class="error">0</span>',
                '{percentage}'  => '<span class="percentage">0%</span>',
            ));?>
        </span>
        <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                <span class="sr-only">0% <?php echo Yii::t('app', 'Complete');?></span>
            </div>
        </div>
        <div class="alert alert-info log-info">
             <?php echo Yii::t('list_import', 'The import process is starting, please wait...');?>
        </div>
        <div class="log-errors"></div>
    </div>    