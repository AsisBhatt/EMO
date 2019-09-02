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
			<span class="glyphicon glyphicon-transfer"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('servers', 'Feedback loop servers');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('feedback_loop_servers/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('feedback_loop_servers/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
			if (AccessHelper::hasRouteAccess('feedback_loop_servers/bulk_action')) { 
				$this->widget('common.components.web.widgets.GridViewBulkAction', array(
					'model'      => $server,
					'formAction' => $this->createUrl('feedback_loop_servers/bulk_action'),
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
						'visible'             => AccessHelper::hasRouteAccess('feedback_loop_servers/bulk_action'),
					),
					array(
						'name'  => 'customer_id',
						'value' => '!empty($data->customer) ? $data->customer->getFullName() : Yii::t("app", "System")',
						'filter'=> CHtml::activeTextField($server, 'customer_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'hostname',
						'value' => 'CHtml::link($data->hostname, Yii::app()->createUrl("feedback_loop_servers/update", array("id" => $data->server_id)))',
						'type'  => 'raw',
						'filter'=> CHtml::activeTextField($server, 'customer_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'username',
						'value' => '$data->username',
						'filter'=> CHtml::activeTextField($server, 'username', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'service',
						'value' => '$data->serviceName',
						'filter'=> CHtml::activeDropDownList($server, 'service', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $server->getServicesArray()), array('class'=>'form-control form-filter input-sm'))
					),
					
					array(
						'name'  => 'port',
						'value' => '$data->port',
						'filter'=> CHtml::activeTextField($server, 'port', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'protocol',
						'value' => '$data->protocolName',
						'filter'=> CHtml::activeDropDownList($server, 'protocol', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $server->getProtocolsArray()), array('class'=>'form-control form-filter input-sm'))
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
								'url'       => 'Yii::app()->createUrl("feedback_loop_servers/update", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("feedback_loop_servers/update")',
							),
							'copy'=> array(
								'label'     => ' &nbsp; <span class="glyphicon glyphicon-subtitles"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("feedback_loop_servers/copy", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Copy'), 'class' => 'copy-server'),
								'visible'   => 'AccessHelper::hasRouteAccess("feedback_loop_servers/copy")',
							),
							'enable'=> array(
								'label'     => ' &nbsp; <span class="fa fa-eye"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("feedback_loop_servers/enable", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Enable'), 'class' => 'enable-server'),
								'visible'   => 'AccessHelper::hasRouteAccess("feedback_loop_servers/enable") && $data->getIsDisabled()',
							),
							'disable'=> array(
								'label'     => ' &nbsp; <span class="glyphicon glyphicon-save"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("feedback_loop_servers/disable", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Disable'), 'class' => 'disable-server'),
								'visible'   => 'AccessHelper::hasRouteAccess("feedback_loop_servers/disable") && $data->getIsActive()',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("feedback_loop_servers/delete", array("id" => $data->server_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("feedback_loop_servers/delete")',
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
		<div class="alert alert-success">
			<?php 
			$text = 'Please note, when adding a feedback loop server make sure the email address is used only for reading feedback email but nothing more.<br />
			This is important since the script that checks the bounced emails needs to read all the emails from the account you specify and beside it can be time and memory consuming, it will also delete all the emails from the email account.';
			echo Yii::t('servers', StringHelper::normalizeTranslationString($text));
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