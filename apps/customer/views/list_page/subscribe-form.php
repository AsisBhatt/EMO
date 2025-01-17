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
    $this->renderPartial('_nav-buttons');
    ?>
    <div class="alert alert-success margin-bottom-20">
        <label><?php echo Yii::t('list_pages', 'Your subscribe form url is:');?></label><br />
		<div class="row">
			<div class="col-lg-10">
				<input type="text" value="<?php echo Yii::app()->apps->getAppUrl('frontend', 'lists/'.$list->list_uid.'/subscribe', true);?>" class="form-control"/>
			</div>
			<div class="col-lg-2 text-right">
				<a class="btn green btn-flat" href="<?php echo Yii::app()->apps->getAppUrl('frontend', 'lists/'.$list->list_uid.'/subscribe', true);?>" target="_blank"><?php echo Yii::t('list_pages', 'Preview it now!');?></a>
			</div>
		</div>        
    </div>
    <?php 
    $this->renderPartial('_form');
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