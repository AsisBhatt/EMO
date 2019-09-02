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
			<span class="glyphicon glyphicon-folder-open"></span>
			<span class="caption-subject font-dark sbold uppercase">
				 <?php echo Yii::t('activity_logs', 'View More');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php //echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('currencies/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('activity_logs/view_more'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
			</div>
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-scrollable">
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<tbody>
						<th>Table Name</th>
						<th>View Record</th>
						<th>Description</th>
						<th>Operation Type</th>
						<th>Customer Name</th>
						<th>Ip Address</th>
						<th>Created At</th>
					</tbody>
					<tbody>
						<tr>
							<td><?php echo $activity_logs_array['table_name']; ?></td>
							<td><?php echo '<a href="javascript:;" data-toggle="modal" data-target="#view_record_model">View Record</a>'; ?></td>
							<td><?php echo $activity_logs_array['log_discription']; ?></td>
							<td><?php echo $activity_logs_array['log_operation_type']; ?></td>
							<td><?php echo $activity_logs_array['customer_name']; ?></td>
							<td><?php echo $activity_logs_array['log_ip_address']; ?></td>
							<td><?php echo $activity_logs_array['created_at']; ?></td>
						</tr>
					</tbody>
				</table>
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