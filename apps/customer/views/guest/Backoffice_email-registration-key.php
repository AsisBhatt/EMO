<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */
$url = Yii::app()->options->get('system.urls.customer_absolute_url') . 'guest/confirm-registration/' . $customer->confirmation_key;
?>
<tr>
	<td align="center">

		<table border="0" cellpadding="0" cellspacing="0" class="brdBottomPadd-two " id="templateContainerMiddle" width="100%">
			<tr valign="top">
				<td align="center" class="bodyContentTicks">
					<table border="0" cellpadding="0" cellspacing="0" width="100%">

						<tr valign="top" align="center">
							<td valign="top" style="color:#505050;font-family:Helvetica;font-size:14px;">
								<h4 style="text-align:center">
									<?php echo Yii::t('customers', 'Please follow the following url in order to confirm your registration:');?>
								</h4>
								<h4 style="text-align:center">
									<a href="<?php echo $url;?>"><?php echo $url;?></a>
								</h4>
							</td>
						</tr>
						<tr valign="top" align="center"> 
							<td valign="top" style="color:#505050;font-family:Helvetica;font-size:14px;">
								<h4 style="text-align:center">
									<?php echo Yii::t('customers', 'If for some reason you cannot click the above url, please paste this one into your browser address bar:')?>
								</h4>
								<h4 style="text-align:center;">
									<?php echo $url;?>
								</h4>
							</td> 
						</tr>
					</table>
				</td>

			</tr>
		</table>
	
	</td>
</tr>
<!-- START CONTENT -->
<?php //echo Yii::t('customers', 'Please follow the following url in order to confirm your registration:');?><br />
<!--<a href="<?php //echo $url;?>"><?php //echo $url;?></a><br /><br />-->
<?php //echo Yii::t('customers', 'If for some reason you cannot click the above url, please paste this one into your browser address bar:')?><br />
<?php //echo $url;?>
<!-- END CONTENT-->