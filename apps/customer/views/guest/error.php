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


<div class="error-massage text-center">
    <div class="number font-red">
		<?php echo Yii::t('app', 'Error {code}!', array('{code}' => (int)$code));?>
	</div>
	<div class="details">
		<div class="box-body">
			<p class="info"><?php echo CHtml::encode($message);?></p>
		</div>
	</div>
	<a href="javascript:history.back(-1);" class="btn btn-default"> <i class="glyphicon glyphicon-circle-arrow-left"></i> <?php echo Yii::t('app', 'Back')?></a>
</div>