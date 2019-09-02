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
    if(!$template->isNewRecord) { ?>
    <?php 
    }
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
				<span class="glyphicon glyphicon-text-width"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<a href="javascript:;" onclick="window.open('<?php echo $previewUrl;?>', '<?php echo Yii::t('email_templates',  'Preview');?>', 'height=600, width=600'); return false;" class="btn green"><?php echo Yii::t('email_templates',  'Preview');?></a>
					<a data-toggle="modal" href="#template-test-email" class="btn green"><?php echo Yii::t('email_templates',  'Send a test email using this template');?></a>
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
					<?php echo $form->labelEx($template, 'name', array('class' => 'control-label'));?>
					<?php echo $form->textField($template, 'name', $template->getHtmlOptions('name')); ?>
					<?php echo $form->error($template, 'name');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($template, 'inline_css', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($template, 'inline_css', $template->getInlineCssArray(), $template->getHtmlOptions('inline_css')); ?>
					<?php echo $form->error($template, 'inline_css');?>
				</div>
				<div class="form-group col-lg-4">
					<?php echo $form->labelEx($template, 'minify', array('class' => 'control-label'));?>
					<?php echo $form->dropDownList($template, 'minify', $template->getYesNoOptions(), $template->getHtmlOptions('minify')); ?>
					<?php echo $form->error($template, 'minify');?>
				</div>
			</div>
			<div class="form-group">
				<div class="pull-left">
					<?php echo $form->labelEx($template, 'content', array('class' => 'control-label'));?>
					<?php 
					// since 1.3.5
					$hooks->doAction('before_wysiwyg_editor_left_side', array('controller' => $this, 'template' => $template));
					?>
				</div>
				<div class="pull-right">
					<?php 
					// since 1.3.5
					$hooks->doAction('before_wysiwyg_editor_right_side', array('controller' => $this, 'template' => $template));
					?>
				</div>
				<div class="clearfix"><!-- --></div>
				<?php echo $form->textArea($template, 'content', $template->getHtmlOptions('content', array('rows' => 15))); ?>
				<?php echo $form->error($template, 'content');?>
			</div>
			<div class="clearfix"><!-- --></div>
			<?php 
			/**
			 * This hook gives a chance to append content after the active form fields.
			 * Please note that from inside the action callback you can access all the controller view variables 
			 * via {@CAttributeCollection $collection->controller->data}
			 * 
			 * @since 1.3.3.1
			 */
			$hooks->doAction('after_active_form_fields', new CAttributeCollection(array(
				'controller'    => $this,
				'form'          => $form    
			))); 
			?>
			<div class="row">
				<div class="col-md-12">
					<button type="submit" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
				</div>
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
    <?php if(!$template->isNewRecord) { ?>
    <div class="modal fade" id="template-test-email" tabindex="-1" role="dialog" aria-labelledby="template-test-email-label" aria-hidden="true">
        <div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
				  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				  <h4 class="modal-title"><?php echo Yii::t('email_templates',  'Send a test email');?></h4>
				</div>
				<div class="modal-body">
					 <div class="alert alert-success margin-bottom-20">
						 <strong><?php echo Yii::t('app', 'Notes');?>: </strong><br />
						<?php 
						$text = '
						* if multiple recipients, separate the email addresses by a comma.<br />
						* the email tags will not be parsed while sending test emails.<br />
						* make sure you save the template changes before you send the test.';
						echo Yii::t('email_templates',  StringHelper::normalizeTranslationString($text));
						?>
					 </div>
					 <?php echo CHtml::form(array('templates/test', 'template_uid' => $template->template_uid), 'post', array('id' => 'template-test-form'));?>
					 <div class="form-group">
						 <?php echo CHtml::label(Yii::t('templates', 'Recipient(s)'), 'email');?>
						 <?php echo CHtml::textField('email', null, array('class' => 'form-control', 'placeholder' => Yii::t('templates', 'i.e: a@domain.com, b@domain.com, c@domain.com')));?>
					 </div>
					 <div class="clearfix"><!-- --></div>
					 <div class="form-group">
						 <?php echo CHtml::label(Yii::t('templates', 'From email (optional)'), 'from_email');?>
						 <?php echo CHtml::textField('from_email', null, array('class' => 'form-control', 'placeholder' => Yii::t('templates', 'i.e: me@domain.com')));?>
					 </div>
					 <?php CHtml::endForm();?>
				</div>
				<div class="modal-footer">
				  <button type="button" class="btn dark btn-outline" data-dismiss="modal"><?php echo Yii::t('app', 'Close');?></button>
				  <button type="button" class="btn green btn-submit" onclick="$('#template-test-form').submit();"><?php echo Yii::t('email_templates',  'Send test');?></button>
				</div>
			</div>
        </div>
    </div>
    <?php } ?>
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