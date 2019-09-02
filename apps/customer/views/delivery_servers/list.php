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
			<span class="glyphicon glyphicon-send"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<button type="button" class="btn green btn-circle btn-sm dropdown-toggle" data-toggle="dropdown"> <?php echo Yii::t('servers', 'Create new server');?> <span class="caret"></span> </button>
				<ul class="dropdown-menu" role="menu">
					<?php foreach (DeliveryServer::getCustomerTypesList() as $type => $name) { ?>
					<li><a href="<?php echo $this->createUrl('delivery_servers/create', array('type' => $type));?>"><?php echo $name;?></a></li>
					<?php } ?>
				</ul>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('delivery_servers/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
				'id'                => $server->modelName.'-grid',
				'dataProvider'      => $server->search(),
				'filter'            => $server,
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
						'value' => 'empty($data->name) ? null : CHtml::link($data->name, Yii::app()->createUrl("delivery_servers/update", array("type" => $data->type, "id" => $data->server_id)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($server, 'name', array('class'=>'form-control form-filter input-sm')),
					), 
					array(
						'name'  => 'hostname',
						'value' => 'CHtml::link($data->hostname, Yii::app()->createUrl("delivery_servers/update", array("type" => $data->type, "id" => $data->server_id)))',
						'type'  => 'raw',
						'filter'=>	CHtml::activeTextField($server, 'hostname', array('class'=>'form-control form-filter input-sm')),
					), 
					array(
						'name'  => 'username',
						'value' => '$data->username',
						'filter'=>	CHtml::activeTextField($server, 'username', array('class'=>'form-control form-filter input-sm')),
					), 
					array(
						'name'  => 'from_email',
						'value' => '$data->from_email',
						'filter'=>	CHtml::activeTextField($server, 'from_email', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'type',
						'value' => 'DeliveryServer::getNameByType($data->type)',
						'filter'=> CHtml::activeDropDownList($server, 'type', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $server->getTypesList()), array('class'=>'form-control form-filter input-sm')),
					), 
					array(
						'name'  => 'status',
						'value' => 'ucfirst(Yii::t("app", $data->status))',
						'filter'=> CHtml::activeDropDownList($server, 'status',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $server->getStatusesList()), array('class'=>'form-control form-filter input-sm')),
					), 
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $server->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'     => ' &nbsp; <span class="fa fa-pencil"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/update", array("type" => $data->type, "id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app','Update'), 'class' => ''),
								'visible'   => '$data->getCanBeUpdated()',
							),
							'copy'=> array(
								'label'     => ' &nbsp; <span class="glyphicon glyphicon-subtitles"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/copy", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Copy'), 'class' => 'copy-server'),
							),
							'enable'=> array(
								'label'     => ' &nbsp; <span class="fa fa-eye"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/enable", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Enable'), 'class' => 'enable-server'),
								'visible'   => '$data->getIsDisabled()',
							),
							'disable'=> array(
								'label'     => ' &nbsp; <span class="glyphicon glyphicon-save"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/disable", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Disable'), 'class' => 'disable-server'),
								'visible'   => '$data->getIsActive()',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/delete", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app','Delete'), 'class' => 'delete'),
								'visible'   => '$data->getCanBeDeleted()',
							),    
						),
						'htmlOptions' => array(
							'style' => 'width:120px;',
						),
						'template' => '{update} {copy} {enable} {disable} {delete}'
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
		<div class="clearfix"><!-- --></div>
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