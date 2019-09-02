<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.4
 */
?>
<?php if (!empty($bulkActions)) { 
    $form = $this->beginWidget('CActiveForm', array(
        'action'      => $formAction,
        'id'          => 'bulk-action-form',
        'htmlOptions' => array('style' => 'display:none'),
    )); 
    $this->endWidget(); 
?>
<div class="row">
	<div class="col-lg-4 margin-top-15" id="bulk-actions-wrapper" style="display: none;">
		<div class="col-lg-10">
			<?php echo CHtml::dropDownList('bulk_action', null, CMap::mergeArray($bulkActions), array(
				'class'           => 'form-control',
				'prompt'		  => 'Please choose',
				'data-delete-msg' => Yii::t('app', 'Are you sure you want to remove the selected items?'),
			));?>
		</div>
		<div class="col-lg-2">
			<a href="javascript:;" class="btn green" id="btn-run-bulk-action" style="display:none"><?php echo Yii::t('app', 'Run bulk action');?></a>
		</div>
	</div>
</div>
<?php } ?>