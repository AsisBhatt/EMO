<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */
 
?>


<div class="price-plan-payment">
	<div class="portlet-title">
		<div class="caption">
			<i class="fa fa-credit-card"></i>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $order->plan->name;?>
			</span>
		</div>
		<div class="actions">
				<?php echo $order->getAttributeLabel('order_uid');?> <b><?php echo $order->uid;?></b>, 
				<?php echo $order->getAttributeLabel('date_added')?>: <?php echo $order->dateAdded;?>
		</div>                            
    </div>

	<div class="portlet-body">
		<div class="row invoice-info margin-bottom-20">
			<div class="col-sm-4 invoice-col">
				<?php echo Yii::t('app', 'From');?>
				<address>
					<?php echo $order->htmlPaymentFrom;?>
				</address>
			</div>
			<div class="col-sm-4 invoice-col">
				<?php echo Yii::t('app', 'To');?>
				<address>
					<?php echo $order->htmlPaymentTo;?>
				</address>
			</div>
			<div class="col-sm-4 invoice-col"></div>
		</div>
	
		<div class="margin-bottom-20">
			<div class="table-scrollable">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th><?php echo Yii::t('orders', 'This order applies for the "{planName}" pricing plan.', array('{planName}' => $order->plan->name));?></th>
						</tr>                                    
					</thead>
					<tbody>
						<tr>
							<td><?php echo $order->plan->description;?></td>
						</tr>
					</tbody>
				</table>                            
			</div>
		</div>
		
		<div class="no-print margin-bottom-20">
			<div class="caption">
				<span class="caption-subject font-dark sbold uppercase lead">
					<?php echo Yii::t('orders', 'Notes');?>:
				</span>
			</div>
			<div class="form-group"> 
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
					), $this),
				), $this));  
				?>    
				</div>
			</div>
		</div>
		
		
		<div class="no-print margin-bottom-20">
			<div class="caption">
				<span class="caption-subject font-dark sbold uppercase lead">
					<?php echo Yii::t('orders', 'Transaction info')?>:
				</span>
			</div>
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
					'id'                => $transaction->modelName.'-grid',
					'dataProvider'      => $transaction->search(),
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
							'name'  => 'payment_gateway_name',
							'value' => '$data->payment_gateway_name',
							'filter'=> false,
						),
						array(
							'name'  => 'payment_gateway_transaction_id',
							'value' => '$data->payment_gateway_transaction_id',
							'filter'=> false,
						),
						array(
							'name'  => 'status',
							'value' => '$data->getStatusName()',
							'filter'=> false,
						),
						array(
							'name'  => 'date_added',
							'value' => '$data->dateAdded',
							'filter'=> false,
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
		
		<div class="margin-bottom-20">
			<div class="caption">
				<span class="caption-subject font-dark sbold uppercase lead">
					<?php echo Yii::t('orders', 'Amount')?>:
				</span>
			</div>
			<div class="table-scrollable">
				<table class="table table-bordered table-hover">
					<tr>
						<th style="width:50%"><?php echo Yii::t('orders', 'Subtotal')?>:</th>
						<td><?php echo $order->formattedSubtotal;?></td>
					</tr>
					<tr>
						<th><?php echo Yii::t('orders', 'Tax')?>:</th>
						<td><?php echo $order->formattedTaxValue;?></td>
					</tr>
					<tr>
						<th><?php echo Yii::t('orders', 'Discount')?>:</th>
						<td><?php echo $order->formattedDiscount;?></td>
					</tr>
					<tr>
						<th><?php echo Yii::t('orders', 'Total')?>:</th>
						<td><?php echo $order->formattedTotal;?></td>
					</tr>
					<tr>
						<th><?php echo Yii::t('orders', 'Status')?>:</th>
						<td><?php echo $order->statusName;?></td>
					</tr>
				</table>
			</div>
		</div>
		
		 <div class="row no-print">
			<div class="col-xs-12 text-right">
				<button class="btn dark" onclick="window.print();"><i class="fa fa-print"></i> <?php echo Yii::t('app', 'Print');?></button>
				<a href="<?php echo $this->createUrl('price_plans/email_invoice', array('order_uid' => $order->order_uid));?>" class="btn dark"><i class="fa fa-envelope"></i> <?php echo Yii::t('orders', 'Email invoice');?></a>
				<a target="_blank" href="<?php echo $this->createUrl('price_plans/order_pdf', array('order_uid' => $order->order_uid));?>" class="btn dark"><i class="fa fa-clipboard"></i> <?php echo Yii::t('orders', 'View invoice');?></a>
				<a href="<?php echo $this->createUrl('price_plans/orders');?>" class="btn green"><?php echo Yii::t('orders', 'Back to orders');?></a>    
			</div>
		</div>
	</div>
</div>
