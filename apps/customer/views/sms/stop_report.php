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
	function set_form_id(rply_obj)
	{
		var test = $('input[name=mobile]').val(rply_obj);
		$('#sms-reply-model').modal('show');	
		return false;
	}
	
	function reply_sms() {
		
		var reply_form = $("#smsrply_form").attr('action');
		var formData = {
			'csrf_token': $('input[name=csrf_token]').val(),
			'mobile': $('input[name=mobile]').val(), 
			'message': $('textarea[name=message]').val(), 
		};
		
		$.ajax({
			url: reply_form,
			data: formData,
			type: "post",
			dataType: "json",
			cache: false,
			success: function(data) {
				if(data !=''){
					$("#sms_reply_ele").hide();
					$("#sms_reply_ele_button").hide();
					if(data.SUCCESS){
						$("#response_ele").html('<h3>'+data.SUCCESS+'</h3>');
						$("#response_ele").addClass('sms-reply-success');
					}else if(data.ERROR){
						$("#response_ele").html('<h3>'+data.ERROR+'</h3>');
						$("#response_ele").addClass('sms-reply-falied');
					}
					$("#response_ele").show();
				}
			}
		});
    };
	function stop_subscriber(sub_obj){
		var csrf = $('input[name=csrf_token]').val();
		$.ajax({
			url: '<?php echo $this->createUrl('sms/subscriberstop'); ?>',
			data: {'csrf':csrf,'sub_number':sub_obj},
			type: 'post',
			dataType: 'json',
			cache: true,
			success: function(data){
				alert(data);
			}
		})
	}
</script>
<?php
// and render if allowed
if ($viewCollection->renderContent) { ?>
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-envelope"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('sms/receive'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
			// since 1.3.5.6
			/*$this->widget('common.components.web.widgets.GridViewBulkAction', array(
				'model'      => $smsrply,
				'formAction' => $this->createUrl('campaigns/bulk_action'),
			));*/

			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $smsrply->modelName.'-grid',
				'dataProvider'      => $smsrply->search(),
				'filter'            => $smsrply,
				'filterPosition'    => 'body',
				'filterCssClass'    => 'grid-filter-cell',
				'itemsCssClass'     => 'table table-bordered table-hover',
				'selectableRows'    => 0,
				'enableSorting'     => false,
				'cssFile'           => false,
				'pagerCssClass'     => 'pagination pull-right',
				'rowHtmlOptionsExpression' => '["id" => $data->sms_rply_id]',
				'pager'             => array(
					'class'         => 'CLinkPager',
					'cssFile'       => false,
					'header'        => false,
					'htmlOptions'   => array('class' => 'pagination')
				),
				'columns' => $hooks->applyFilters('grid_view_columns', array(
					array(
						'name'  => 'sms_rply_time',
						'value' => '$data->sms_rply_time',
						'filter'=>	CHtml::activeTextField($smsrply, 'sms_rply_time', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'sms_rply_direction',
						'value' => '$data->sms_rply_direction',
						'filter'=>	CHtml::activeTextField($smsrply, 'sms_rply_direction', array('class'=>'form-control form-filter input-sm')),

					),
					array(
						'name'  => 'sms_rply_to_number',
						'value' => '$data->sms_rply_to_number',
						'filter' => false,

					),
					array(
						'name'  => 'sms_rply_from_number',
						'value' => '$data->sms_rply_from_number',
						'filter'=>	CHtml::activeTextField($smsrply, 'sms_rply_from_number', array('class'=>'form-control form-filter input-sm')),
					),
					/*array(
						'name'  => 'sms_rply_cost',
						'value' => '$data->sms_rply_cost'
					),*/
					array(
						'name'  => 'sms_rply_body',
						'value' => '$data->sms_rply_body',
						//'filter'=>	CHtml::activeTextField($smsrply, 'sms_rply_body', array('class'=>'form-control form-filter input-sm')),
					),
					/*array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $smsrply->paginationOptions->getGridFooterPagination(),
						'buttons'	=> array(
							'update' => array(
								'label'    => '<i class="fa fa-reply"></i>',
								'url'      => '$data->sms_rply_from_number',
								'imageUrl' => null,
								'options'  => array(
									'title' => Yii::t('lists', 'Reply'),
									'class' => 'btn btn-transparent grey-salsa btn-outline btn-sm',
									'onclick' => 'js:set_form_id($(this).attr("href"));return false;',
								),
							),
							'active' => array(
								'label' => '<i class="fa fa-toggle-on"></i>',
								'visible' => '($data->getStatus($data->sms_rply_from_number) == "confirmed" ? true : false)',
								'url' => 'Yii::app()->createUrl("sms/subscriberstop",array("stop_number" => $data->sms_rply_from_number))',
								//'url' => 'Yii::app()->createUrl("sms/subscriberactive", array("active_number" => $data->sms_rply_from_number))',
								'imageUrl' => null,
								'options' => array(
									'title' => Yii::t('lists','Active'),
									'class' => 'btn btn-transparent grey-salsa btn-outline btn-sm btn-green-active ',
								),
							),
							'deactive' => array(
								'label' => '<i class="fa fa-toggle-on"></i>',
								'visible' => '($data->getStatus($data->sms_rply_from_number) == "stop" ? true : false)',
								'url' => 'Yii::app()->createUrl("sms/subscriberactive", array("active_number" => $data->sms_rply_from_number))',
								'imageUrl' => null,
								'options' => array(
									'title' => Yii::t('lists','Active'),
									'class' => 'btn btn-transparent grey-salsa btn-outline btn-sm btn-red-active ',
								),
							),
						),
						'htmlOptions' => array(
							'style' => 'width: 180px;',
						),
						'template'=>'{update} {active}{deactive}',
					),*/
					/*sprintf(
						'js:set_form_id(%d);return false;',
						$smsrply->sms_rply_from_number
					),*/
					/*array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $campaign->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							 'view' => array(
								'label'    => ' &nbsp; <span class="glyphicon glyphicon-eye-open"></span> &nbsp;',
								'url'      => 'Yii::app()->createUrl("sms/view")',
								//'imageUrl' => null,
								'options'  => array('title' => Yii::t('lists', 'View'), 'class' => ''),
							),
						),
						
						'htmlOptions' => array(
							'style' => 'width: 180px;'
						),
						//'template'=>' '
						'template'=>'{view}',
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
?>
<?php
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
?>
