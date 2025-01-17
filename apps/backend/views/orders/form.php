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
if ($viewCollection->renderContent) {
    /**
     * This hook gives a chance to prepend content before the active form or to replace the default active form entirely.
     * Please note that from inside the action callback you can access all the controller view variables 
     * via {@CAttributeCollection $collection->controller->data}
     * In case the form is replaced, make sure to set {@CAttributeCollection $collection->renderForm} to false 
     * in order to stop rendering the default content.
     * @since 1.3.3.1
     */
    $hooks->doAction('before_active_form', $collection = new CAttributeCollection(array(
        'controller'    => $this,
        'renderForm'    => true,
    )));
    
    // and render if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm'); ?>
            <div class="portlet-title">
                <div class="caption">
					<span class="glyphicon glyphicon-credit-card"></span>
					<span class="caption-subject font-dark sbold uppercase">
						<?php echo $pageHeading;?>
					</h3>
                </div>
                <div class="actions">
					<div class="btn-group btn-group-devided">
						<?php if (!$order->isNewRecord) { ?>
						<?php echo CHtml::link(Yii::t('app', 'Create new'), array('orders/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
						<?php } ?>
						<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('orders/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
					</div>
                </div>
            </div>
            <div class="portlet-body">
                <?php 
                /**
                 * This hook gives a chance to prepend content before the active form fields.
                 * Please note that from inside the action callback you can access all the controller view variables 
                 * via {@CAttributeCollection $collection->controller->data}
                 * @since 1.3.3.1
                 */
                $hooks->doAction('before_active_form_fields', new CAttributeCollection(array(
                    'controller'    => $this,
                    'form'          => $form    
                )));
                ?>
				<div class="row">
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'customer_id', array('class' => 'control-label'));?>
						<?php echo $form->hiddenField($order, 'customer_id', $order->getHtmlOptions('customer_id')); ?>
						<?php
						$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
							'name'          => 'customer',
							'value'         => !empty($order->customer_id) ? $order->customer->getFullName() : '',
							'source'        => $this->createUrl('customers/autocomplete'),
							'cssFile'       => false,
							'options'       => array(
								'minLength' => '2',
								'select'    => 'js:function(event, ui) {
									$("#'.CHtml::activeId($order, 'customer_id').'").val(ui.item.customer_id);
								}',
								'search'    => 'js:function(event, ui) {
									$("#'.CHtml::activeId($order, 'customer_id').'").val("");
								}',
								'change'    => 'js:function(event, ui) {
									if (!ui.item) {
										$("#'.CHtml::activeId($order, 'customer_id').'").val("");
									}
								}',
							),
							'htmlOptions'   => $order->getHtmlOptions('customer_id'),
						));
						?>
						<?php echo $form->error($order, 'customer_id');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'plan_id', array('class' => 'control-label'));?>
						<?php echo $form->hiddenField($order, 'plan_id', $order->getHtmlOptions('plan_id')); ?>
						<?php
						$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
							'name'          => 'plan',
							'value'         => !empty($order->plan_id) ? $order->plan->name : '',
							'source'        => $this->createUrl('price_plans/autocomplete'),
							'cssFile'       => false,
							'options'       => array(
								'minLength' => '2',
								'select'    => 'js:function(event, ui) {
									$("#'.CHtml::activeId($order, 'plan_id').'").val(ui.item.plan_id);
								}',
								'search'    => 'js:function(event, ui) {
									$("#'.CHtml::activeId($order, 'plan_id').'").val("");
								}',
								'change'    => 'js:function(event, ui) {
									if (!ui.item) {
										$("#'.CHtml::activeId($order, 'plan_id').'").val("");
									}
								}',
							),
							'htmlOptions'   => $order->getHtmlOptions('plan_id'),
						));
						?>
						<?php echo $form->error($order, 'plan_id');?>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'promo_code_id', array('class' => 'control-label'));?>
						<?php echo $form->hiddenField($order, 'promo_code_id', $order->getHtmlOptions('promo_code_id')); ?>
						<?php
						$this->widget('zii.widgets.jui.CJuiAutoComplete',array(
							'name'          => 'promoCode',
							'value'         => !empty($order->promo_code_id) ? $order->promoCode->code : '',
							'source'        => $this->createUrl('promo_codes/autocomplete'),
							'cssFile'       => false,
							'options'       => array(
								'minLength' => '2',
								'select'    => 'js:function(event, ui) {
									$("#'.CHtml::activeId($order, 'promo_code_id').'").val(ui.item.promo_code_id);
								}',
								'search'    => 'js:function(event, ui) {
									$("#'.CHtml::activeId($order, 'promo_code_id').'").val("");
								}',
								'change'    => 'js:function(event, ui) {
									if (!ui.item) {
										$("#'.CHtml::activeId($order, 'promo_code_id').'").val("");
									}
								}',
							),
							'htmlOptions'   => $order->getHtmlOptions('promo_code_id'),
						));
						?>
						<?php echo $form->error($order, 'promo_code_id');?>
					</div>
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'subtotal', array('class' => 'control-label'));?>
						<?php echo $form->textField($order, 'subtotal', $order->getHtmlOptions('subtotal')); ?>
						<?php echo $form->error($order, 'subtotal');?>
					</div> 
				</div>
				<div class="row">
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'discount', array('class' => 'control-label'));?>
						<?php echo $form->textField($order, 'discount', $order->getHtmlOptions('discount')); ?>
						<?php echo $form->error($order, 'discount');?>
					</div>  
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'total', array('class' => 'control-label'));?>
						<?php echo $form->textField($order, 'total', $order->getHtmlOptions('total')); ?>
						<?php echo $form->error($order, 'total');?>
					</div>  
				</div>
				<div class="row">
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'tax_id', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($order, 'tax_id', CMap::mergeArray(array(''=>'---'), Tax::getAsDropdownOptions()), $order->getHtmlOptions('tax_id')); ?>
						<?php echo $form->error($order, 'tax_id');?>
					</div>  
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'tax_percent', array('class' => 'control-label'));?>
						<?php echo $form->textField($order, 'tax_percent', $order->getHtmlOptions('tax_percent')); ?>
						<?php echo $form->error($order, 'tax_percent');?>
					</div>  
				</div>
				<div class="row">
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'tax_value', array('class' => 'control-label'));?>
						<?php echo $form->textField($order, 'tax_value', $order->getHtmlOptions('tax_value')); ?>
						<?php echo $form->error($order, 'tax_value');?>
					</div>   
					<div class="form-group col-lg-6">
						<?php echo $form->labelEx($order, 'status', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($order, 'status', $order->getStatusesList(), $order->getHtmlOptions('status')); ?>
						<?php echo $form->error($order, 'status');?>
					</div>        
				</div>  
                <div class="row">
					<div class="form-group col-lg-12">
						<?php echo $form->label($note, 'note', array('class' => 'control-label'));?>
						<?php echo $form->textArea($note, 'note', $note->getHtmlOptions('note')); ?>
						<?php echo $form->error($note, 'note');?>
					</div>   
				</div>
                <?php 
                /**
                 * This hook gives a chance to append content after the active form fields.
                 * Please note that from inside the action callback you can access all the controller view variables 
                 * via {@CAttributeCollection $collection->controller->data}
                 * @since 1.3.3.1
                 */
                $hooks->doAction('after_active_form_fields', new CAttributeCollection(array(
                    'controller'    => $this,
                    'form'          => $form    
                )));
                ?>
				<div class="row">
					<div class="form-group col-lg-12"> 
						<div class="table-scrollable">
						<?php 
						$this->widget('zii.widgets.grid.CGridView', $hooks->applyFilters('grid_view_properties', array(
							'ajaxUrl'           => $this->createUrl($this->route, array('id' => (int)$order->order_id)),
							'id'                => $note->modelName.'-grid',
							'dataProvider'      => $note->search(),
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
									'name'  => 'author',
									'value' => '$data->getAuthor()',
								),
								array(
									'name'  => 'note',
									'value' => '$data->note',
								),
								array(
									'name'  => 'date_added',
									'value' => '$data->dateAdded',
								),
								array(
									'class'     => 'CButtonColumn',
									'header'    => Yii::t('app', 'Options'),
									'footer'    => $note->paginationOptions->getGridFooterPagination(),
									'buttons'   => array(
										'delete' => array(
											'label'     => ' &nbsp; <i class="fa fa-trash-o"></i> &nbsp; ', 
											'url'       => 'Yii::app()->createUrl("orders/delete_note", array("id" => $data->note_id))',
											'imageUrl'  => null,
											'options'   => array('title' => Yii::t('app', 'Delete'), 'class' => 'delete'),
										),    
									),
									'htmlOptions' => array(
										'style' => 'width:50px;',
									),
									'template' => '{delete}'
								),
							), $this),
						), $this));  
						?>    
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12">
						<?php if (!$order->isNewRecord) { ?>
						<a href="<?php echo $this->createUrl('orders/view', array('id' => $order->order_id));?>" class="btn green"><?php echo Yii::t('orders', 'View order');?></a>
						<?php } ?>
						<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
					</div>
				</div>
				 <div class="alert alert-success margin-top-20">
					<?php echo Yii::t('orders', 'Please note that any order added/changed from this area is not verified nor it goes through a payment gateway.');?><br />
					<?php echo Yii::t('orders', 'Updating orders from this area is useful for offline orders mostly or for payment corrections.');?><br />
					<?php echo Yii::t('orders', 'If the order is incomplete, pending or due and changed to complete, the customer will be affected and the price plan will be assigned properly.');?><br />
				</div>
            </div>
            
       
        <?php 
        $this->endWidget(); 
    }
    /**
     * This hook gives a chance to append content after the active form.
     * Please note that from inside the action callback you can access all the controller view variables 
     * via {@CAttributeCollection $collection->controller->data}
     * @since 1.3.3.1
     */
    $hooks->doAction('after_active_form', new CAttributeCollection(array(
        'controller'      => $this,
        'renderedForm'    => $collection->renderForm,
    )));
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