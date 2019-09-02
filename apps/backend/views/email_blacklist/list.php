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

   
	<div class="text-right margin">
		<a href="javascript:;" class="btn green toggle-filters-form"><?php echo Yii::t('list_subscribers', 'Toggle filters form');?></a>
	</div>
	<hr style="margin:0;">
    <?php $this->renderPartial('_filters');?>
	
	
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-ban-circle"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('email_blacklist', 'Blacklisted emails');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Remove all'), array('email_blacklist/delete_all'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm delete-all', 'title' => Yii::t('app', 'Remove all'), 'data-message' => Yii::t('dashboard', 'Are you sure you want to remove all blacklisted emails?')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Add new'), array('email_blacklist/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Add new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Export all'), array('email_blacklist/export'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Export all')));?>
				<?php 
				if (AccessHelper::hasRouteAccess('email_blacklist/import')) {
					echo CHtml::link(Yii::t('app', 'Import'), '#csv-import-modal', array('data-toggle' => 'modal', 'class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Import')));
				}
				?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('email_blacklist/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
			if (AccessHelper::hasRouteAccess('email_blacklist/bulk_action')) { 
				$this->widget('common.components.web.widgets.GridViewBulkAction', array(
					'model'      => $filter,
					'formAction' => $this->createUrl('email_blacklist/bulk_action'),
				));
			}
			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $filter->modelName.'-grid',
				'dataProvider'      => $filter->getActiveDataProvider(),
				'filter'            => null,
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
						'name'                => 'email_id',
						'checkBoxHtmlOptions' => array('name' => 'email_id[]'),
						'visible'             => AccessHelper::hasRouteAccess('email_blacklist/bulk_action'),
					),
					array(
						'name'  => 'email',
						'value' => '$data->email',
						'filter'=> false,
					),
					array(
						'name'  => 'reason',
						'value' => '$data->reason',
						'filter'=> false,
					),
					array(
						'name'  => 'date_added',
						'value' => '$data->dateAdded',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $filter->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'     => ' &nbsp; <span class="fa fa-pencil"></span> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("email_blacklist/update", array("id" => $data->email_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("email_blacklist/update")',
							),
							'delete' => array(
								'label'     => ' &nbsp; <i class="fa fa-trash-o" aria-hidden="true"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("email_blacklist/delete", array("id" => $data->email_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("email_blacklist/delete")',
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
	</div>
    
    <div class="modal fade" id="csv-import-modal" tabindex="-1" role="dialog" aria-labelledby="csv-import-modal-label" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title"><?php echo Yii::t('email_blacklist', 'Import from CSV file');?></h4>
            </div>
            <div class="modal-body">
                 <div class="alert alert-success margin-bottom-15">
                    <?php echo Yii::t('email_blacklist', 'Please note, the csv file must contain a header with at least the email column.');?><br />
                    <?php echo Yii::t('email_blacklist', 'If unsure about how to format your file, do an export first and see how the file looks.');?>
                 </div>
                <?php 
                $form = $this->beginWidget('CActiveForm', array(
                    'action'        => array('email_blacklist/import'),
                    'htmlOptions'   => array(
                        'id'        => 'import-csv-form', 
                        'enctype'   => 'multipart/form-data'
                    ),
                ));
                ?>
                <div class="form-group">
                    <?php echo $form->labelEx($blacklist, 'file', array('class' => 'control-label'));?>
                    <?php echo $form->fileField($blacklist, 'file', $filter->getHtmlOptions('file')); ?>
                    <?php echo $form->error($blacklist, 'file');?>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn dark btn-outline" data-dismiss="modal"><?php echo Yii::t('app', 'Close');?></button>
              <button type="button" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>" onclick="$('#import-csv-form').submit();">Import file</button>
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