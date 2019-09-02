<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3
 */
 
?>

<div class="portlet-title">
	<div class="caption">
		<span class="glyphicon glyphicon-file"></span>
		<span class="caption-subject font-dark sbold uppercase">
			 <?php echo $pageHeading;?>
		</span>
	</div>
	<div class="actions">
		<div class="btn-group btn-group-devided">
			<?php echo CHtml::form();?>
			<button type="submit" name="delete" value="1" class="btn btn-danger btn-outline btn-circle delete-app-log" data-message="<?php echo Yii::t('app', 'Are you sure you want to remove the application log?')?>"><?php echo Yii::t('app', 'Delete');?></button>
			<?php echo CHtml::endForm();?>
		</div>
	</div>	
</div>
<div class="portlet-body">
	<textarea class="form-control" rows="30"><?php echo $applicationLog;?></textarea>  
</div>
