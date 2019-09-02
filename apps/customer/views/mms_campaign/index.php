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
				<?php echo CHtml::link(Yii::t('app', 'Create new'), array('mms_campaign/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('mms_campaign/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
			/*$this->widget('common.components.web.widgets.GridViewBulkAction', array(
				'model'      => $smscampaign,
				//'formAction' => $this->createUrl('campaigns/bulk_action'),
			));*/

			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $smscampaign->modelName.'-grid',
				'dataProvider'      => $smscampaign->search(),
				'filter'            => $smscampaign,
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
						'name'  => 'campaign_name',
						'value' => '$data->campaign_name',
						'filter'=>	CHtml::activeTextField($smscampaign, 'campaign_name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'campaign_text',
						'value' => '$data->campaign_text',
						'filter'=>	CHtml::activeTextField($smscampaign, 'campaign_text', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'list_id',
						'value' => '$data->list->name',
						'filter'=> false,
					),
					
					array(
						'name' => 'send_at',
						'value' => '$data->send_at',
						'filter'=>	CHtml::activeTextField($smscampaign, 'send_at', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name' => 'sent_record',
						'value' => '$data->sent_record',
						'filter'=>	CHtml::activeTextField($smscampaign, 'sent_record', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name' => 'not_sent_record',
						'value' => '$data->not_sent_record',
						'filter'=>	CHtml::activeTextField($smscampaign, 'not_sent_record', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name' => 'campaign_status',
						'value' => '$data->campaign_status',
						'filter'=>	CHtml::activeTextField($smscampaign, 'campaign_status', array('class'=>'form-control form-filter input-sm')),
					),
					
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $smscampaign->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							 'update' => array(
								'label'    => ' &nbsp; <span class="fa fa-pencil"></span> &nbsp;',
								'url'      => 'Yii::app()->createUrl("sms_campaign/update", array("id" => $data->sms_campaign_id))',
								'imageUrl' => null,
								'options'  => array('title' => Yii::t('lists', 'View'), 'class' => ''),
							),
							'delete' => array(
								'label'     => ' &nbsp; <span class="fa fa-trash-o"></span> &nbsp; ',
								'url'       => 'Yii::app()->createUrl("sms_campaign/delete", array("id" => $data->sms_campaign_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
							),
						),
						'htmlOptions' => array(
							'style' => 'width: 180px;',
						),
						//'template'=>' '
						'template'=>'{update} {delete}',
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
