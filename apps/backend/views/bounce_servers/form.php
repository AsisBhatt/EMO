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
        $form = $this->beginWidget('CActiveForm');
        ?>
		<div class="portlet-title">
			<div class="caption">
				<span class="glyphicon glyphicon-filter"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('bounce_servers/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
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
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'hostname', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'hostname', $server->getHtmlOptions('hostname')); ?>
					<?php echo $form->error($server, 'hostname');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'username', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'username', $server->getHtmlOptions('username')); ?>
					<?php echo $form->error($server, 'username');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'password', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'password', $server->getHtmlOptions('password', array('value' => ''))); ?>
					<?php echo $form->error($server, 'password');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'email', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'email', $server->getHtmlOptions('email')); ?>
					<?php echo $form->error($server, 'email');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'service', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'service', $server->getServicesArray(), $server->getHtmlOptions('service')); ?>
					<?php echo $form->error($server, 'service');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'port', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'port', $server->getHtmlOptions('port')); ?>
					<?php echo $form->error($server, 'port');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'protocol', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'protocol', $server->getProtocolsArray(), $server->getHtmlOptions('protocol')); ?>
					<?php echo $form->error($server, 'protocol');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'validate_ssl', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'validate_ssl', $server->getValidateSslOptions(), $server->getHtmlOptions('validate_ssl')); ?>
					<?php echo $form->error($server, 'validate_ssl');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'search_charset', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'search_charset', $server->getHtmlOptions('search_charset')); ?>
					<?php echo $form->error($server, 'search_charset');?>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'disable_authenticator', array('class' => 'control-label'));?>
					<?php echo $form->textField($server, 'disable_authenticator', $server->getHtmlOptions('disable_authenticator')); ?>
					<?php echo $form->error($server, 'disable_authenticator');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($server, 'delete_all_messages', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($server, 'delete_all_messages', $server->getYesNoOptions(), $server->getHtmlOptions('delete_all_messages')); ?>
					<?php echo $form->error($server, 'delete_all_messages');?>
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
				<div class="col-lg-12">
					<?php $this->renderPartial('//delivery_servers/_customer', compact('form'));?> 
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
				</div>
			</div>
			<div class="alert alert-success margin-top-20">
				<?php 
				$text = 'Please note that the server settings will be checked when you save the server and the save process will be denied if there are any connection errors.<br />
				Also, this is a good chance to see how long it takes from the moment you hit the save button till the moment the changes are saved  because this is the same amount of time it will take the script to connect to the server and retrieve the bounced emails.<br />
				Some of the servers, like gmail for example, are very slow if you use a hostname(i.e: imap.gmail.com). If that\'s the case, then simply instead of the hostname, use the IP address.<br />
				You can use a service like <a target="_blank" href="http://www.hcidata.info/host2ip.htm">hcidata.info</a> to find out the IP address of any hostname.';
				echo Yii::t('servers', StringHelper::normalizeTranslationString($text));
				?>
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
    ?>
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