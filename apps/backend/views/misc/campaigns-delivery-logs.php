<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.6
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
			<span class="glyphicon glyphicon-file"></span>
			<span class="caption-subject font-dark sbold uppercase">
				 <?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php 
				if (empty($archive)) {
					echo CHtml::link(Yii::t('misc', 'View archived logs'), array('misc/campaigns_delivery_logs', 'archive' => 1), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('misc', 'View archived logs')));
				} else {
					echo CHtml::link(Yii::t('misc', 'View current logs'), array('misc/campaigns_delivery_logs'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('misc', 'View current logs')));
				}
				?>
				<?php echo HtmlHelper::accessLink(Yii::t('misc', 'Delete delivery temporary errors'), array('misc/delete_delivery_temporary_errors'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Delete delivery temporary errors'), 'data-confirm' => Yii::t('misc', 'Are you sure you want to delete the delivery temporary errors? Please note that this will affect running campaigns, continue only if you really know what you are doing!')));?>
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
			'controller'  => $this,
			'renderGrid'  => true,
		)));
		
		// and render if allowed
		if ($collection->renderGrid) {
			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $log->modelName.'-grid',
				'dataProvider'      => $log->searchLight(),
				'filter'            => null,
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
						'name'  => 'customer_id',
						'value' => 'empty($data->campaign) ? "-" : CHtml::link($data->campaign->customer->getFullName(), array("customers/update", "id" => $data->campaign->customer->customer_id))',
						'type'  => 'raw',
					),
					array(
						'name'  => 'campaign_id',
						'value' => 'empty($data->campaign) ? "-" : $data->campaign->name',
					),
					array(
						'name'  => 'list_id',
						'value' => 'empty($data->campaign) ? "-" : $data->campaign->list->name',
					),
					array(
						'name'  => 'segment_id',
						'value' => '!empty($data->campaign) && !empty($data->campaign->segment_id) ? $data->campaign->segment->name : "-"',
					),
					array(
						'name'  => 'subscriber_id',
						'value' => 'empty($data->subscriber) ? "-" : $data->subscriber->email',
					),
					array(
						'name'  => 'message',
						'value' => '$data->message',
					),
					array(
						'name'  => 'delivery_confirmed',
						'value' => 'ucfirst(Yii::t("app", $data->delivery_confirmed))',
						'filter'=> $log->getYesNoOptions(),
					),
					array(
						'name'  => 'status',
						'value' => '$data->statusName',
						'filter'=> $log->getStatusesArray(),
					),
					array(
						'name'  => 'date_added',
						'value' => '$data->dateAdded',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $log->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(),
						'htmlOptions' => array(
							'style' => 'width:50px;',
						),
						'template' => ''
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
			'controller'  => $this,
			'renderedGrid'=> $collection->renderGrid,
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