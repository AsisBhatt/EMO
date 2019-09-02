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
			<span class="glyphicon glyphicon-credit-card"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('plan', 'Plans');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('plan/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('plan/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
				'id'                => $plan->modelName.'-grid',
				'dataProvider'      => $plan->search(),
				'filter'            => $plan,
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
						'filter'=>	CHtml::activeTextField($plan, 'name', array('class'=>'form-control form-filter input-sm')),
					),array(
						'name'  => 'sms_total',
						'value' => '$data->sms_total',
						'filter'=>	CHtml::activeTextField($plan, 'sms_total', array('class'=>'form-control form-filter input-sm')),
					)
					,array(
						'name'  => 'email_total',
						'value' => '$data->email_total',
						'filter'=>	CHtml::activeTextField($plan, 'email_total', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'validity',
						'value' => '$data->validity',
						'filter'=>	CHtml::activeTextField($plan, 'validity', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name' 	=> 'list_limit',
						'value'	=> '$data->list_limit',
						'filter'=>	CHtml::activeTextField($plan, 'list_limit', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'	=> 'listsend_limit',
						'value'	=> '$data->listsend_limit',
						'filter' => CHtml::activeTextField($plan, 'listsend_limit', array('class' => 'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'price',
						'value' => '$data->formattedPrice',
						'filter'=>	CHtml::activeTextField($plan, 'price', array('class'=>'form-control form-filter input-sm')),
					),
				   
					array(
						'name'  => 'status',
						'value' => '$data->statusName',
						// 'filter'=> $plan->getStatusesList(),
						'filter'=> CHtml::activeDropDownList($plan, 'status',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), plan::getStatusesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'date_added',
						'value' => '$data->dateAdded',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $plan->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'     => ' &nbsp; <i class="fa fa-pencil"></i> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("plan/update", array("id" => $data->plan_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("plan/update")',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("plan/delete", array("id" => $data->plan_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("plan/delete")',
							),    
						),
						// 'htmlOptions' => array(
							// 'style' => 'width:70px;',
						// ),
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