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
			<span class="glyphicon glyphicon-list-alt"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php //echo CHtml::link(Yii::t('app', 'Create new'), array('link/create'), array('class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('socialsetting/connect'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
				'id'                => $link->modelName.'-grid',
				'dataProvider'      => $link->search(),
				'filter'            => $link,
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
						'name'  => 'socialpost_id',
						'value' => '$data->socialpost_id',
						'filter'=>	CHtml::activeTextField($link, 'socialpost_id', array('class'=>'form-control form-filter input-sm')),
					), 
					
					array(
						'name'  => 'text',
						'value' => '$data->text',
						'filter'=>	CHtml::activeTextField($link, 'text', array('class'=>'form-control form-filter input-sm')),
					),
										
					/*
					array(
						'name'  => 'imagename',
						'value' => '$data->imagename',

					),
					*/
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $link->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							
							'twitter' => array(
								'label'     => ' &nbsp; <span class=""><b>T</b></span> &nbsp;',
								'url'       => 'Yii::app()->createUrl("socialsetting/twitterstatus", array("id" => $data->socialpost_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Overview'), 'class' => ''),
								//'visible'   => '!$data->socialpost_id',
							),
							'linkedin' => array(
								'label'     => ' &nbsp; <span class=""><b>L</b></span> &nbsp;',
								'url'       => 'Yii::app()->createUrl("socialsetting/linkedinstatus", array("id" => $data->socialpost_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Overview'), 'class' => ''),
								//'visible'   => '!$data->socialpost_id',
							),
							
							'confirm_delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ',
								'url'       => 'Yii::app()->createUrl("socialsetting/delete", array("id" => $data->socialpost_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("socialsetting/delete")',
							),
							
						),
						'htmlOptions' => array(
							'style' => 'width:130px;',
						),
						'template'=>'{twitter} {linkedin} {confirm_delete}'
						
						
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
