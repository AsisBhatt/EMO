<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since @since 1.3.5.5
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
			if (AccessHelper::hasRouteAccess('campaigns/bulk_action')) {
				$this->widget('common.components.web.widgets.GridViewBulkAction', array(
					'model'      => $campaign,
					'formAction' => $this->createUrl('campaigns/bulk_action'),
				));
			}
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
						'name'  => 'customer_id',
						'value' => 'CHtml::link($data->customer->fullName, Yii::app()->createUrl("customers/update", array("id" => $data->customer_id)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($campaign, 'customer_id', array('class'=>'form-control form-filter input-sm')),
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
						//'filter'=> $campaign->getTypesList(),
						'filter'=> CHtml::activeDropDownList($campaign, 'type',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $campaign->getTypesList()), array('class'=>'form-control form-filter input-sm')),
						//'htmlOptions' => array('style' => 'max-width: 150px')
					),
					array(
						'name'  => 'group_id',
						'value' => '!empty($data->group_id) ? $data->group->name : "-"',
						//'filter'=> $campaign->getGroupsDropDownArray(),
						'type'  => 'raw',
						'filter'=> CHtml::activeDropDownList($campaign, 'group_id',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $campaign->getGroupsDropDownArray()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'list_id',
						'value' => '$data->list->name',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($campaign, 'list_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'segment_id',
						'value' => '!empty($data->segment_id) ? $data->segment->name : "-"',
						'filter'=> false,
						'type'  => 'raw',
					),
					array(
						'name'        => 'search_recurring',
						'value'       => 'Yii::t("app", $data->option->cronjob_enabled ? "Yes" : "No")',
						//'filter'      => $campaign->getYesNoOptions(),
						//'htmlOptions' => array('style' => 'max-width: 150px'),
						'filter'=> CHtml::activeDropDownList($campaign, 'search_recurring',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $campaign->getYesNoOptions()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'status',
						'value' => '$data->getStatusWithStats()',
						//'filter'=> $campaign->getStatusesList(),
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
								'visible'   => '(!$data->editable || $data->isPaused) && !$data->pendingDelete && !$data->isDraft',
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
							'block'=> array(
								'label'     => ' &nbsp; <i class="fa fa-power-off"></i> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/block_unblock", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('campaigns', 'Block sending'), 'class' => 'block-sending', 'data-message' => Yii::t('campaigns', 'Are you sure you want to block this campaign ?')),
								'visible'   => '$data->canBeBlocked',
							),
							'unblock'=> array(
								'label'     => ' &nbsp; <i class="fa fa-play"></i> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/block_unblock", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('campaigns', 'Unblock sending'), 'class' => 'unblock-sending', 'data-message' => Yii::t('campaigns', 'Are you sure you want to unblock sending emails for this campaign ?')),
								'visible'   => '$data->isBlocked',
							),
							'reset'=> array(
								'label'     => ' &nbsp; <i class="fa fa-refresh"></i> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/resume_sending", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('campaigns', 'Resume sending'), 'class' => 'resume-campaign-sending', 'data-message' => Yii::t('campaigns', 'Resume sending, use this option if you are 100% sure your campaign is stuck and does not send emails anymore!')),
								'visible'   => '$data->canBeResumed',
							),
							'marksent'=> array(
								'label'     => ' &nbsp; <i class="fa fa-check-circle-o"></i> &nbsp;',
								'url'       => 'Yii::app()->createUrl("campaigns/marksent", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('campaigns', 'Mark as sent'), 'class' => 'mark-campaign-as-sent', 'data-message' => Yii::t('campaigns', 'Are you sure you want to mark this campaign as sent ?')),
								'visible'   => '$data->canBeMarkedAsSent',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ',
								'url'       => 'Yii::app()->createUrl("campaigns/delete", array("campaign_uid" => $data->campaign_uid))',
								'imageUrl'  => null,
								'visible'   => '$data->removable',
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
							),
						),
						// 'htmlOptions' => array(
							// 'style' => 'width: 140px;'
						// ),
						'template'=>'{overview} {pause} {unpause} {reset} {marksent} {block} {unblock} {delete}'
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
		<div class="clearfix"><!-- --></div>
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
