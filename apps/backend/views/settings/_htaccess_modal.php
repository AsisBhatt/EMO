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
<div>
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title"><?php echo Yii::t('settings', 'Htaccess contents')?></h4>
    </div>
    <div class="modal-body">
        <div class="modal-message"></div>
        <?php echo CHtml::textArea('htaccess', $this->getHtaccessContent(), array('rows' => 10, 'id' => 'htaccess', 'class' => 'form-control'));?>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Close');?></button>
      <button type="button" class="btn btn-primary btn-submit btn-write-htaccess" data-remote="<?php echo $this->createUrl('settings/write_htaccess');?>" data-loading-text="<?php echo Yii::t('app', 'Please wait, processing...');?>"><?php echo Yii::t('settings', 'Write htaccess');?></button>
    </div>
</div>