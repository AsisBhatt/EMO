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
    <div class="box box-primary">
        <div class="box-header">
            <div class="pull-left">
                <h3 class="box-title">
                    <span class="glyphicon glyphicon-credit-card"></span> <?php echo Yii::t('plan', 'Plans');?>
                </h3>
            </div>
            <div class="pull-right">
                <?php //echo HtmlHelper::accessLink(Yii::t('app', 'Create new'), array('plan/create'), array('class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Create new')));?>
                <?php //echo HtmlHelper::accessLink(Yii::t('app', 'Refresh'), array('plan/index'), array('class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Refresh')));?>
            </div>
            <div class="clearfix"><!-- --></div>
        </div>
        <div class="box-body">
		
			<div class="clearfix"><!-- --></div>
			
			<?php foreach($cplans as $key=>$p){ $index= $key+1;?>
			<div class="col-lg-3 col-xs-6">
                <?php if($index%2==0) { ?>
					<div class="small-box bg-maroon">
				<?php }else{ ?>
					<div class="small-box bg-purple">
				<?php }?>
					<!--<div class="small-box bg-green">-->
                    <div class="inner">
                        <h4><b><?php echo $p->name ?></b></h4>
						 <p>Email List : <?php echo $p->list_limit; ?></p>
                         <p>Email: <?php echo $p->email_total; ?></p> 
						 <p>Email for each client is limited to : <?php echo $p->listsend_limit; ?> Per Month</p>
						 <p>Sms : <?php echo $p->sms_total; ?></p> 
						 <p>Validity : <?php echo $p->validity; ?> days</p> 
						 <p>Price : <?php echo $p->price; ?></p> 
                    </div>
                    <div class="icon">
                        <i class="ion ion-ios7-email-outline"></i>
                    </div>
                    <a href="/customer/plan/index" class="small-box-footer">
                        Purchase Now <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
			<?php } ?>
			
			<?php //print_r($cplans->attributes); ?>
			
			   
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