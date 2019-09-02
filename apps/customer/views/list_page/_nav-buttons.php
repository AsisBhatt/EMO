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
		<div class="btn-group">
			<button type="button" class="btn green btn-circle btn-sm dropdown-toggle" data-toggle="dropdown">
				<?php echo str_repeat(10) . Yii::t('list_pages', 'Select another list page to edit') . str_repeat(10)?> <span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<?php foreach ($pageTypes as $pType) { ?>
				<li><a href="<?php echo $this->createUrl($this->route, array('list_uid' => $list->list_uid, 'type' => $pType->slug));?>"><?php echo Yii::t('list_pages', $pType->name);?></a></li>
				<?php } ?>
			</ul>
		</div>    
	</div>
	<hr class="no-margin">