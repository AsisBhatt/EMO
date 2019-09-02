<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.9
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
			<span class="glyphicon glyphicon-ban-circle"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('email_blacklist_monitors/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Add new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('email_blacklist_monitors/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
				'id'                => $monitor->modelName.'-grid',
				'dataProvider'      => $monitor->search(),
				'filter'            => $monitor,
				'filterPosition'    => 'body',
				'filterCssClass'    => 'grid-filter-cell',
				'itemsCssClass'     => 'table table-bordered table-hover table-striped',
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
						'value' => '$data->name',
						'filter'=>	CHtml::activeTextField($monitor, 'name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'email_condition',
						'value' => 'Yii::t("email_blacklist", ucfirst($data->email_condition))',
						'filter'=> CHtml::activeDropDownList($monitor, 'email_condition', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $monitor->getConditionsList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'email',
						'value' => '$data->email',
						'filter'=>	CHtml::activeTextField($monitor, 'email', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'reason_condition',
						'value' => 'Yii::t("email_blacklist", ucfirst($data->reason_condition))',
						'filter'=> CHtml::activeDropDownList($monitor, 'reason_condition', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $monitor->getConditionsList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'reason',
						'value' => '$data->reason',
						'filter'=>	CHtml::activeTextField($monitor, 'reason', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'condition_operator',
						'value' => 'Yii::t("email_blacklist", ucfirst($data->condition_operator))',
						'filter'=> CHtml::activeDropDownList($monitor, 'condition_operator', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $monitor->getConditionOperatorsList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'notifications_to',
						'value' => '$data->notifications_to',
						'filter'=> true,
					),
					array(
						'name'  => 'status',
						'value' => '$data->statusName',
						'filter'=> CHtml::activeDropDownList($monitor, 'status', CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $monitor->getStatusesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'date_added',
						'value' => '$data->dateAdded',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $monitor->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'     => ' &nbsp; <span class="fa fa-pencil"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("email_blacklist_monitors/update", array("id" => $data->monitor_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("email_blacklist_monitors/update")',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("email_blacklist_monitors/delete", array("id" => $data->monitor_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("email_blacklist_monitors/delete")',
							),    
						),
						'htmlOptions' => array(
							'style' => 'width:70px;',
						),
						'template' => '{update} {delete}'
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
			<?php echo Yii::t('email_blacklist', 'Blacklist monitors will monitor the email blacklist and when emails matching the conditions will be added in the blacklist, they will be removed automatically and subscribers matching the emails will be marked back as confirmed.');?><br />
			<?php echo Yii::t('email_blacklist', 'Please note that in order for the monitoring to work, you need to add the following cron job, which runs once per hour:');?><br />
			<span class="badge">0 */1 * * * <?php echo CommonHelper::findPhpCliPath();?> -q <?php echo MW_PATH;?>/apps/console/console.php email-blacklist-monitor >/dev/null 2>&1 </span>
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