<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.9
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
			<span class="glyphicon glyphicon-envelope"></span>
			<span class="caption-subject font-dark sbold uppercase">
				<?php echo $pageHeading;?>
			</span>
		</div>
		<div class="actions">
			<div class="btn-group btn-group-devided">
				<?php echo CHtml::link(Yii::t('app', 'Back'), array('smssetting/index'), array('class' => 'btn btn-transparent grey-salsa btn-outline btn-circle btn-sm', 'title' => Yii::t('app', 'Back')));?>
			</div>
		</div>
	</div>
	<div class="portlet-body">
		<div class="table-scrollable">
		<?php
		$this->widget('zii.widgets.CDetailView', array(
			'data'      => $message,
			'cssFile'   => false,
			'htmlOptions' => array(
				'class' => 'table table-bordered table-hover'
			),
			'attributes' => array(
				array(
					'label' => $message->getAttributeLabel('apiurl'),
					'value' => $message->apiurl,
				),
				array(
					'label' => $message->getAttributeLabel('authkey'),
					'value' => $message->authkey,
					'type'  => 'raw',
				),
				array(
					'label' => $message->getAttributeLabel('access_key'),
					'value' => $message->access_key,
					'type'  => 'raw',
				),
				array(
					'label' => $message->getAttributeLabel('sender'),
					'value' => $message->sender,
				),
				array(
					'label' => $message->getAttributeLabel('route'),
					'value' => $message->route,
				),array(
					'label' => $message->getAttributeLabel('country'),
					'value' => $message->country,
				),
				
			),
		));
		?>
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
