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

<?php if($result > 0) { ?>
<div class="alert alert-success alert-block">
    Congratulations! Your server configuration satisfies all requirements by MailWizz EMA.
</div>
<?php } elseif($result < 0) { ?>
<div class="alert alert-warning alert-block">
    Your server configuration satisfies the minimum requirements by MailWizz EMA.<br />
    Please pay attention to the warnings listed below if your application will use the corresponding features.
</div>
<?php } else { ?>
<div class="alert alert-danger alert-block">
    Unfortunately your server configuration does not satisfy the requirements by MailWizz EMA.    
</div>
<?php } ?>

<form method="post">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Requirements</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <tr>
                        <th>Name</th>
                        <th>Result</th>
                        <th>Required by</th>
                        <th>Memo</th>
                    </tr>
                    <?php foreach($requirements as $requirement): ?>
                    <tr>
                        <td><?php echo $requirement[0]; ?></td>
                        <td class="<?php echo $requirement[2] ? 'success' : ($requirement[1] ? 'danger' : 'warning'); ?>">
                        <?php echo $requirement[2] ? 'Passed' : ($requirement[1] ? 'Failed' : 'Warning'); ?>
                        </td>
                        <td><?php echo $requirement[3]; ?></td>
                        <td><?php echo $requirement[4]; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="clearfix"><!-- --></div>      
        </div>
        <div class="box-footer">
            <div class="pull-right">
                <button class="btn btn-default btn-submit" data-loading-text="Please wait, processing..." value="<?php echo $result?>" name="result"><?php if ($result != 0) { ?> Next <?php } else { ?> Check again <?php }?></button>
            </div>
            <div class="clearfix"><!-- --></div>        
        </div>
    </div>
</form>