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
			<span class="glyphicon glyphicon-usd"></span>
			<span class="caption-subject font-dark sbold uppercase">
				 <?php echo Yii::t('taxes', 'Taxes');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('taxes/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('taxes/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
				'id'                => $tax->modelName.'-grid',
				'dataProvider'      => $tax->search(),
				'filter'            => $tax,
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
						'name'  => 'name',
						'value' => '$data->name',
						'filter'=>	CHtml::activeTextField($tax, 'name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'percent',
						'value' => '$data->formattedPercent',
						'filter'=>	CHtml::activeTextField($tax, 'percent', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'country_id',
						'value' => '!empty($data->country_id) ? $data->country->name : "---"',
						'filter'=>	CHtml::activeTextField($tax, 'country_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'zone_id',
						'value' => '!empty($data->zone_id) ? $data->zone->name : "---"',
						'filter'=>	CHtml::activeTextField($tax, 'zone_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'is_global',
						'value' => 'ucfirst(Yii::t("app", $data->is_global))',
						//'filter'=> $tax->getYesNoOptions(),						
						'filter'=> CHtml::activeDropDownList($tax, 'is_global', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), tax::getYesNoOptions()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'status',
						'value' => '$data->statusName',
						//'filter'=> $tax->getStatusesList(),						
						'filter'=> CHtml::activeDropDownList($tax, 'status',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $tax->getStatusesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'date_added',
						'value' => '$data->dateAdded',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $tax->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'     => ' &nbsp; <i class="fa fa-pencil"></i> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("taxes/update", array("id" => $data->tax_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("taxes/update")',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("taxes/delete", array("id" => $data->tax_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("taxes/delete")',
							),    
						),
						'htmlOptions' => array(
							'style' => 'width:70px;',
						),
						'template' => '{update} {delete}'
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