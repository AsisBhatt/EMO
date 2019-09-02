<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.7
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
			<span class="glyphicon glyphicon-text-width"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('templates/gallery'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
			</div>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<?php foreach ($templates as $model) { ?>
			<div class="col-md-3">
				<div class="panel panel-default panel-template-box">
					<div class="panel-heading"><h3 class="panel-title"><?php echo $model->shortName;?></h3></div>
					<div class="panel-body">
						<img class="img-rounded" src="<?php echo $model->screenshotSrc;?>" />
					</div>
					<div class="panel-footer">
						<a href="<?php echo Yii::app()->createUrl("templates/gallery_import", array("template_uid" => $model->template_uid));?>" class="btn green btn-xs"><span class="glyphicon glyphicon-import"></span> <?php echo Yii::t('app', 'Import into my templates');?></a>
					</div>
				</div>
			</div>
			<?php } ?>
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