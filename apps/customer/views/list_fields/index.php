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
 
?>
<div class="margin">
    <?php $this->widget('customer.components.web.widgets.MailListSubNavWidget', array(
        'list' => $list,
    ))?>
</div>
<hr class="no-margin">
<?php $hooks->doAction('customer_controller_list_fields_before_form');?>
<?php echo CHtml::form();?>

<div class="portlet-title">
	<div class="caption">
		<span class="glyphicon glyphicon-tasks"></span>
		<span class="caption-subject font-dark sbold uppercase">
			<?php echo $pageHeading;?>
		</span>
	</div>
</div>
<div class="portlet-body">
	<div class="list-fields">
		<?php echo $fieldsHtml; ?>
	</div>
	<div class="list-fields-buttons">
		<?php $hooks->doAction('customer_controller_list_fields_render_buttons');?>
	</div>
	<div class="row">
		<div class="col-md-12">
			<button type="submit" class="btn green btn-submit margin-top-20" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('app', 'Save changes');?></button>
		</div>
	</div>
</div>
<?php echo CHtml::endForm();?>
<?php $hooks->doAction('customer_controller_list_fields_after_form');?>