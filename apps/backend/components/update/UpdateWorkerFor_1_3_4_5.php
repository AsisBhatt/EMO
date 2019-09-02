<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * UpdateWorkerFor_1_3_4_5
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.5
 */
 
class UpdateWorkerFor_1_3_4_5 extends UpdateWorkerAbstract
{
    public function run()
    {
        // run the sql from file
        $this->runQueriesFromSqlFile('1.3.4.5');
        
        // add a note about the new cron job
        $phpCli = CommonHelper::findPhpCliPath();
        $notify = Yii::app()->notify;
        $notify->addInfo(Yii::t('update', 'Version {version} brings a new cron job that you have to add to run once at 2 minutes. After addition, it must look like: {cron}', array(
            '{version}' => '1.3.4.5',
            '{cron}'    => sprintf('<br /><strong>*/2 * * * * %s -q ' . MW_ROOT_PATH . '/apps/console/console.php send-transactional-emails > /dev/null 2>&1</strong>', $phpCli),
        )));
    }
} 