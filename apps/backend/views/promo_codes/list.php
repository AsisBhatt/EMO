<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.4
 */

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false 
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
$hooks->doAction('views_before_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) { ?>
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-code"></i>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('promo_codes/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('promo_codes/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
		$hooks->doAction('views_before_grid', $collection = new CAttributeCollection(array(
			'controller'   => $this,
			'renderGrid'   => true,
		)));
		
		// and render if allowed
		if ($collection->renderGrid) {
			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('views_grid_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $promoCode->modelName.'-grid',
				'dataProvider'      => $promoCode->search(),
				'filter'            => $promoCode,
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
				'beforeAjaxUpdate'  => 'js:function(id, options){
					window.dpStartSettings = $("#PricePlanPromoCode_date_start").data("datepicker").settings;
					window.dpEndSettings = $("#PricePlanPromoCode_date_end").data("datepicker").settings;
				}',
				'afterAjaxUpdate'   => 'js:function(id, data) {
					$("#PricePlanPromoCode_date_start").datepicker(window.dpStartSettings);
					$("#PricePlanPromoCode_date_end").datepicker(window.dpEndSettings);
					window.dpStartSettings = null;
					window.dpEndSettings = null;
				}',
				'columns' => $hooks->applyFilters('views_grid_columns', array(
					array(
						'name'  => 'code',
						'value' => '$data->code',
						'filter'=>	CHtml::activeTextField($promoCode, 'code', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'type',
						'value' => '$data->typeName',
						'filter'=> CHtml::activeDropDownList($promoCode, 'type',  array_merge(array('' => 'Choose'), $promoCode->getTypesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'discount',
						'value' => '$data->formattedDiscount',
						'filter' => CHtml::activeTextField($promoCode, 'discount', array('class'=>'form-control form-filter input-sm'))
					),
					array(
						'name'  => 'total_amount',
						'value' => '$data->formattedTotalAmount',
						'filter' => CHtml::activeTextField($promoCode, 'total_amount', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'total_usage',
						'value' => '$data->total_usage',
						'filter' => CHtml::activeTextField($promoCode, 'total_usage', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'customer_usage',
						'value' => '$data->customer_usage',
						'filter' => CHtml::activeTextField($promoCode, 'customer_usage', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'status',
						'value' => '$data->statusName',
						'filter'=> CHtml::activeDropDownList($promoCode, 'status',  array_merge(array('' => 'Choose'), $promoCode->getStatusesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'date_start',
						'value' => '$data->dateStart',
						'filter'=> '<div class="row"><div class="col-lg-12">' . 
							'<div class="col-lg-3" style="padding-right:0px; padding-left:0px"> ' . CHtml::activeDropDownList($promoCode, 'pickerDateStartComparisonSign', $promoCode->getComparisonSignsList(), array('class'=>'form-control form-filter input-sm')) . '</div>' .
							'<div class="col-lg-9" style="padding-right:0px;">' .
							$this->widget('zii.widgets.jui.CJuiDatePicker',array(
								'model'     => $promoCode,
								'attribute' => 'date_start',
								'cssFile'   => null,
								'language'  => $promoCode->getDatePickerLanguage(),
								'options'   => array(
									'showAnim'   => 'fold',
									'dateFormat' => $promoCode->getDatePickerFormat(),
								),
								'htmlOptions'=>array('class' => 'form-control form-filter input-sm'),
							), true) . 
							'</div>' .
							'</div>' .
							'</div>',
					),
					array(
						'name'  => 'date_end',
						'value' => '$data->dateEnd',
						'filter'=> '<div class="row"><div class="col-lg-12">' . 
							'<div class="col-lg-3" style="padding-right:0px; padding-left:0px"> ' . CHtml::activeDropDownList($promoCode, 'pickerDateEndComparisonSign', $promoCode->getComparisonSignsList(), array('class'=>'form-control form-filter input-sm')) . '</div>' .
							'<div class="col-lg-9" style="padding-right:0px;">' .
							$this->widget('zii.widgets.jui.CJuiDatePicker',array(
								'model'     => $promoCode,
								'attribute' => 'date_end',
								'cssFile'   => null,
								'language'  => $promoCode->getDatePickerLanguage(),
								'options'   => array(
									'showAnim'   => 'fold',
									'dateFormat' => $promoCode->getDatePickerFormat(),
								),
								'htmlOptions'=>array('class' => 'form-control form-filter input-sm'),
							), true) . 
							'</div>' .
							'</div>' .
							'</div>',
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $promoCode->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'     => ' &nbsp; <i class="fa fa-pencil"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("promo_codes/update", array("id" => $data->promo_code_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("promo_codes/update")',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("promo_codes/delete", array("id" => $data->promo_code_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("promo_codes/delete")',
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
		$hooks->doAction('views_after_grid', new CAttributeCollection(array(
			'controller'   => $this,
			'renderedGrid' => $collection->renderGrid,
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
$hooks->doAction('views_after_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));