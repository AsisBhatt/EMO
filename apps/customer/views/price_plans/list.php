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
			<span class="glyphicon glyphicon-credit-card"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('price_plans', 'My Plan');?>
			</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<?php 
				foreach ($pricePlans as $plan) { 
					if($plan->group_id == $customer->group_id){
			?>
				<div class="col-lg-6 col-xs-12">
					<div class="panel panel-success panel-template-box">
						<div class="panel-heading clearfix">
							<div class="caption pull-left">
								<span class="caption-subject font-hide bold uppercase">
									<?php echo $plan->name;?>
								</span>
							</div>
							<div class="box-tools pull-right">
								<?php if ($plan->isRecommended) { ?>
								<span class="badge bg-<?php echo $plan->group_id == $customer->group_id ? 'blue' : 'red';?>"><?php echo Yii::t('app', 'Recommended');?></span>
								<?php } ?>
								<span class="badge bg-<?php echo $plan->group_id == $customer->group_id ? 'blue' : 'red';?>"><?php echo $plan->formattedPrice;?></span>
							</div>
						</div>
						<div class="panel-body">
							<p> <?php echo $plan->description;?> </p>
						</div>
						<div class="panel-footer panel-success">					
							<span style="color:red;"><?php echo $plan->group_id == $customer->group_id ? Yii::t('app', 'Your current plan') : '';?></span>
							<!--<a class="btn btn-<?php //echo $plan->group_id == $customer->group_id ? 'primary' : 'success';?> btn-sm btn-do-order" href="#payment-options-modal" data-toggle="modal" data-plan-uid="<?php //echo $plan->uid;?>">
								
							</a>-->
						</div>
					</div>
				</div>
			<?php 
					}
				} 
			?>
		</div>
	</div>
	<div class="portlet-title">
		<div class="caption">
			<span class="glyphicon glyphicon-credit-card"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo Yii::t('price_plans', 'Available Plan');?>
			</span>
		</div>
	</div>
	<div class="portlet-body">
		<div class="row">
			<?php 
				foreach ($pricePlans as $plan) { 
					if($plan->group_id != $customer->group_id){
			?>
				<div class="col-lg-6 col-xs-12">
					<div class="panel panel-primary panel-template-box">
						<div class="panel-heading clearfix">
							<div class="caption pull-left">
								<span class="caption-subject font-hide bold uppercase">
									<?php echo $plan->name;?>
								</span>
							</div>
							<div class="box-tools pull-right">
								<?php if ($plan->isRecommended) { ?>
								<span class="badge bg-<?php echo $plan->group_id == $customer->group_id ? 'blue' : 'red';?>"><?php echo Yii::t('app', 'Recommended');?></span>
								<?php } ?>
								<span class="badge bg-<?php echo $plan->group_id == $customer->group_id ? 'blue' : 'red';?>"><?php echo $plan->formattedPrice;?></span>
							</div>
						</div>
						<div class="panel-body">
							<p> <?php echo $plan->description;?> </p>
						</div>
					</div>
				</div>
			<?php 
					}
				} 
			?>
		</div>
	</div>	
    
    <div class="modal fade" id="payment-options-modal" tabindex="-1" role="dialog" aria-labelledby="payment-options-modal-label" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title"><?php echo Yii::t('price_plans', 'Select payment method');?></h4>
            </div>
            <div class="modal-body">
                <?php 
                echo CHtml::form(array('price_plans/payment'), 'post', array('id' => 'payment-options-form'));
                echo CHtml::hiddenField('plan_uid');
                ?>
                <div class="form-group">
				
					<?php 
						//print_r($paymentMethods); 
						$paymentMethods['edata'] = 'Edata'; 
						//unset($paymentMethods['offline']); 
					?>
				
                    <?php echo CHtml::label(Yii::t('price_plans', 'Payment gateway selection'), 'payment_gateway');?>
                    <?php echo CHtml::dropDownList('payment_gateway', '', $paymentMethods, array('class' => 'form-control')); ?>
                 </div>
                <?php echo CHtml::endForm(); ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn dark btn-outline" data-dismiss="modal"><?php echo Yii::t('app', 'Close');?></button>
              <button type="button" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>" onclick="$('#payment-options-form').submit();"><?php echo Yii::t('price_plans', 'Proceed to payment');?></button>
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