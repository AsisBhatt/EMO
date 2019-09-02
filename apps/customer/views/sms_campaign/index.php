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
				<?php
					$type = Yii::app()->request->getQuery('type');
					echo CHtml::link(Yii::t('app', 'Create new'), array('sms_campaign/create/type/'.$type), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));
				?>
				<?php echo CHtml::link(Yii::t('app', 'Refresh'), array('sms_campaign/index/type/'.$type), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
				'model'      => $smscampaign,
				//'formAction' => $this->createUrl('campaigns/bulk_action'),
			));*/

			$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
				'ajaxUrl'           => $this->createUrl($this->route),
				'id'                => $smscampaign->modelName.'-grid',
				'dataProvider'      => $smscampaign->search(),
				'filter'            => $smscampaign,
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
						'name'  => 'campaign_name',
						'value' => '$data->campaign_name',
						'filter'=>	CHtml::activeTextField($smscampaign, 'campaign_name', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name'  => 'campaign_text',
						'value' => '$data->campaign_text',
						'filter'=>	CHtml::activeTextField($smscampaign, 'campaign_text', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name' => 'campaign_media',
						'value' => function($data){
							$camp_media_types['images'] = array('jpg','jpeg','bmp','png','gif');
							$camp_media_types['audio']  = array('mp3','wav','aiff');
							$camp_media_types['video']  = array('mp4','flv','avi','3gp');
							
							if($data->campaign_media != ''){
								$camp_media_type_array = explode("/",$data->campaign_media);
								$camp_media_info = new SplFileInfo($camp_media_type_array[4]);
								if(in_array($camp_media_info->getExtension(),$camp_media_types['images'])){
									echo '<a href="'.urldecode($data->campaign_media).'" target="_blank"><i class="fa fa-picture-o fa-2" aria-hidden="true"></i></a>';
								}else if(in_array($camp_media_info->getExtension(),$camp_media_types['audio'])){
									echo '<a href="'.urldecode($data->campaign_media).'" target="_blank"><i class="fa fa-file-audio-o fa-2" aria-hidden="true"></i></a>';
								}else if(in_array($camp_media_info->getExtension(),$camp_media_types['video'])){
									echo '<a href="'.urldecode($data->campaign_media).'" target="_blank"><i class="fa fa-file-video-o fa-2" aria-hidden="true"></i></a>';
								}
							}
						},
						'filter' => false,
						'visible'=> (Yii::app()->request->getQuery('type') == 'mms') ? true : false,
					),
					array(
						'name'  => 'list_id',
						'value' => '$data->list->name',
						'filter'=> false,
					),
					
					array(
						'name' => 'send_at',
						'value' => '$data->getsendAt()',
						'filter'=> false,
					),
					array(
						'name' => 'sent_record',
						'value' => '$data->sent_record',
						'filter'=>	CHtml::activeTextField($smscampaign, 'sent_record', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name' => 'not_sent_record',
						'value' => '$data->not_sent_record',
						'filter'=>	CHtml::activeTextField($smscampaign, 'not_sent_record', array('class'=>'form-control form-filter input-sm')),
					),
					array(
						'name' => 'campaign_status',
						'value' => '$data->campaign_status',
						'filter'=>	CHtml::activeTextField($smscampaign, 'campaign_status', array('class'=>'form-control form-filter input-sm')),
					),
					
					array(
						'class'     => 'CButtonColumn',
						'header'    => Yii::t('app', 'Options'),
						'footer'    => $smscampaign->paginationOptions->getGridFooterPagination(),
						'buttons'   => array(
							 'update' => array(
								'label'    => ' &nbsp; <span class="fa fa-pencil"></span> &nbsp;',
								'visible' => ('$data->campaign_status == "DRAFTS" || $data->campaign_status == "STOP" ? true : false'),
								'url'      => 'Yii::app()->createUrl("sms_campaign/update", array("sms_campaign_id" => $data->sms_campaign_id,"type" => strtolower($data->campaign_type)))',
								'imageUrl' => null,
								'options'  => array('title' => Yii::t('lists', 'View'), 'class' => ''),
							),
							'delete' => array(
								'label'     => ' &nbsp; <span class="fa fa-trash-o"></span> &nbsp; ',
								'url'       => 'Yii::app()->createUrl("sms_campaign/delete", array("id" => $data->sms_campaign_id))',
								'imageUrl'  => null,
								'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
							),
							'stop' => array(
								'label'    => ' &nbsp; <span class="fa fa-stop-circle"></span> &nbsp; ',
								'visible' => '($data->campaign_status == "PROCESSING" || $data->campaign_status == "PENDING" ? true : false)',
								'url'      => 'Yii::app()->createUrl("sms_campaign/stop", array("id" => $data->sms_campaign_id))',
								'imageUrl' => null,
								'options'  => array(
									'title' => Yii::t('lists', 'Status Stop'), 
									'class' => '', 
									'onclick' => 'js:update_status($(this).attr("href"));return false;',
								),
							),
						),
						'htmlOptions' => array(
							'style' => 'width: 180px;',
						),
						//'template'=>' '
						'template'=>'{update} {stop} {delete}',
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
