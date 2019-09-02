<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * UpdateWorkerFor_1_3_6_3
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.3
 */

class UpdateWorkerFor_1_3_6_3 extends UpdateWorkerAbstract
{
    public function run()
    {
        // run the sql from file
        $this->runQueriesFromSqlFile('1.3.6.3');
        
        // enable the tour extension
        if (Yii::app()->extensionsManager->enableExtension('tour')) {
            Yii::app()->extensionsManager->getExtensionInstance('tour')->setOption('enabled', 'yes');
        }
    }
}
