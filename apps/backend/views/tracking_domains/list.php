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
    <div class="alert alert-success">
        <?php echo Yii::t('tracking_domains', 'Please note, in order for this feature to work this (sub)domain needs a dedicated IP address, otherwise all defined CNAMES for it will point to the default domain on this server.');?>
        <br />
        <strong><?php echo Yii::t('tracking_domains', 'If you do not use a dedicated IP address for this domain only or you are not sure you do so, do not use this feature!');?></strong>
    </div>
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-flash"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('tracking_domains', 'Tracking domains');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('tracking_domains/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('tracking_domains/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
				'id'                => $domain->modelName.'-grid',
				'dataProvider'      => $domain->search(),
				'filter'            => $domain,
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
						'value' => '!empty($data->customer) ? $data->customer->getFullName() : Yii::t("app", "System")',
						'filter'=> CHtml::activeTextField($domain, 'customer_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'name',
						'value' => 'CHtml::link($data->name, Yii::app()->createUrl("tracking_domains/update", array("id" => $data->domain_id)))',
						'type'  => 'raw',
						'filter'=> CHtml::activeTextField($domain, 'name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'date_added',
						'value' => '$data->dateAdded',
						'filter'=> false,
					),
					array(
						'name'  => 'last_updated',
						'value' => '$data->lastUpdated',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $domain->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'     => ' &nbsp; <span class="fa fa-pencil"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("tracking_domains/update", array("id" => $data->domain_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app','Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("tracking_domains/update")',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("tracking_domains/delete", array("id" => $data->domain_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app','Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("tracking_domains/delete")',
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