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

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false 
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) { ?>
    
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon export"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('list_segments/index', 'list_uid' => $list->list_uid), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('list_segments_export/index', 'list_uid' => $list->list_uid, 'segment_uid' => $segment->segment_uid), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
			</div>
		</div>		
	</div>
	<div class="portlet-body">
		<div class="col-lg-2 col-xs-6">
			<div class="small-box bg-teal">
				<div class="inner">
					<h3><?php echo Yii::t('list_export', 'CSV');?></h3>
					<p><?php echo Yii::t('app', 'File');?></p>
				</div>
				<div class="icon">
					<i class="ion ion-ios7-download"></i>
				</div>
				<div class="small-box-footer">
					<div class="pull-left">
						&nbsp; <a href="<?php echo $this->createUrl('list_segments_export/csv', array('list_uid' => $list->list_uid, 'segment_uid' => $segment->segment_uid));?>" class="btn bg-teal btn-flat btn-xs"><span class="glyphicon glyphicon-export"></span> <?php echo Yii::t('list_export', 'Click to export');?></a>
					</div>
					<div class="clearfix"><!-- --></div>
				</div>
			</div>
		</div>
	</div>
<?php 
}
/**
 * This hook gives a chance to append content after the view file default content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * @since 1.3.3.1
 */
$hooks->doAction('after_view_file_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));