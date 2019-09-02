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
if ($viewCollection->renderContent) { 
    /**
     * This hook gives a chance to prepend content before the active form or to replace the default active form entirely.
     * Please note that from inside the action callback you can access all the controller view variables 
     * via {@CAttributeCollection $collection->controller->data}
     * In case the form is replaced, make sure to set {@CAttributeCollection $collection->renderForm} to false 
     * in order to stop rendering the default content.
     * @since 1.3.3.1
     */
    $hooks->doAction('before_active_form', $collection = new CAttributeCollection(array(
        'controller'    => $this,
        'renderForm'    => true,
    )));
    
    // and render if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm'); ?>
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-envelope"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('dashboard/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
				</div>
			</div>
		</div>
		<div class="portlet-body">
			<?php 
			/**
			 * This hook gives a chance to prepend content before the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables 
			 * via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.3.1
			 */
			$hooks->doAction('before_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form    
			)));
			?>
			
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($campaign, 'name', array('class' => 'control-label'));?>
					<?php echo $form->textField($campaign, 'name', $campaign->getHtmlOptions('name')); ?>
					<?php echo $form->error($campaign, 'name');?>
				</div>
				<div class="form-group col-lg-3">
					<?php echo $form->labelEx($campaign, 'type', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($campaign, 'type', $campaign->getTypesList(), $campaign->getHtmlOptions('type')); ?>
					<?php echo $form->error($campaign, 'type');?>
				</div>
				<!--<div class="form-group col-lg-3">
					<?php //echo $form->labelEx($campaign, 'group_id', array('class' => 'control-label'));?>
					<?php //echo $form->dropDownList($campaign, 'group_id', $groupsArray, $campaign->getHtmlOptions('group_id')); ?>
					<?php //echo $form->error($campaign, 'group_id');?>
				</div>-->
			</div>
			<div class="row">
				<div class="form-group col-lg-6">
					<?php echo $form->labelEx($campaign, 'list_id', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($campaign, 'list_id', $listsArray, $campaign->getHtmlOptions('list_id')); ?>
					<?php echo $form->error($campaign, 'list_id');?>
				</div>
				<?php if (!empty($canSegmentLists)) { ?>
				<div class="form-group col-lg-6">
					<?php //echo $form->labelEx($campaign, 'segment_id', array('class' => 'control-label'));?>
					<?php //echo $form->dropDownList($campaign, 'segment_id', $segmentsArray, $campaign->getHtmlOptions('segment_id', array('disabled' => empty($campaign->segment_id) && empty($campaign->list_id), 'data-url' => $this->createUrl('campaigns/list_segments')))); ?>
					<?php echo $form->error($campaign, 'segment_id');?>
				</div>
				<?php } ?>
			</div>
			
			<?php if ($multiListsAllowed) { ?>
				
			<!--<div class="portlet-title">
				<div class="caption">	
					<span class="caption-subject font-dark sbold uppercase">
						<?php //echo Yii::t('campaigns', 'Campaign extra recipients');?>
					</span>
				</div>
				<div class="actions">
					<div class="btn-group btn-group-devided">
						<a href="javascript:;" class="btn green btn-add-extra-recipients"><?php //echo Yii::t('campaigns', 'Add new list and/or segment');?></a>
					</div>
				</div>
			</div>-->
			<!--<div id="extra-list-segment-container" class="row">
				<?php //if (!empty($temporarySources)) { foreach ($temporarySources as $index => $source) { ?>
				<div class="form-group col-lg-6">
					<div class="row">
						<div class="col-lg-5 col-list">
							<label class="required control-label"><?php //echo Yii::t('campaigns', 'List');?> <span class="required">*</span></label>							
							<?php //echo CHtml::dropDownList($source->modelName . '['.$index.'][list_id]', $source->list_id, CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $campaign->getListsDropDownArray()), $source->getHtmlOptions('list'));?>
						</div>
						<?php //if (!empty($canSegmentLists)) { ?>
						<div class="col-lg-5 col-segment">
							<label class="required control-label"><?php //echo Yii::t('campaigns', 'Segment');?> <span class="required">*</span></label>
							<?php //echo CHtml::dropDownList($source->modelName . '['.$index.'][segment_id]', $source->segment_id, CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $campaign->getSegmentsDropDownArray()), $source->getHtmlOptions('segment_id', array('data-url' => $this->createUrl('campaigns/list_segments'))));?>
						</div>
						<?php //} ?>
						<div class="col-lg-2">
							<label class="control-label">&nbsp;</label>
							<a href="javascript:;" class="btn red-mint remove-extra-recipients"><?php //echo Yii::t('app', 'Remove');?></a>
						</div>
					</div>
				</div>
				<?php //}} ?>
			</div>
			<div class="clearfix"></div>-->
			<?php } ?>
			<?php 
			/**
			 * This hook gives a chance to append content after the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables 
			 * via {@CAttributeCollection $collection->controller->data}
			 * 
			 * @since 1.3.3.1
			 */
			$hooks->doAction('after_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'campaign'      => $campaign,
				'form'          => $form    
			)));
			?>
			<div class="row">
				<div class="col-md-12">
					<div class="wizard">
						<?php if ($campaign->isNewRecord) { ?>
						<ul class="steps">
							<li class="active"><?php echo Yii::t('campaigns', 'Details');?><span class="chevron"></span></li>
							<li><?php echo Yii::t('campaigns', 'Setup');?><span class="chevron"></span></li>
							<li><?php echo Yii::t('campaigns', 'Template');?><span class="chevron"></span></li>
							<li><?php echo Yii::t('campaigns', 'Confirmation');?><span class="chevron"></span></li>
						</ul>
						<?php } else { ?>
						<ul class="steps">
							<li class="active"><a href="<?php echo $this->createUrl('campaigns/update', array('campaign_uid' => $campaign->campaign_uid));?>"><?php echo Yii::t('campaigns', 'Details');?></a><span class="chevron"></span></li>
							<li><a href="<?php echo $this->createUrl('campaigns/setup', array('campaign_uid' => $campaign->campaign_uid));?>"><?php echo Yii::t('campaigns', 'Setup');?></a><span class="chevron"></span></li>
							<li><a href="<?php echo $this->createUrl('campaigns/template', array('campaign_uid' => $campaign->campaign_uid));?>"><?php echo Yii::t('campaigns', 'Template');?></a><span class="chevron"></span></li>
							<li><a href="<?php echo $this->createUrl('campaigns/confirm', array('campaign_uid' => $campaign->campaign_uid));?>"><?php echo Yii::t('campaigns', 'Confirmation');?></a><span class="chevron"></span></li>
							<li><a href="javascript:;"><?php echo Yii::t('app', 'Done');?></a><span class="chevron"></span></li>
						</ul>
						<?php } ?>
						<div class="actions">
							<button type="submit" id="is_next" name="is_next" value="1" class="btn green btn-submit btn-go-next" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('campaigns', 'Save and next');?></button>
						</div>
					</div>
				</div>
			</div>
		</div>
        <?php 
        $this->endWidget(); 
    } 
    /**
     * This hook gives a chance to append content after the active form.
     * Please note that from inside the action callback you can access all the controller view variables 
     * via {@CAttributeCollection $collection->controller->data}
     * @since 1.3.3.1
     */
    $hooks->doAction('after_active_form', new CAttributeCollection(array(
        'controller'      => $this,
        'renderedForm'    => $collection->renderForm,
    )));

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
?>
<?php if ($multiListsAllowed) { ?>
<div id="extra-recipients-template" style="display: none;" data-count="<?php echo !empty($temporarySources) ? count($temporarySources) : 0;?>">
    <div class="form-group col-lg-6">
		<div class="row">
			<div class="col-lg-5 col-list">
				<label class="required control-label"><?php echo Yii::t('campaigns', 'List');?> <span class="required">*</span></label>
				<?php echo CHtml::dropDownList($campaignTempSource->modelName . '[__#__][list_id]', null, $listsArray, $campaign->getHtmlOptions('list_id', array('disabled' => true)));?>
			</div>
			<?php if (!empty($canSegmentLists)) { ?>
			<div class="col-lg-5 col-segment">
				<label class="required control-label"><?php echo Yii::t('campaigns', 'Segment');?> </label>
				<?php echo CHtml::dropDownList($campaignTempSource->modelName . '[__#__][segment_id]', null, $segmentsArray, $campaign->getHtmlOptions('segment_id', array('disabled' => true, 'data-url' => $this->createUrl('campaigns/list_segments'))));?>
			</div>
			<?php } ?>
			<div class="col-lg-2">
				<label class="control-label">&nbsp;</label>				
				<a href="javascript:;" class="btn red-mint remove-extra-recipients"><?php echo Yii::t('app', 'Remove');?></a>
			</div>
		</div>
    </div>
</div>
<?php } ?>