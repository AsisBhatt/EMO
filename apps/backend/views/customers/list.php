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
			<span class="glyphicon glyphicon-user"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('customers', 'Merchant Customer');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('customers/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('customers', 'Manage groups'), array('customer_groups/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('customers', 'Manage groups')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('customers/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
			</div>
		</div>
		<div class="clearfix"><!-- --></div>
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
					'id'                => $customer->modelName.'-grid',
					'dataProvider'      => $customer->search(),
					'filter'            => $customer,
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
							'name'  => 'first_name',
							'value' => '$data->first_name',
							'filter'=>	CHtml::activeTextField($customer, 'first_name', array('class'=>'form-control form-filter input-sm')),
						),
						array(
							'name'  => 'last_name',
							'value' => '$data->last_name',
							'filter'=>	CHtml::activeTextField($customer, 'last_name', array('class'=>'form-control form-filter input-sm')),
						),
						array(
							'name'  => 'email',
							'value' => '$data->email',
							'filter'=>	CHtml::activeTextField($customer, 'email', array('class'=>'form-control form-filter input-sm')),
						),
						array(
							'name'  => 'company_name',
							'value' => '!empty($data->company) ? $data->company->name : "-"',
							'filter'=>	CHtml::activeTextField($customer, 'company_name', array('class'=>'form-control form-filter input-sm')),
						),
						array(
							'name'  => 'group_id',
							'value' => '!empty($data->group_id) ? CHtml::link($data->group->name, array("customer_groups/update", "id" => $data->group_id)) : "-"',
							'type'  => 'raw',
							'filter'=> CHtml::activeDropDownList($customer, 'group_id',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), CustomerGroup::getGroupsArray()), array('class'=>'form-control form-filter input-sm')),
						),
						array(
							'name'  => 'sending_quota_usage',
							'value' => '$data->getSendingQuotaUsageDisplay()."<br/>Email : ".((int)$data->getGroupOption("sending.quota", -1) == -1 ? "Unlimited" : (int)$data->getGroupOption("sending.quota", -1))."<br/><span class=highlight_green>Used : ".$data->countUsageFromQuotaMark()."</span><br/>SMS :".((int)$data->getGroupOption("smssending.sms_quota", -1) == -1 ? "Unlimited" : (int)$data->getGroupOption("smssending.sms_quota", -1))."<br/><span class=highlight_green>Used : ".$data->countUsageSmsFromQuotaMark()."</span>"',
							'type'  => 'raw',
							'filter'=> false,
						),
						array(
							'name'  => 'status',
							'value' => '$data->status',
							//'filter'=> $customer->getStatusesArray(),
							'filter'=> CHtml::activeDropDownList($customer, 'status',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $customer->getStatusesArray()), array('class'=>'form-control form-filter input-sm')),
						),
						array(
							'name'  => 'date_added',
							'value' => '$data->dateAdded',
							'filter'=> false,
						),
						array(
							'class'     => 'CButtonColumn',
							'header'    => Yii::t('app', 'Options'),
							'footer'    => $customer->paginationOptions->getGridFooterPagination(),
							'buttons'   => array(
								'impersonate' => array(
									'label'     => ' &nbsp; <i class="fa fa-random"></i> &nbsp;', 
									'url'       => 'Yii::app()->createUrl("customers/impersonate", array("id" => $data->customer_id))',
									'imageUrl'  => null,
									'options'   => array('title' => Yii::t('app', 'Login as this customer'), 'class' => ''),
									'visible'   => 'AccessHelper::hasRouteAccess("customers/impersonate")',
								),
								'reset_quota' => array(
									'label'     => ' &nbsp; <i class="fa fa-refresh"></i> &nbsp;', 
									'url'       => 'Yii::app()->createUrl("customers/reset_sending_quota", array("id" => $data->customer_id))',
									'imageUrl'  => null,
									'options'   => array('title' => Yii::t('app', 'Reset sending quota'), 'class' => 'reset-sending-quota', 'data-message' => Yii::t('customers', 'Are you sure you want to reset the sending quota for this customer?')),
									'visible'   => 'AccessHelper::hasRouteAccess("customers/reset_sending_quota")',
								),
								'update' => array(
									'label'     => ' &nbsp; <i class="fa fa-pencil"></i> &nbsp;', 
									'url'       => 'Yii::app()->createUrl("customers/update", array("id" => $data->customer_id))',
									'imageUrl'  => null,
									'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
									'visible'   => 'AccessHelper::hasRouteAccess("customers/update")',
								),
								'delete' => array(
									'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
									'url'       => 'Yii::app()->createUrl("customers/delete", array("id" => $data->customer_id))',
									'imageUrl'  => null,
									'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
									'visible'   => 'AccessHelper::hasRouteAccess("customers/delete") && $data->removable === Customer::TEXT_YES',
								),
								'reports' => array(
									'label' => ' &nbsp; <i class="fa fa-files-o" aria-hidden="true"></i> &nbsp;',
									'url' => 'Yii::app()->createUrl("customers/report", array("id" => $data->customer_id))',
									'imageUrl' => null,
									'options' => array('title' => Yii::t('app', 'SMS Stop Count Report'), 'class' => 'reports'),
									'visible' => 'AccessHelper::hasRouteAccess("customer/report")',
								),
							),
							'htmlOptions' => array(
								'style' => 'width:130px;',
							),
							'template' => '{impersonate} {reset_quota} {update} {delete} {reports}'
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