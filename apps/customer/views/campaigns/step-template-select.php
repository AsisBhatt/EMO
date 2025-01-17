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
    <div class="alert alert-success">
        <?php
        $text = 'Please note, once you select a template, the existing content of the campaign template will be overridden by the one you have selected.<br />
        If you don\'t want this, then just click on the cancel button and you will be redirect back to the inital template page.';
        echo Yii::t('campaigns', StringHelper::normalizeTranslationString($text));
        ?>
    </div>
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-envelope"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">	
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('campaigns/template', 'campaign_uid' => $campaign->campaign_uid), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
			</div>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<?php foreach ($templates as $model) { ?>
				<div class="col-md-4">
					<div class="panel panel-default panel-template-box">
						<div class="panel-heading"><h3 class="panel-title"><?php echo $model->shortName;?></h3></div>
						<div class="panel-body">
							<a title="<?php echo Yii::t('email_templates',  'Preview');?> <?php echo CHtml::encode($model->name);?>" href="javascript:;" onclick="window.open('<?php echo $this->createUrl('templates/preview', array('template_uid' => $model->template_uid));?>','<?php echo Yii::t('email_templates',  'Preview') . ' '.CHtml::encode($model->name);?>', 'height=600, width=600'); return false;">
								<img class="img-rounded" src="<?php echo $model->screenshotSrc;?>" />
							</a>
						</div>
						<div class="panel-footer">
							<a href="<?php echo Yii::app()->createUrl("campaigns/template", array("campaign_uid" => $campaign->campaign_uid, "do" => "select", "template_uid" => $model->template_uid));?>" class="btn green"><span class="glyphicon glyphicon-screenshot"></span> <?php echo Yii::t('app', 'Choose');?></a>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>		
		<div class="clearfix"><!-- --></div>
		<?php if ($pages->pageCount > 1) {?>
			<div class="box-footer">
				<div class="pull-right">
				<?php $this->widget('CLinkPager', array(
					'pages'         => $pages,
					'htmlOptions'   => array('id' => 'templates-pagination', 'class' => 'pagination'),
					'header'        => false,
					'cssFile'       => false                    
				)); ?>
				</div>
				<div class="clearfix"><!-- --></div>
			</div>
		<?php } ?>
		
		<div class="box-footer">
			<div class="wizard">
				<ul class="steps">
					<li class="complete"><a href="<?php echo $this->createUrl('campaigns/update', array('campaign_uid' => $campaign->campaign_uid));?>"><?php echo Yii::t('campaigns', 'Details');?></a><span class="chevron"></span></li>
					<li class="complete"><a href="<?php echo $this->createUrl('campaigns/setup', array('campaign_uid' => $campaign->campaign_uid));?>"><?php echo Yii::t('campaigns', 'Setup');?></a><span class="chevron"></span></li>
					<li class="active"><a href="<?php echo $this->createUrl('campaigns/template', array('campaign_uid' => $campaign->campaign_uid));?>"><?php echo Yii::t('campaigns', 'Template');?></a><span class="chevron"></span></li>
					<li><a href="<?php echo $this->createUrl('campaigns/confirm', array('campaign_uid' => $campaign->campaign_uid));?>"><?php echo Yii::t('campaigns', 'Confirmation');?></a><span class="chevron"></span></li>
					<li><a href="javascript:;"><?php echo Yii::t('app', 'Done');?></a><span class="chevron"></span></li>
				</ul>
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