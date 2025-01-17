<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.2
 */

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false
 * in order to stop rendering the default content.
 * @since 1.3.4.3
 */

$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));
// and render if allowed
if ($viewCollection->renderContent) { ?>
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-folder-open"></span>
			<span class="caption-subject font-dark sbold uppercase">
				&nbsp;<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('customer_login_logs/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
			 * @since 1.3.4.3
			 */
			$hooks->doAction('before_grid_view', $collection = new CAttributeCollection(array(
				'controller'    => $this,
				'renderGrid'    => true,
			)));

			// and render if allowed
			if ($collection->renderGrid) {
				$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
					'ajaxUrl'           => $this->createUrl($this->route),
					'id'                => $model->modelName.'-grid',
					'dataProvider'      => $model->search(),
					'filter'            => $model,
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
							'value' => 'CHtml::link($data->customer->fullName, Yii::app()->createUrl("customers/update", array("id" => $data->customer_id)))',
							'type'  => 'raw',
							'filter'=>	CHtml::activeTextField($model, 'customer_id', array('class'=>'form-control form-filter input-sm')),
						),
						array(
							'name'  => 'ip_address',
							'value' => '$data->ip_address',
							'filter'=>	CHtml::activeTextField($model, 'ip_address', array('class'=>'form-control form-filter input-sm')),
						),
						array(
							'name'  => 'countryName',
							'value' => '$data->countryName',
							'filter'=> false,
						),
						array(
							'name'  => 'zoneName',
							'value' => '$data->zoneName',
							'filter'=> false,
						),
						array(
							'name'  => 'cityName',
							'value' => '$data->cityName',
							'filter'=> false,
						),
						array(
							'name'  => 'user_agent',
							'value' => 'StringHelper::truncateLength($data->user_agent, 100)',
							'filter'=> false,
						),
						array(
							'name'  => 'date_added',
							'value' => '$data->dateAdded',
							'filter'=> false,
						),
						array(
							'class'     => 'CButtonColumn',
							'header'    => Yii::t('app', 'Options'),
							'footer'    => $model->paginationOptions->getGridFooterPagination(),
							'buttons'   => array(
								'delete' => array(
									'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ',
									'url'       => 'Yii::app()->createUrl("customer_login_logs/delete", array("id" => $data->log_id))',
									'imageUrl'  => null,
									'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
									'visible'   => 'AccessHelper::hasRouteAccess("customer_login_logs/delete")',
								),
							),
							'htmlOptions' => array(
								'style' => 'width:50px;',
							),
							'template' => '{delete}'
						),
					), $this),
				), $this));
			}
			/**
			 * This hook gives a chance to append content after the grid view content.
			 * Please note that from inside the action callback you can access all the controller view
			 * variables via {@CAttributeCollection $collection->controller->data}
			 * @since 1.3.4.3
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
 * @since 1.3.4.3
 */
$hooks->doAction('after_view_file_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));
