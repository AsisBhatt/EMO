<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.1
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
			<span class="glyphicon glyphicon-flag"></span>
			<span class="caption-subject font-dark sbold uppercase">
				 <?php echo Yii::t('languages', 'Available languages');?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php 
				if (AccessHelper::hasRouteAccess('languages/upload')) {
					echo CHtml::link(Yii::t('languages', 'Upload language pack'), '#language-upload-modal', array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'data-toggle' => 'modal', 'title' => Yii::t('languages', 'Upload language pack')));
				}
				?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('languages/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
				<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('languages/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $language->modelName.'-grid',
				'dataProvider'      => $language->search(),
				'filter'            => $language,
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
						'name'  => 'name',
						'value' => '$data->name',
						'filter'=>	CHtml::activeTextField($language, 'name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'language_code',
						'value' => '$data->language_code',
						'filter'=> false,
					),
					array(
						'name'  => 'region_code',
						'value' => '$data->region_code',
						'filter'=> false,
					),
					array(
						'name'  => 'is_default',
						'value' => 'Yii::t("app", ucfirst($data->is_default))',
						'filter'=> false,
					),
					array(
						'name'  => 'date_added',
						'value' => '$data->dateAdded',
						'filter'=> false,
					),
					array(
						'name'  => 'last_updated',
						'value' => '$data->lastUpdated',
						'filter'=> false,
					),
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $language->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							'update' => array(
								'label'     => '&nbsp; <i class="fa fa-pencil"></i> &nbsp;', 
								'url'       => 'Yii::app()->createUrl("languages/update", array("id" => $data->language_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
								'visible'   => 'AccessHelper::hasRouteAccess("languages/update")',
							),
							'delete' => array(
								'label'     => '&nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
								'url'       => 'Yii::app()->createUrl("languages/delete", array("id" => $data->language_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
								'visible'   => 'AccessHelper::hasRouteAccess("languages/delete") && $data->is_default === Language::TEXT_NO',
							),    
						),
						// 'htmlOptions' => array(
							// 'style' => 'width:70px;',
						// ),
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
			'controller'    => $this,
			'renderedGrid'  => $collection->renderGrid,
		)));
		?>
		<div class="clearfix"><!-- --></div>
		</div>    
	</div>
    
    <div class="modal fade" id="language-upload-modal" tabindex="-1" role="dialog" aria-labelledby="language-upload-modal-label" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title"><?php echo Yii::t('languages', 'Upload language pack')?></h4>
            </div>
            <div class="modal-body">
                 <div class="alert alert-success margin-bottom-20">
                     <?php echo Yii::t('languages', 'Please note that only zip files are allowed for upload.')?><br />
                     <strong><?php echo Yii::t('app', 'Warning');?></strong>: <?php echo Yii::t('languages', 'Language packs contain executable PHP files, please check the packs before upload.')?>
                 </div>
                <?php 
                $form = $this->beginWidget('CActiveForm', array(
                    'action'        => array('languages/upload'),
                    'id'            => $languageUpload->modelName.'-upload-form',
                    'htmlOptions'   => array('enctype' => 'multipart/form-data'),
                ));
                ?>
                <div class="form-group">
                    <?php echo $form->labelEx($languageUpload, 'archive', array('class' => 'control-label'));?>
                    <?php echo $form->fileField($languageUpload, 'archive', $languageUpload->getHtmlOptions('archive')); ?>
                    <?php echo $form->error($languageUpload, 'archive');?>
                </div>
                <?php $this->endWidget(); ?>
            </div>
            <div class="modal-footer">
				<button type="button" class="btn dark btn-outline" data-dismiss="modal"><?php echo Yii::t('app', 'Close');?></button>
				<button type="button" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>" onclick="$('#<?php echo $languageUpload->modelName;?>-upload-form').submit();"><?php echo Yii::t('app', 'Upload archive');?></button>
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