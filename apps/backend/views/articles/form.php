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
				<span class="glyphicon glyphicon-book"></span>
				<span class="caption-subject font-dark sbold uppercase">
					<?php echo $pageHeading;?>
				</span>
			</div>
			<div class="actions">
				<div class="btn-group btn-group-devided">
					<?php if (!$article->isNewRecord) { ?>
					<?php echo CHtml::link(Yii::t('app', 'Create new'), array('articles/create'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Create new')));?>
					<?php } ?>
					<?php echo CHtml::link(Yii::t('app', 'Cancel'), array('articles/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Cancel')));?>
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
			<div class="form-group">
				<?php echo $form->labelEx($article, 'title', array('class' => 'control-label'));?>
				<?php echo $form->textField($article, 'title', $article->getHtmlOptions('title', array('data-article-id' => (int)$article->article_id, 'data-slug-url' => $this->createUrl('articles/slug')))); ?>
				<?php echo $form->error($article, 'title');?>
			</div>
			<div class="form-group">
				<?php echo $form->labelEx($article, 'content', array('class' => 'control-label'));?>
				<?php echo $form->textArea($article, 'content', $article->getHtmlOptions('content', array('rows' => 15))); ?>
				<?php echo $form->error($article, 'content');?>
			</div>
			<div class="row">
				<div class="form-group col-md-6">
					<?php echo $form->labelEx($articleToCategory, 'category_id', array('class' => 'control-label'));?>
					<div class="form-control">
						<?php echo CHtml::checkBoxList($articleToCategory->modelName, $article->getSelectedCategoriesArray(), $article->getAvailableCategoriesArray(), $articleToCategory->getHtmlOptions('category_id', array(
							'class'        => '',
							'template'     => '{beginLabel}{input} {labelTitle} <span></span>{endLabel}',
							'container'    => '',
							'separator'    => '',
							'labelOptions' => array('class' => 'mt-checkbox mt-checkbox-outline')
						))); ?>
					</div>
					<?php echo $form->error($articleToCategory, 'category_id');?>
				</div>
				<div class="col-md-6">
					<div class="form-group">
						<?php echo $form->labelEx($article, 'status', array('class' => 'control-label'));?>
						<?php echo $form->dropDownList($article, 'status', $article->getStatusesArray(), $article->getHtmlOptions('status')); ?>
						<?php echo $form->error($article, 'status');?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<div class="form-group slug-wrapper"<?php if (empty($article->slug)){ echo ' style="display:none"';}?>>
						<?php echo $form->labelEx($article, 'slug', array('class' => 'control-label'));?>
						<?php echo $form->textField($article, 'slug', $article->getHtmlOptions('slug')); ?>
						<?php echo $form->error($article, 'slug');?>
					</div>	
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