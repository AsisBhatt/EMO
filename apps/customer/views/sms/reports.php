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
?>
<script>
	$(document).ready(function(){
		//create_chart(0);
		$("#frm_date").datetimepicker({
			format: 'dd-mm-yyyy',
			minView: 2,
			maxView: 4,
			autoclose: true
		});

		$("#to_date").datetimepicker({
			format: 'dd-mm-yyyy',
			minView: 2,
			maxView: 4,
			autoclose: true
		});
	});
	function search_sms() {
		var search_form = $("#sms_search_form").attr('action');
		var frm_date = $('input[name=frm_date]').val();
		var formData = {
			'csrf_token': $('input[name=csrf_token]').val(),
			'frm_date': $('input[name=frm_date]').val(), 
			'to_date': $('input[name=to_date]').val(), 
		};
		$.ajax({
			url: search_form,
			data: formData,
			type: "post",
			dataType: "json",
			cache: false,
			success: function(data){
				 $("#mycanvas").fadeOut(800, function(){
					create_chart(data);
					//$("#mycanvas").hide();
					$('.btn-submit').prop("disabled", false);
					$('.btn-submit').removeClass("disabled");
				});
				
			}
		})
	}	
</script>
<?php if ($viewCollection->renderContent) { ?>
	<div class="portlet-body">
		<div class="row">
			<?php
				$form = $this->beginWidget('CActiveForm', array(
					'action'        => array('sms/reports'),
					'htmlOptions'   => array(
						'id'        => 'sms_search_form',
					),
				));
			?>
				<div class="form-group col-lg-2">
					<label class="control-label">From :</label>
					<?php echo CHtml::textField('frm_date','',array('class'=> 'form-control')); ?>
				</div>		
				<div class="form-group col-lg-2">
					<label class="control-label">To :</label>
					<?php echo CHtml::textField('to_date','',array('class'=> 'form-control')); ?>
				</div>
				<div class="form-group col-lg-8">
					<br/>
					<button type="submit" class="btn green btn-submit"><?php echo Yii::t('app', 'Submit');?></button>
					<a href="<?php echo $this->createUrl('sms/reports'); ?>" class="btn green btn-submit">View All Records</a>
				</div>
			<?php $this->endWidget(); ?>
		</div>		
		<!--onclick="search_sms();"-->
		<div id="chart-container">
			<?php
				$this->widget('customer.extensions.widgets.highcharts.HighchartsWidget', array(
					'scripts' => array(
						'modules/exporting',
						'themes/epcGray',
					),
					'options' => array(
						'title' => array(
							'text' => '',
						),
						'xAxis' => array(
							'categories' => $date_array['date'],
						),
						'labels' => array(
							'items' => array(
								array(
									'html' => 'Total Send and Not Send SMS',
									'style' => array(
										'left' => '50px',
										'top' => '18px',
										'color' => 'js:(Highcharts.theme && Highcharts.theme.textColor) || \'black\'',
									),
								),
							),
						),
						'credits' => array('enabled' => false),
						'series' => array(
							array(
								'type' => 'column',
								'name' => 'Send',
								'data' => $date_array['send_count'],
								//$date_array['send_count']
							),
							array(
								'type' => 'column',
								'name' => 'Not Send',
								'data' => $date_array['notsend_count'],
							),
							/*array(
								'type' => 'column',
								'name' => 'Joe',
								'data' => array(4, 3, 3, 9, 8),
							),*/
							/*array(
								'type' => 'spline',
								'name' => 'Average',
								'data' => array(3, 2.67, 3, 6.33, 3.33),
								'marker' => array(
									'lineWidth' => 2,
									'lineColor' => 'js:Highcharts.getOptions().colors[3]',
									'fillColor' => 'white',
								),
							),*/
						),
					)
				));
			?>
			<!--- Data Here -->
			<div class="col-md-12 col-sm-6 col-xs-12">
				<table class="table table-bordered table-striped table-hover custom-table">
					<tr align="center">
						<td>Mobile</td>
						<td>Message</td>
						<td>Response</td>
						<td>Status</td>
						<td>Date added</td>
					</tr>
					<?php 
						if(is_array($send_sms) && count($send_sms)){
							foreach($send_sms as $sendsms){
					?>
					<tr>
						<td><?php echo $sendsms['mobile']; ?></td>
						<td><?php echo $sendsms['message']; ?></td>
						<td><?php echo $sendsms['response']; ?></td>
						<td><?php echo $sendsms['status']; ?></td>
						<td><?php echo $sendsms['date_added']; ?></td>
					</tr>
					<?php
							}
						}
					?>
				</table>
			</div>
			<!-- Data End Here -->			
			<div class="row">
				<div class="col-md-4 col-sm-6 col-xs-12 custom-chart">
					<?php
						if($rem_quota != 'UNLIMITED'){
							$this->widget('customer.extensions.widgets.cvisualizewidget.CVisualizeWidget',array(
								'data'=>array(
									'data'=>array(
										'Send' => array($sens_sms_count),
										'Not send' => array($notsend_sms_count),
										'Remaining Quota'=>array($rem_quota)
									)
								),
								'options' => array(
									'title'=>'SMS Reports',
									'type'=>'pie',
									'width'=>200,
									'height'=>200
								)
							));
						}
					?>
				</div>
				<div class="col-md-4 col-sm-6 col-xs-12">
					<table class="table table-bordered table-striped table-hover custom-table">
						<tbody>
							<tr>
								<td>Sent Sms :</td>
								<td><?php echo $sens_sms_count; ?></td>
							</tr>
							<tr>
								<td>Not Sent Sms :</td>
								<td><?php echo $notsend_sms_count; ?></td>
							</tr>
							<tr>
								<td>Remaining Quota :</td>
								<td><?php echo $rem_quota; ?></td>
							</tr>
							<tr>
								<td>Total Quota :</td>
								<td><?php echo $rem_quota + $sens_sms_count; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!--<canvas id="mycanvas"></canvas>
			<canvas id="searching_data"></canvas>-->
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