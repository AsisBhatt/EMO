<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * ClearCacheCommand
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.6
 *
 */

class ClearCacheCommand extends ConsoleCommand
{
    // enable verbose mode
    public $verbose = 1;
    
    /**
     * @return int
     */
    public function actionIndex()
    {
        Yii::app()->hooks->doAction('console_command_clear_cache_before_process', $this);

        $result = $this->process();

        Yii::app()->hooks->doAction('console_command_clear_cache_after_process', $this);

        return $result;
    }

    /**
     * @return int
     */
    protected function process()
    {
        $this->stdout(FileSystemHelper::clearCache());
        return 0;
    }
}
