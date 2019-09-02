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
$type = $_GET['type'];

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
		<script>
			$( document ).ready(function() {
				//var obj_log = $("input[name='SocialmediaApi[socialmedia_logging]']"); 
				$('#SocialmediaApi_socialmedia_logging.form-control').val(''); 
			});
		</script>
        <div class="box box-primary">
            <div class="box-header">
                <div class="pull-left">
                    <h3 class="box-title"><span class="glyphicon glyphicon-credit-card"></span> <?php echo $pageHeading;?></h3>
                </div>
                <!--<div class="pull-right">
                    <?php //if (!$plan->isNewRecord) { ?>
                    <?php //echo CHtml::link(Yii::t('app', 'Create new'), array('plan/create'), array('class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Create new')));?>
                    <?php //} ?>
                    <?php //echo CHtml::link(Yii::t('app', 'Cancel'), array('plan/index'), array('class' => 'btn btn-primary btn-xs', 'title' => Yii::t('app', 'Cancel')));?>
                </div>-->
                <div class="clearfix"><!-- --></div>
            </div>
            <div class="box-body">
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
				<div class="col-lg-12 social_logo">
					<?php
						switch ($type) {
							case "facebook":
								echo '<a class="" href="https://www.facebook.com/login/" target="_blank">';
								break;
							case "twitter":
								echo '<a class="" href="https://twitter.com/login?lang=en" target="_blank">';
								break;
							case "linkedin":
								echo '<a class="" href="https://www.linkedin.com/uas/login" target="_blank">';
								break;
							case "youtube":
								echo '<a class="" href="https://www.youtube.com/" target="_blank" >';
								break;
							case "instagram":
								echo '<a class="" href="https://www.instagram.com/accounts/login/" target="_blank" >';
								break;
							case "google_plus":
								echo '<a class="" href="https://plus.google.com/" target="_blank" >';
								break;
							case "myspace":
								echo '<a class="" href="https://myspace.com/signin" target="_blank" >';
								break;
							case "pinterest":
								echo '<a class="" href="https://www.pinterest.com/login" target="_blank" >';
								break;
							case "tumblr":
								echo '<a class="" href="https://www.tumblr.com/login" target="_blank" >';
								break;
						}
					?>
						<img src="<?php echo Yii::app()->baseUrl.'/assets/img/social logo/'.$type.'.png'; ?>" alt="" />
					</a>
				</div>
				<div class="clearfix"><!-- --></div>
                <div class="clearfix"><!-- --></div>
				
                <div class="form-group col-lg-4">
                    <?php echo $form->labelEx($socialapi, 'socialmedia_fname');?>
                    <?php echo $form->textField($socialapi, 'socialmedia_fname', $socialapi->getHtmlOptions('socialmedia_fname')); ?>
                    <?php echo $form->error($socialapi, 'socialmedia_fname');?>
                </div>
				
				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($socialapi, 'socialmedia_lname');?>
                    <?php echo $form->textField($socialapi, 'socialmedia_lname', $socialapi->getHtmlOptions('socialmedia_lname')); ?>
                    <?php echo $form->error($socialapi, 'socialmedia_lname');?>
                </div>
				
				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($socialapi, 'socialmedia_business_name');?>
                    <?php echo $form->textField($socialapi, 'socialmedia_business_name', $socialapi->getHtmlOptions('socialmedia_business_name')); ?>
                    <?php echo $form->error($socialapi, 'socialmedia_business_name');?>
                </div>

				<div class="clearfix"><!-- --></div>
				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($socialapi, 'socialmedia_mobile_no');?>
                    <?php echo $form->textField($socialapi, 'socialmedia_mobile_no', $socialapi->getHtmlOptions('socialmedia_mobile_no')); ?>
                    <?php echo $form->error($socialapi, 'socialmedia_mobile_no');?>
                </div>

				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($socialapi, 'socialmedia_email');?>
                    <?php echo $form->textField($socialapi, 'socialmedia_email', $socialapi->getHtmlOptions('socialmedia_email')); ?>
                    <?php echo $form->error($socialapi, 'socialmedia_email');?>
                </div>
				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($socialapi, 'socialmedia_gmail');?>
                    <?php echo $form->textField($socialapi, 'socialmedia_gmail', $socialapi->getHtmlOptions('socialmedia_gmail')); ?>
                    <?php echo $form->error($socialapi, 'socialmedia_gmail');?>
                </div>
				
				<div class="clearfix"><!-- --></div>
			
            	<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($socialapi, 'socialmedia_logging');?>
                    <?php echo $form->textField($socialapi, 'socialmedia_logging', $socialapi->getHtmlOptions('socialmedia_logging',array('autocomplete' => 'on'))); ?>
                    <?php echo $form->error($socialapi, 'socialmedia_logging');?>
                </div>
                

				<div class="form-group col-lg-4">
                    <?php echo $form->labelEx($socialapi, 'socialmedia_password');?>
                    <?php echo $form->passwordField($socialapi, 'socialmedia_password', $socialapi->getHtmlOptions('socialmedia_password',array('autocomplete' => 'on'))); ?>
                    <?php echo $form->error($socialapi, 'socialmedia_password');?>
                </div>
                <div class="form-group col-lg-4">
                    <?php echo $form->labelEx($socialapi, 'socialmedia_link');?>
                    <?php echo $form->textField($socialapi, 'socialmedia_link', $socialapi->getHtmlOptions('socialmedia_link')); ?>
                    <?php echo $form->error($socialapi, 'socialmedia_link');?>
                </div>

                <div class="clearfix"><!-- --></div>
				<div class="form-group col-lg-3">
					<?php
						switch ($type) {
							case "facebook":
								echo '<a class="" href="https://www.facebook.com/login/" target="_blank">Go to Facebook Login</a>';
								break;
							case "twitter":
								echo '<a class="" href="https://twitter.com/login?lang=en" target="_blank">Go to Twitter Login</a>';
								break;
							case "linkedin":
								echo '<a class="" href="https://www.linkedin.com/uas/login" target="_blank">Go to Linkedin Login</a>';
								break;
							case "youtube":
								echo '<a class="" href="https://www.youtube.com/" target="_blank" >Go to Youtube Login</a>';
								break;
							case "instagram":
								echo '<a class="" href="https://www.instagram.com/accounts/login/" target="_blank" >Go to Instagram Login</a>';
								break;
							case "google_plus":
								echo '<a class="" href="https://plus.google.com/" target="_blank" >Go to Google Plus Login</a>';
								break;
							case "myspace":
								echo '<a class="" href="https://myspace.com/signin" target="_blank" >Go to Myspace Login</a>';
								break;
							case "pinterest":
								echo '<a class="" href="https://www.pinterest.com/login" target="_blank" >Go to Pinterest Login</a>';
								break;
							case "tumblr":
								echo '<a class="" href="https://www.tumblr.com/login" target="_blank" >Go to Tumblr Login</a>';
								break;
							case "snapchat":
								echo '<a class="" href="https://accounts.snapchat.com/accounts/login" target="_blank" >Go to Snap Chat Login</a>';
								break;
						}
					?>
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
                <div class="clearfix"><!-- --></div>    
            </div>
            <div class="box-footer">
                <div class="pull-right">
                    <button type="submit" class="btn btn-primary btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
                </div>
                <div class="clearfix"><!-- --></div>
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