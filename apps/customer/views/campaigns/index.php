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
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-envelope"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('app', 'Create new'), array('campaigns/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'New')));?>
				<?php echo CHtml::link(Yii::t('campaigns', 'Manage groups'), array('campaign_groups/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('campaigns', 'Manage groups')));?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('campaigns/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
			</div>
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-scrollable">
		<?php
		/**
		 * This hook gives a chance to prepend content or to replace the default grid view content with a custom content.
		 * Please note that from inside the action callback you can access all the controller view
		 * variables via {@CAttributeCollection $collection->controller->data}
		 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderGrid} to false
		 * in order to stop rendering the default content.
		 * @since 1.3.3.1
		 */
		$hooks->doAction('before_grid_view', $collection = new CAttributeCollection(array(
			'controller'    => $this,
			'renderGrid'    => true,
		)));

		// and render if allowed
		if ($collection->renderGrid) {
			// since 1.3.5.6
			$this->widget('common.components.web.widgets.GridViewBulkAction', array(
				'model'      => $campaign,
				'formAction' => $this->createUrl('campaigns/bulk_action'),
			));

			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $campaign->modelName.'-grid',
				'dataProvider'      => $campaign->search(),
				'filter'            => $campaign,
				'filterPosition'    => 'body',
				'filterCssClass'    => 'grid-filter-cell',
				'itemsCssClass'     => 'table table-bordered table-hover',
				'selectableRows'    => 0,
				'enableSorting'     => false,
				'cssFile'           => false,
				'pagerCssClass'     => 'pagination pull-right',
				'pager'             => array(
					'class'         => 'CLinkPager',
					'cssFile'       => false,
					'header'        => false,
					'htmlOptions'   => array('class' => 'pagination')
				),
				'columns' => $hooks->applyFilters('grid_view_columns', array(
					array(
						'class'               => 'CCheckBoxColumn',
						'name'                => 'campaign_uid',
						'selectableRows'      => 100,
						'checkBoxHtmlOptions' => array('name' => 'bulk_item[]'),
					),
					array(
						'name'  => 'campaign_uid',
						'value' => 'CHtml::link($data->campaign_uid, Yii::app()->createUrl("campaigns/overview", array("campaign_uid" => $data->campaign_uid)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($campaign, 'campaign_uid', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'name',
						'value' => 'CHtml::link($data->name, Yii::app()->createUrl("campaigns/overview", array("campaign_uid" => $data->campaign_uid)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($campaign, 'name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'type',
						'value' => 'ucfirst(strtolower($data->getTypeNameDetails()))',
						'type'  => 'raw',
						//'htmlOptions' => array('style' => 'max-width: 150px')
						'filter'=> CHtml::activeDropDownList($campaign, 'type',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $campaign->getTypesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'group_id',
						'value' => '!empty($data->group_id) ? CHtml::link($data->group->name, Yii::app()->createUrl("campaign_groups/update", array("group_uid" => $data->group->uid))) : "-"',
						'type'  => 'raw',
						'filter'=> CHtml::activeDropDownList($campaign, 'group_id',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $campaign->getGroupsDropDownArray()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'list_id',
						'value' => 'CHtml::link($data->list->name, Yii::app()->createUrl("lists/overview", array("list_uid" => $data->list->uid)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($campaign, 'list_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'segment_id',
						'value' => '!empty($data->segment_id) ? CHtml::link($data->segment->name, Yii::app()->createUrl("list_segments/update", array("list_uid" => $data->list->uid, "segment_uid" => $data->segment->uid))) : "-"',
						'filter'=> false,
						'type'  => 'raw',
					),
					array(
						'name'        => 'search_recurring',
						'value'       => 'Yii::t("app", $data->option->cronjob_enabled ? "Yes" : "No")',
						'htmlOptions' => array('style' => 'max-width: 150px'),
						'filter'=> CHtml::activeDropDownList($campaign, 'search_recurring',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $campaign->getYesNoOptions()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'status',
						'value' => '$data->getStatusWithStats()',
						'filter'=> CHtml::activeDropDownList($campaign, 'status',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $campaign->getStatusesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'date_added',
						'value' => '$data->dateAdded',
						'filter'=> false,
					),
					array(
						'name'  => 'send_at',
						'value' => '$data->getSendAt()',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $campaign->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'overview'=> array(
								'label'     => ' &nbsp; <i class="fa fa-info-circle"></i> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/overview", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('campaigns', 'Overview'), 'class' => ''),
								'visible'   => '(!$data->editable || $data->isPaused) && !$data->pendingDelete',
							),
							'pause'=> array(
								'label'     => ' &nbsp; <i class="fa fa-pause-circle-o"></i> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/pause_unpause", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('campaigns', 'Pause sending'), 'class' => 'pause-sending', 'data-message' => Yii::t('campaigns', 'Are you sure you want to pause this campaign ?')),
								'visible'   => '$data->canBePaused',
							),
							'unpause'=> array(
								'label'     => ' &nbsp; <i class="fa fa-play-circle-o"></i> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/pause_unpause", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('campaigns', 'Unpause sending'), 'class' => 'unpause-sending', 'data-message' => Yii::t('campaigns', 'Are you sure you want to unpause sending emails for this campaign ?')),
								'visible'   => '$data->isPaused',
							),
							'reset'=> array(
								'label'     => ' &nbsp; <i class="fa fa-refresh"></i> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/resume_sending", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('campaigns', 'Resume sending'), 'class' => 'resume-campaign-sending', 'data-message' => Yii::t('campaigns', 'Resume sending, use this option if you are 100% sure your campaign is stuck and does not send emails anymore!')),
								'visible'   => '$data->canBeResumed',
							),
							'copy'=> array(
								'label'     => ' &nbsp; <span class="glyphicon glyphicon-subtitles"></span> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/copy", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'visible'   => '!$data->pendingDelete',
								'options'   => array('title' => Yii::t('app', 'Copy'), 'class' => 'copy-campaign'),
							),
							'update'=> array(
								'label'     => ' &nbsp; <span class="fa fa-pencil"></span> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/update", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'visible'   => '$data->editable',
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
							),
							'marksent'=> array(
								'label'     => ' &nbsp; <span class="fa fa-check"></span> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/marksent", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('campaigns', 'Mark as sent'), 'class' => 'mark-campaign-as-sent', 'data-message' => Yii::t('campaigns', 'Are you sure you want to mark this campaign as sent ?')),
								'visible'   => '$data->canBeMarkedAsSent',
							),
							'delete' => array(
								'label'     => ' &nbsp; <span class="fa fa-trash-o"></span> &nbsp; ',
								'url'       => 'Yii::app()->createUrl("campaigns/delete", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'visible'   => '$data->removable',
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
							),
						),
						// 'htmlOptions' => array(
							// 'style' => 'width: 180px;'
						// ),
						'template'=>'{overview} {pause} {unpause} {reset} {copy} {update} {marksent} {delete}'
					),
				), $this),
			), $this));
		}
		/**
		 * This hook gives a chance to append content after the grid view content.
		 * Please note that from inside the action callback you can access all the controller view
		 * variables via {@CAttributeCollection $collection->controller->data}
		 * @since 1.3.3.1
		 */
		$hooks->doAction('after_grid_view', new CAttributeCollection(array(
			'controller'    => $this,
			'renderedGrid'  => $collection->renderGrid,
		)));
		?>
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
