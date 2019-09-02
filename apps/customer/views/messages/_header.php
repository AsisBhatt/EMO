<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.9
 */

?>
<?php foreach ($messages as $message) { ?>
<li>
    <a href="<?php echo $this->createUrl('messages/view', array('message_uid' => $message->message_uid));?>">
        <h4>
            <small><i class="fa fa-clock-o"></i> <?php echo $message->dateAdded;?></small>
            <div class="clearfix"><!-- --></div>
            <span><?php echo $message->shortTitle;?></span>
        </h4>
        <p><?php echo wordwrap($message->getShortMessage(90), 45, '<br />', true);?></p>
    </a>
</li>
<?php } ?>
