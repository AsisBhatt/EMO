<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.6
 */

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false 
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
$hooks->doAction('views_before_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) { ?>
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-envelope"></i>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('transactional_emails/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
		$hooks->doAction('views_before_grid', $collection = new CAttributeCollection(array(
			'controller'   => $this,
			'renderGrid'   => true,
		)));
		
		// and render if allowed
		if ($collection->renderGrid) {
			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('views_grid_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $email->modelName.'-grid',
				'dataProvider'      => $email->search(),
				'filter'            => $email,
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
				'columns' => $hooks->applyFilters('views_grid_columns', array(
					array(
						'name'  => 'to_email',
						'value' => '$data->to_email',
						'filter'=>	CHtml::activeTextField($email, 'to_email', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'to_name',
						'value' => '$data->to_name',
						'filter'=>	CHtml::activeTextField($email, 'to_name', array('class'=>'form-control form-filter input-sm')),
					),
					
					array(
						'name'  => 'reply_to_email',
						'value' => '$data->reply_to_email',
						'filter'=>	CHtml::activeTextField($email, 'reply_to_email', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'reply_to_name',
						'value' => '$data->reply_to_name',
						'filter'=>	CHtml::activeTextField($email, 'reply_to_name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'from_email',
						'value' => '$data->from_email',
						'filter'=>	CHtml::activeTextField($email, 'from_email', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'from_name',
						'value' => '$data->from_name',
						'filter'=>	CHtml::activeTextField($email, 'from_name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'subject',
						'value' => '$data->subject',
						'filter'=>	CHtml::activeTextField($email, 'subject', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'status',
						'value' => '$data->statusName',
						//'filter'=> $email->getStatusesList(),
						'filter'=> CHtml::activeDropDownList($email, 'status',  CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $email->getStatusesList()), array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'send_at',
						'value' => '$data->sendAt',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $email->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'resend' => array(
								'label'     => ' &nbsp; <i class="fa fa-play-circle-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("transactional_emails/resend", array("id" => $data->email_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Resend')),
								'visible'   => '$data->status == TransactionalEmail::STATUS_SENT && AccessHelper::hasRouteAccess("transactional_emails/resend")',
							),
							'preview' => array(
								'label'     => ' &nbsp; <i class="fa fa-eye"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("transactional_emails/preview", array("id" => $data->email_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Preview'), 'class' => 'preview-transactional-email', 'target' => '_blank'),
								'visible'   => 'AccessHelper::hasRouteAccess("transactional_emails/preview")',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("transactional_emails/delete", array("id" => $data->email_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("transactional_emails/delete")',
							),    
						),
						'htmlOptions' => array(
							'style' => 'width:100px;',
						),
						'template' => '{resend} {preview} {delete}'
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
		$hooks->doAction('views_after_grid', new CAttributeCollection(array(
			'controller'   => $this,
			'renderedGrid' => $collection->renderGrid,
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
$hooks->doAction('views_after_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));