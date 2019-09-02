<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5
 */
 
?>
<div class="portlet-title">
	<div class="caption">
		<span class="glyphicon glyphicon-file"></span>
		<span class="caption-subject font-dark sbold uppercase">
			<?php echo $pageHeading;?>
		</span>
	</div>
</div>
<div class="portlet-body">
	<iframe src="<?php echo $this->createUrl($this->route, array('show' => 1));?>" width="100%" height="700" frameborder="0"></iframe>
</div>