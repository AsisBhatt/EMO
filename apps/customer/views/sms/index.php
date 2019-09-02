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
			<span class="glyphicon glyphicon-envelope"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('sms/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
			$this->widget('common.components.web.widgets.GridViewBulkAction', array(
				'model'      => $sms,
				'formAction' => $this->createUrl('campaigns/bulk_action'),
			));

			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $sms->modelName.'-grid',
				'dataProvider'      => $sms->search(),
				'filter'            => $sms,
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
						'name'  => 'mobile',
						'value' => '$data->mobile',
						'filter'=>	CHtml::activeTextField($sms, 'mobile', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'message',
						'value' => '$data->message',
						'filter'=>	CHtml::activeTextField($sms, 'message', array('class'=>'form-control form-filter input-sm')),

					),
					array(
						'name' 	=> 'media',
						//'value'	=> '$data->media',
						'value' => function($data){
							$media_types = array();
							$media_types['images'] = array('jpg','jpeg','bmp','png','gif');
							$media_types['audio']  = array('mp3','wav','aiff');
							$media_types['video']  = array('mp4','flv','avi','3gp');
							
							if($data->media != ''){
								$media_array = explode("/",$data->media);
								$info = new SplFileInfo($media_array[4]);
								if(in_array($info->getExtension(),$media_types['images'])){
									echo '<a href="'.$data->media.'" target="_blank"><i class="fa fa-picture-o fa-2" aria-hidden="true"></i></a>';
								}else if(in_array($info->getExtension(),$media_types['audio'])){
									echo '<a href="'.$data->media.'" target="_blank"><i class="fa fa-file-audio-o fa-2" aria-hidden="true"></i></a>';
								}else if(in_array($info->getExtension(),$media_types['video'])){
									echo '<a href="'.$data->media.'" target="_blank"><i class="fa fa-file-video-o fa-2" aria-hidden="true"></i></a>';
								}
							}
						},
						'filter' => false,
						'visible'=> (isset($_GET['mms'])) ? true : false,
						/*'visible' => function ($data) {
							if ($data->media != '') {
								return true; // or return true;
							} else {
								return false; // or return false;
							}
						},*/
					),
					array(
						'name'  => 'response',
						'value' => '$data->response',
						'filter'=>	CHtml::activeTextField($sms, 'response', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'status',
						'value' => '$data->status',
						'filter'=>	CHtml::activeTextField($sms, 'status', array('class'=>'form-control form-filter input-sm')),
					),
				   
					array(
						'name'  => 'date_added',
						'value' => '$data->date_added',
						'filter'=> false,
					),
					
					/*array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $campaign->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							
							 
							 'view' => array(
								'label'    => ' &nbsp; <span class="glyphicon glyphicon-eye-open"></span> &nbsp;',
								'url'      => 'Yii::app()->createUrl("sms/view", array("id" => $data->sms_id))',
								'imageUrl' => null,
								'options'  => array('title' => Yii::t('lists', 'View'), 'class' => ''),
							),
							
						  
						),
						
						'htmlOptions' => array(
							'style' => 'width: 180px;'
						),
						'template'=>' '
						//'template'=>'{view} '
					),
					*/
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
