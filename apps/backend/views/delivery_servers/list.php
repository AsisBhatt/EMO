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
			<span class="glyphicon glyphicon-send"></span>
			<span class="caption-subject font-dark sbold uppercase">
				 <?php echo Yii::t('servers', 'Delivery servers');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php if (AccessHelper::hasRouteAccess('delivery_servers/create')) { ?>
				<button type="button" class="btn green btn-circle btn-sm dropdown-toggle" data-toggle="dropdown"> <?php echo Yii::t('servers', 'Create new server');?> <span class="caret"></span> </button>
				<ul class="dropdown-menu" role="menu">
					<?php foreach (DeliveryServer::getTypesList() as $type => $name) { ?>
					<li><a href="<?php echo $this->createUrl('delivery_servers/create', array('type' => $type));?>"><?php echo $name;?></a></li>
					<?php } ?>
				</ul>
				<?php } ?>
				<?php if (AccessHelper::hasRouteAccess('delivery_servers/import')) { ?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Import'), '#csv-import-modal', array('data-toggle' => 'modal', 'class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Import')));?>
				<?php } ?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Export'), array('delivery_servers/export'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Export')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('delivery_servers/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
			// since 1.3.5.4
			if (AccessHelper::hasRouteAccess('delivery_servers/bulk_action')) { 
				$this->widget('common.components.web.widgets.GridViewBulkAction', array(
					'model'      => $server,
					'formAction' => $this->createUrl('delivery_servers/bulk_action'),
				));
			}
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
						'class'               => 'CCheckBoxColumn',
						'name'                => 'server_id',
						'selectableRows'      => 100,  
						'checkBoxHtmlOptions' => array('name' => 'bulk_item[]'),
						'visible'             => AccessHelper::hasRouteAccess('delivery_servers/bulk_action'),
					),
					array(
						'name'  => 'customer_id',
						'value' => '!empty($data->customer) ? $data->customer->getFullName() : Yii::t("app", "System")',
						'filter'=> CHtml::activeTextField($server, 'customer_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'name',
						'value' => 'empty($data->name) ? null : CHtml::link($data->name, Yii::app()->createUrl("delivery_servers/update", array("type" => $data->type, "id" => $data->server_id)))',
						'type'  => 'raw',
						'filter'=> CHtml::activeTextField($server, 'name', array('class'=>'form-control form-filter input-sm')),
					), 
					array(
						'name'  => 'hostname',
						'value' => 'CHtml::link($data->hostname, Yii::app()->createUrl("delivery_servers/update", array("type" => $data->type, "id" => $data->server_id)))',
						'type'  => 'raw',
						'filter'=> CHtml::activeTextField($server, 'hostname', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'username',
						'value' => '$data->username',
						'filter'=> CHtml::activeTextField($server, 'username', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'from_email',
						'value' => '$data->from_email',
						'filter'=> CHtml::activeTextField($server, 'from_email', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'type',
						'value' => 'DeliveryServer::getNameByType($data->type)',
						//'filter'=> $server->getTypesList(),						
						'filter'=> CHtml::activeDropDownList($server, 'type', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $server->getTypesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'status',
						'value' => 'ucfirst(Yii::t("app", $data->status))',
						//'filter'=> $server->getStatusesList(),
						'filter'=> CHtml::activeDropDownList($server, 'status',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $server->getStatusesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $server->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'     => ' &nbsp; <i class="fa fa-pencil"></i> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/update", array("type" => $data->type, "id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app','Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("delivery_servers/update") && $data->getCanBeUpdated()',
							),
							'copy'=> array(
								'label'     => ' &nbsp; <span class="glyphicon glyphicon-subtitles"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/copy", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Copy'), 'class' => 'copy-server'),
								'visible'   => 'AccessHelper::hasRouteAccess("delivery_servers/copy")',
							),
							'enable'=> array(
								'label'     => ' &nbsp; <i class="fa fa-download"></i> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/enable", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Enable'), 'class' => 'enable-server'),
								'visible'   => 'AccessHelper::hasRouteAccess("delivery_servers/enable") && $data->getIsDisabled()',
							),
							'disable'=> array(
								'label'     => ' &nbsp; <span class="glyphicon glyphicon-save"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/disable", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Disable'), 'class' => 'disable-server'),
								'visible'   => 'AccessHelper::hasRouteAccess("delivery_servers/disable") && $data->getIsActive()',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("delivery_servers/delete", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app','Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("delivery_servers/delete") && $data->getCanBeDeleted()',
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
			'controller'  => $this,
			'renderedGrid'=> $collection->renderGrid,
		)));
		?>
		</div>    
	</div>
    
    <div class="modal fade" id="csv-import-modal" tabindex="-1" role="dialog" aria-labelledby="csv-import-modal-label" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title"><?php echo Yii::t('servers', 'Import from CSV file');?></h4>
            </div>
            <div class="modal-body">
                 <div class="alert alert-success margin-bottom-15">
                    <?php echo Yii::t('servers', 'Please note, the csv file must contain a header with proper columns.');?><br />
                    <?php echo Yii::t('servers', 'If unsure about how to format your file, do an export first and see how the file looks.');?>
                 </div>
                <?php 
                $form = $this->beginWidget('CActiveForm', array(
                    'action'        => array('delivery_servers/import'),
                    'htmlOptions'   => array(
                        'id'        => 'import-csv-form', 
                        'enctype'   => 'multipart/form-data'
                    ),
                ));
                ?>
                <div class="form-group">
                    <?php echo $form->labelEx($csvImport, 'file', array('class' => 'control-label'));?>
                    <?php echo $form->fileField($csvImport, 'file', $csvImport->getHtmlOptions('file')); ?>
                    <?php echo $form->error($csvImport, 'file');?>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn dark btn-outline" data-dismiss="modal"><?php echo Yii::t('app', 'Close');?></button>
              <button type="button" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>" onclick="$('#import-csv-form').submit();"><?php echo Yii::t('app', 'Import file');?></button>
            </div>
          </div>
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