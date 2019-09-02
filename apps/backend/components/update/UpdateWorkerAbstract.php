<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * UpdateWorkerAbstract
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.2
 */
 
abstract class UpdateWorkerAbstract extends CApplicationComponent
{
    final public function getDb()
    {
        return Yii::app()->getDb();
    }
    
    final public function getTablePrefix()
    {
        return $this->getDb()->tablePrefix;    
    }
    
    final public function getSqlFilesPath()
    {
        return Yii::getPathOfAlias('common.data.update-sql');
    }
    
    public function runQueriesFromSqlFile($version)
    {
        if (!is_file($sqlFile = $this->sqlFilesPath . '/' . $version . '.sql')) {
            return false;
        }
        
        $queries = (array)CommonHelper::getQueriesFromSqlFile($sqlFile, $this->getTablePrefix());

        foreach ($queries as $query) {
            $this->getDb()->createCommand($query)->execute();
        } 
        
        return true;
    }
    
    abstract public function run();
} 