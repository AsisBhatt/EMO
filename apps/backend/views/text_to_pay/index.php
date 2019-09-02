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
if ($viewCollection->renderContent) { 

?>
	<script>
		function update_status(auto_template_id){
			
			var Ajax_url = '<?php echo $this->createUrl('autoreply_template/updatestatus'); ?>';
			
			$.ajax({
				url: Ajax_url,
				data: {'auto_template_id':auto_template_id},
				type: 'post',
				dataType: "json",
				cache: false,
				success: function(data){
					if(data.SUCCESS){
						$("#response_ele").html('<h3>'+data.SUCCESS+'</h3>');
						$("#response_ele").addClass('sms-reply-success');
						$("#response_ele").show();
						window.location.href = '<?php echo $this->createUrl('autoreply_template/index'); ?>';
					}else if(data.ERROR){
						$("#response_ele").html('<h3>'+data.ERROR+'</h3>');
						$("#response_ele").addClass('sms-reply-danger');
						$("#response_ele").show();
						window.location.href = '<?php echo $this->createUrl('autoreply_template/index'); ?>';
					}
				}
			});
		}
	</script>
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-envelope"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php //if($text_reply->isNewRecord()){ ?>
					<?php echo CHtml::link(Yii::t('app', 'Create'), array('autoreply_template/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create')));?>
				<?php //} ?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('sms/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
			</div>
		</div>
	</div>
	<div id="response_ele" style="display:none;" class=""></div>
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
			// since 1.3.5.6
			/*$this->widget('common.components.web.widgets.GridViewBulkAction', array(
				'model'      => $autoreply_template,
				'formAction' => $this->createUrl('autoreply_template/bulk_action'),
			));*/

			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $text_reply->modelName.'-grid',
				'dataProvider'      => $text_reply->search(),
				'filter'            => $text_reply,
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
						'name'  => 'text_mdr_id',
						'value' => '$data->text_mdr_id',
						'filter'=>	CHtml::activeTextField($text_reply, 'text_mdr_id', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'text_from_number',
						'value' => '$data->text_from_number',
						'filter'=>	CHtml::activeTextField($text_reply, 'text_from_number', array('class'=>'form-control form-filter input-sm')),
					),
				   
					array(
						'name'  => 'text_to_number',
						'value' => '$data->text_to_number',
						'filter'=>	CHtml::activeTextField($text_reply, 'text_to_number', array('class'=>'form-control form-filter input-sm')),
					),
					
					/*array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $text_reply->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'    => ' &nbsp; <span class="fa fa-pencil"></span> &nbsp;',
								'url'      => 'Yii::app()->createUrl("autoreply_template/update", array("id" => $data->auto_temp_id))',
								'imageUrl' => null,
								'options'  => array('title' => Yii::t('autoreply_template', 'Edit'), 'class' => ''),
							),
							'delete' => array(
								'label'     => ' &nbsp; <span class="fa fa-trash-o"></span> &nbsp; ',
								'url'       => 'Yii::app()->createUrl("autoreply_template/delete", array("id" => $data->auto_temp_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
							),
							 'view' => array(
								'label'    => ' &nbsp; <span class="glyphicon glyphicon-eye-open"></span> &nbsp;',
								'url'      => '$data->auto_temp_id',
								'imageUrl' => null,
								'options'  => array(
									'title' => Yii::t('lists', 'View'), 
									'class' => '', 
									'onclick' => 'js:update_status($(this).attr("href"));return false;',
								),
							),
						),
						
						'htmlOptions' => array(
							'style' => 'width: 180px;'
						),
						//Yii::app()->createUrl("sms/view", array("id" => $data->auto_temp_id))
						//'template'=>' '
						'template'=>'{view} {update} {delete}'
					),*/
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
