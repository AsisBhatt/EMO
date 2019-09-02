<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.7
 */
 
?>

<div class="portlet-title">
	<div class="caption">
		<span class="caption-subject font-dark sbold uppercase">
			<?php 
			if ($domain->isVerified) {
				echo Yii::t('sending_domains', 'This domain has been verified');
			} else {
				echo Yii::t('sending_domains', 'Verify this domain');
			}
			?>
		</span>
	</div>
</div>
<div class="portlet-body">
	<div class="alert alert-success">
		<p>
		<?php echo Yii::t('sending_domains', 'Please edit your DNS records for {domain} domain and add the following TXT record: ', array('{domain}' => '<strong>' . $domain->name . '</strong>' ));?>
		<textarea class="form-control" rows="5"><?php echo $domain->getDnsTxtDkimSelectorToAdd();?></textarea>
		<br />
		<?php echo Yii::t('sending_domains', 'For best delivery rates, your domain SPF record must look like:');?><br />
		<textarea class="form-control" rows="3"><?php echo $domain->getDnsTxtSpfRecordToAdd();?></textarea><br />
		<?php if (!$domain->isVerified) { ?>
		<?php echo Yii::t('sending_domains', 'After you have added the DNS records for your domain, please click the Verify DNS records button below to verify your domain.');?><br />
		<?php echo Yii::t('sending_domains', 'Please note that it can take up to 48 hours for DNS changes to propagate. If verification fails now, please try again later.');?><br />
		<?php } ?>
		</p>
	</div>       
	<?php if (!$domain->isVerified) { ?>
		<div class="row">
			<div class="col-md-12">
				<a href="<?php echo $this->createUrl('sending_domains/verify', array('id' => $domain->domain_id));?>" class="btn green btn-submit" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('sending_domains', 'Verify DNS records');?></a>
			</div>
		</div>
	<?php } ?>
</div>
