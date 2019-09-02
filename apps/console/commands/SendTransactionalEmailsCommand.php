<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * SendTransactionalEmailsCommand
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.5
 */
 
class SendTransactionalEmailsCommand extends ConsoleCommand 
{
    public function actionIndex() 
    {
        $mutex    = Yii::app()->mutex;
        $lockName = md5(__FILE__ . __CLASS__);
        
        if (!$mutex->acquire($lockName)) {
            return 1;
        }
        
        // added in 1.3.4.7
        Yii::app()->hooks->doAction('console_command_transactional_emails_before_process', $this);
        
        $this->process();
        
        // added in 1.3.4.7
        Yii::app()->hooks->doAction('console_command_transactional_emails_after_process', $this);
        
        $mutex->release($lockName);
        return 0;
    }
    
    protected function process()
    {
        $emails = TransactionalEmail::model()->findAll(array(
            'condition' => '`status` = "unsent" AND `send_at` < NOW() AND `retries` < `max_retries`',
            'order'     => 'email_id ASC',
            'limit'     => 100,
        ));
        
        if (empty($emails)) {
            return $this;
        }
        
        foreach ($emails as $email) {
            $email->send();
        }

        Yii::app()->getDb()->createCommand('UPDATE {{transactional_email}} SET `status` = "sent" WHERE `status` = "unsent" AND send_at < NOW() AND retries >= max_retries')->execute();
        Yii::app()->getDb()->createCommand('DELETE FROM {{transactional_email}} WHERE `status` = "unsent" AND send_at < NOW() AND date_added < DATE_SUB(NOW(), INTERVAL 1 MONTH)')->execute();
        
        return $this;
    }
}