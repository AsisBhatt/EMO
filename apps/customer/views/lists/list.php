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
				<?php 
					echo CHtml::link(Yii::t('app', 'Create new'), array('lists/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));
				?>
				<?php echo CHtml::link(Yii::t('app', 'All subscribers'), array('lists/all_subscribers'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'All subscribers')));?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('lists/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
				'id'                => $list->modelName.'-grid',
				'dataProvider'      => $list->search(),
				'filter'            => $list,
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
						'name'  => 'list_uid',
						'value' => 'CHtml::link($data->list_uid,Yii::app()->createUrl("lists/overview", array("list_uid" => $data->list_uid)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($list, 'list_uid', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'name',
						'value' => 'CHtml::link($data->name,Yii::app()->createUrl("lists/overview", array("list_uid" => $data->list_uid)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($list, 'name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'display_name',
						'value' => 'CHtml::link($data->display_name,Yii::app()->createUrl("lists/overview", array("list_uid" => $data->list_uid)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($list, 'display_name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'subscribers_count',
						'value' => 'Yii::app()->format->formatNumber($data->subscribersCount)',
						'filter'=> false,
					),
					array(
						'name'  => 'opt_in',
						'value' => 'Yii::t("lists", ucfirst($data->opt_in))',
						'filter'=> CHtml::activeDropDownList($list, 'opt_in',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $list->getOptInArray()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'opt_out',
						'value' => 'Yii::t("lists", ucfirst($data->opt_out))',
						'filter'=> CHtml::activeDropDownList($list, 'opt_out',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $list->getOptOutArray()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'merged',
						'value' => 'Yii::t("lists", ucfirst($data->merged))',
						'filter'=> CHtml::activeDropDownList($list, 'merged',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $list->getYesNoOptions()), array('class'=>'form-control form-filter input-sm')),
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
						'footer'    => $list->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'overview' => array(
								'label'     => ' <span class="fa fa-info-circle"></span> &nbsp;',
								'url'       => 'Yii::app()->createUrl("lists/overview", array("list_uid" => $data->list_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('lists', 'Overview'), 'class' => ''),
								'visible'   => '!$data->pendingDelete',
								'visible'   => '($data->customer_id != 0 ? true : false)',
							),
							'copy'=> array(
								'label'     => ' &nbsp; <span class="glyphicon glyphicon-subtitles"></span> &nbsp;',
								'url'       => 'Yii::app()->createUrl("lists/copy", array("list_uid" => $data->list_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Copy'), 'class' => 'copy-list'),
								'visible'   => '!$data->pendingDelete',
								'visible'   => '($data->customer_id != 0 ? true : false)',
							),
							'update' => array(
								'label'     => ' &nbsp; <span class="fa fa-pencil"></span> &nbsp;',
								'url'       => 'Yii::app()->createUrl("lists/update", array("list_uid" => $data->list_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
								'visible'   => '$data->editable',
								'visible'   => '($data->customer_id != 0 ? true : false)',
							),
							'confirm_delete' => array(
								'label'     => ' &nbsp; <span class="fa fa-trash-o"></span> &nbsp; ',
								'url'       => 'Yii::app()->createUrl("lists/delete", array("list_uid" => $data->list_uid))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => ''),
								'visible'   => '$data->isRemovable',
								'visible'   => '($data->customer_id != 0 ? true : false)',
							),
						),
						'htmlOptions' => array(
							'style' => 'width:130px;',
						),
						'template'=>'{overview} {copy} {update} {confirm_delete}'
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
