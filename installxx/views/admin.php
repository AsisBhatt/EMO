<?php defined('MW_INSTALLER_PATH') || exit('No direct script access allowed');

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
<form action="" method="post">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Admin credentials</h3>
        </div>
        <div class="box-body">
            <div class="form-group col-lg-6">
                <label class="required">First name <span class="required">*</span></label>
                <input class="form-control has-help-text<?php echo $context->getError('first_name') ? ' error':'';?>" name="first_name" type="text" value="<?php echo getPost('first_name', '');?>"/>
                <?php if ($error = $context->getError('first_name')) { ?>
                <div class="errorMessage" style="display: block;"><?php echo $error;?></div>
                <?php } ?>
            </div>
            <div class="form-group col-lg-6">
                <label class="required">Last name <span class="required">*</span></label>
                <input class="form-control has-help-text<?php echo $context->getError('last_name') ? ' error':'';?>" name="last_name" type="text" value="<?php echo getPost('last_name', '');?>"/>
                <?php if ($error = $context->getError('last_name')) { ?>
                <div class="errorMessage" style="display: block;"><?php echo $error;?></div>
                <?php } ?>
            </div>
            <div class="form-group col-lg-6">
                <label class="required">Email <span class="required">*</span></label>
                <input class="form-control has-help-text<?php echo $context->getError('email') ? ' error':'';?>" name="email" type="text" value="<?php echo getPost('email', '');?>"/>
                <?php if ($error = $context->getError('email')) { ?>
                <div class="errorMessage" style="display: block;"><?php echo $error;?></div>
                <?php } ?>
            </div>
            <div class="form-group col-lg-6">
                <label class="required">Password <span class="required">*</span></label>
                <input class="form-control has-help-text<?php echo $context->getError('password') ? ' error':'';?>" name="password" type="text" value="<?php echo getPost('password', '');?>"/>
                <?php if ($error = $context->getError('password')) { ?>
                <div class="errorMessage" style="display: block;"><?php echo $error;?></div>
                <?php } ?>
            </div>
            <div class="form-group col-lg-6">
                <label class="required">Timezone <span class="required">*</span></label>
                <select class="form-control has-help-text<?php echo $context->getError('timezone') ? ' error':'';?>" name="timezone">
                    <?php foreach ($timezones as $key => $value) {?>
                    <option value="<?php echo $key;?>"<?php echo getPost('timezone') == $key ? ' selected=""selected':'';?>><?php echo $value;?></option>
                    <?php } ?>
                </select>
                <?php if ($error = $context->getError('timezone')) { ?>
                <div class="errorMessage" style="display: block;"><?php echo $error;?></div>
                <?php } ?>
            </div>
            <div class="form-group col-lg-6">
                <label class="required">Also create first customer with same data <span class="required">*</span></label>
                <select class="form-control has-help-text<?php echo $context->getError('create_customer') ? ' error':'';?>" name="create_customer">
                    <?php foreach (array('yes' => 'Yes', 'no' => 'No') as $key => $value) {?>
                    <option value="<?php echo $key;?>"<?php echo getPost('create_customer') == $key ? ' selected=""selected':'';?>><?php echo $value;?></option>
                    <?php } ?>
                </select>
                <?php if ($error = $context->getError('create_customer')) { ?>
                <div class="errorMessage" style="display: block;"><?php echo $error;?></div>
                <?php } ?>
            </div>
            <div class="clearfix"><!-- --></div>      
        </div>
        <div class="box-footer">
            <div class="pull-right">
                <button type="submit" name="next" value="1" class="btn btn-default btn-submit" data-loading-text="Please wait, processing...">Create account</button>
            </div>
            <div class="clearfix"><!-- --></div>
        </div>
    </div>
</form>