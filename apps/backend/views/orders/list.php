<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
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
			<span class="glyphicon glyphicon-credit-card"></span>
			<span class="caption-subject font-dark sbold uppercase">
				 <?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('orders/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('orders/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $order->modelName.'-grid',
				'dataProvider'      => $order->search(),
				'filter'            => $order,
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
						'name'  => 'order_uid',
						'value' => 'CHtml::link($data->uid, Yii::app()->createUrl("orders/update", array("id" => $data->order_id)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($order, 'order_uid', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'customer_id',
						'value' => 'CHtml::link($data->customer->getFullName(), Yii::app()->createUrl("customers/update", array("id" => $data->customer_id)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($order, 'customer_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'plan_id',
						'value' => 'CHtml::link($data->plan->name, Yii::app()->createUrl("price_plans/update", array("id" => $data->plan_id)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($order, 'plan_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'promo_code_id',
						'value' => '!empty($data->promo_code_id) ? CHtml::link($data->promoCode->code, Yii::app()->createUrl("promo_codes/update", array("id" => $data->promo_code_id))) : "-"',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($order, 'promo_code_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'subtotal',
						'value' => '$data->formattedSubtotal',
						'filter'=>	CHtml::activeTextField($order, 'subtotal', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'tax_id',
						'value' => '!empty($data->tax_id) ? $data->tax->name : "---"',
						'filter'=>	CHtml::activeTextField($order, 'tax_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'tax_percent',
						'value' => '$data->formattedTaxPercent',
						'filter'=>	CHtml::activeTextField($order, 'tax_percent', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'tax_value',
						'value' => '$data->formattedTaxValue',
						'filter'=>	CHtml::activeTextField($order, 'tax_value', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'discount',
						'value' => '$data->formattedDiscount',
						'filter'=>	CHtml::activeTextField($order, 'discount', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'total',
						'value' => '$data->formattedTotal',
						'filter'=>	CHtml::activeTextField($order, 'total', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'status',
						'value' => '$data->getStatusName()',
						//'filter'=> $order->getStatusesList(),
						'filter'=> CHtml::activeDropDownList($order, 'status',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $order->getStatusesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'date_added',
						'value' => '$data->dateAdded',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $order->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'view' => array(
								'label'     => ' &nbsp; <i class="fa fa-eye"></i> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("orders/view", array("id" => $data->order_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'View'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("orders/view")',
							),
							'update' => array(
								'label'     => ' &nbsp; <i class="fa fa-pencil"></i> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("orders/update", array("id" => $data->order_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("orders/update")',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("orders/delete", array("id" => $data->order_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("orders/delete")',
							),    
						),
						'htmlOptions' => array(
							'style' => 'width:100px;',
						),
						'template' => '{view} {update} {delete}'
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