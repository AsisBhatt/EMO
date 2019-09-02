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
?>
	<script>
		function get_view_more(log_id){
			var view_url = '<?php echo $this->createUrl('activity_logs/view_more'); ?>';
			
			$.ajax({
				url: view_url,
				data: {'log_id':log_id},
				type: "post",
				dataType: "json",
				cache: false,
				success: function(data) {
					var trHTML = '';
					$('#view_record_model').modal('show');
					if(data.ERROR){
						$("#log_error").show();
						$("#log_error").html(data.ERROR);
					}else{			
						//console.log(data.model_data);
						$.each(data.model_data, function(key, value){
							$("#model_data tr:first").append('<th>'+key+'</th>');
							$('#model_data tr').eq(1).append('<td>'+value+'</td>')
						});
					}
					$('#view_record_model').on('hidden.bs.modal', function (e) {
						window.location.reload();
					})
				}
			});
		};
	</script>
<?php
// and render if allowed
if ($viewCollection->renderContent) { ?>
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-folder-open"></span>
			<span class="caption-subject font-dark sbold uppercase">
				 <?php echo Yii::t('activity_logs', 'Activity Logs');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php //echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('currencies/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('activity_logs/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
				'id'                => $activity_logs->modelName.'-grid',
				'dataProvider'      => $activity_logs->search(),
				'filter'            => $activity_logs,
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
						'name'  => 'log_discription',
						'value' => '$data->log_discription',
						'filter'=>	CHtml::activeTextField($activity_logs, 'log_discription', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'log_operation_type',
						'value' => '$data->log_operation_type',
						'filter'=>	CHtml::activeTextField($activity_logs, 'log_operation_type', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'user_login_id',
						'value' => '$data->customer->fullName',
						'filter'=>	CHtml::activeTextField($activity_logs, 'user_login_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'log_ip_address',
						'value' => '$data->log_ip_address',
						'filter'=>	CHtml::activeTextField($activity_logs, 'log_ip_address', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name' => 'created_at',
						'value' => '$data->created_at',
						'filter' => false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'View'),
						'footer'    => $activity_logs->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'view_more' => array(
								'label'     => ' &nbsp; <i class="fa fa-eye"></i> &nbsp;', 
								'url'       => '$data->log_id',
								'imageUrl'  => null,
								'options'   => array(
									'title' => Yii::t('app', 'View More'), 'class' => '',
									'onclick' => 'js:get_view_more($(this).attr("href"));return false;',
								),
								//'visible'   => 'AccessHelper::hasRouteAccess("currencies/update")',
								
							),
							/*'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("currencies/delete", array("id" => $data->currency_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("currencies/delete") && $data->isRemovable',
							),*/
						),
						'htmlOptions' => array(
							'style' => 'text-align:center;',
						),
						'template' => '{view_more}'
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
	<div class="modal fade" id="view_record_model" tabindex="-1" role="dialog" aria-labelledby="view_record_model-label" aria-hidden="true">
		<div class="modal-dialog modal-lg">
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="javascript:window.location.reload()">&times;</button>
			  <h4 class="modal-title"><?php echo Yii::t('activity_logs', 'View Record');?></h4>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-striped no-margin" id="model_data">
						<tr>
						</tr>
						<tr>
						</tr>
						<!--<tr>
							<?php 
								//$model_data = array_values($activity_logs_array['model_data'][0]);
								//foreach($model_data as $model => $value){ 
							?>
								<td><?php //echo $value; ?></td>
							<?php //} ?>
						</tr>-->
					</table>
					<div id="log_error" class="alert alert-danger text-center" style="display:none;"></div>
				</div>
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