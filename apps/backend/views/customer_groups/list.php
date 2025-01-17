<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false 
 * in order to stop rendering the default content.
 * @since 1.3.4.3
 */
$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) { ?>
        <div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-folder-close"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			
            <div class="actions">
				<div class="btn-group btn-group-devided">
					<?php echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('customer_groups/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
					<?php echo HtmlHelper::accessLink(Yii::t('customers', 'Manage customers'), array('customers/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('customers', 'Manage customers')));?>
					<?php echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('customer_groups/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Refresh')));?>
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
             * @since 1.3.4.3
             */
            $hooks->doAction('before_grid_view', $collection = new CAttributeCollection(array(
                'controller'    => $this,
                'renderGrid'    => true,
            )));
            
            // and render if allowed
            if ($collection->renderGrid) {
                $this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
                    'ajaxUrl'           => $this->createUrl($this->route),
                    'id'                => $group->modelName.'-grid',
                    'dataProvider'      => $group->search(),
                    'filter'            => $group,
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
                            'value' => 'CHtml::link($data->name, Yii::app()->createUrl("customer_groups/update", array("id" => $data->group_id)))',
                            'type'  => 'raw',
							'filter'=>	CHtml::activeTextField($group, 'name', array('class'=>'form-control form-filter input-sm')),
                        ),
                        array(
                            'name'  => 'customersCount',
                            'value' => '$data->customersCount',
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
                            'footer'    => $group->paginationOptions->getGridFooterPagination(),
                            'buttons'   => array(
                                'copy'=> array(
                                    'label'     => ' &nbsp; <span class="glyphicon glyphicon-subtitles"></span> &nbsp;', 
                                    'url'       => 'Yii::app()->createUrl("customer_groups/copy", array("id" => $data->group_id))',
                                    'imageUrl'  => null,
                                    'options'   => array('title' => Yii::t('app', 'Copy'), 'class' => 'copy-group'),
                                    'visible'   => 'AccessHelper::hasRouteAccess("customer_groups/copy")',
                                ),
                                'reset_quota' => array(
                                    'label'     => ' &nbsp; <i class="fa fa-refresh"></i> &nbsp;', 
                                    'url'       => 'Yii::app()->createUrl("customer_groups/reset_sending_quota", array("id" => $data->group_id))',
                                    'imageUrl'  => null,
                                    'options'   => array('title' => Yii::t('app', 'Reset sending quota'), 'class' => 'reset-sending-quota', 'data-message' => Yii::t('customers', 'Are you sure you want to reset the sending quota for this group?')),
                                    'visible'   => 'AccessHelper::hasRouteAccess("customer_groups/reset_sending_quota")',
                                ),
                                'update' => array(
                                    'label'     => ' &nbsp; <i class="fa fa-pencil"></i> &nbsp;', 
                                    'url'       => 'Yii::app()->createUrl("customer_groups/update", array("id" => $data->group_id))',
                                    'imageUrl'  => null,
                                    'options'   => array('title' => Yii::t('app', 'Update'), 'class' => ''),
                                    'visible'   => 'AccessHelper::hasRouteAccess("customer_groups/update")',
                                ),
                                'delete' => array(
                                    'label'     => ' <i class="fa fa-trash-o"></i> ', 
                                    'url'       => 'Yii::app()->createUrl("customer_groups/delete", array("id" => $data->group_id))',
                                    'imageUrl'  => null,
                                    'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
                                    'visible'   => 'AccessHelper::hasRouteAccess("customer_groups/delete")',
                                ),    
                            ),
                            'htmlOptions' => array(
                                'style' => 'width:130px;',
                            ),
                            'template' => '{copy} {reset_quota} {update} {delete}'
                        ),
                    ), $this),
                ), $this));  
            }
            /**
             * This hook gives a chance to append content after the grid view content.
             * Please note that from inside the action callback you can access all the controller view
             * variables via {@CAttributeCollection $collection->controller->data}
             * @since 1.3.4.3
             */
            $hooks->doAction('after_grid_view', new CAttributeCollection(array(
                'controller'    => $this,
                'renderedGrid'  => $collection->renderGrid,
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
 * @since 1.3.4.3
 */
$hooks->doAction('after_view_file_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));