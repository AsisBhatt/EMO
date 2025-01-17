<?php if ( ! defined('MW_PATH')) exit('No direct script access allowed');

/**
 * FileExtensionMimes
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.2
 */
 
class FileExtensionMimes extends CApplicationComponent
{
    public $alias = '%s.config.mimes';
    
    protected $_mimes;
    
    /**
     * FileExtensionMimes::get()
     * 
     * @param string $extension
     * @return @CMap
     */
    public function get($extension)
    {
        if (!is_array($extension)) {
            $extension = array($extension);
        }
        $mimes = array();
        foreach ($extension as $ext) {
            if (!$this->getMimes()->contains($ext)) {
                $this->getMimes()->add($ext, array());
            }
            $mimes = CMap::mergeArray($mimes, $this->getMimes()->itemAt($ext));    
        }
        return new CMap($mimes);
    }
    
    /**
     * FileExtensionMimes::getMimes()
     * 
     * @return @CMap
     */
    protected function getMimes()
    {
        if ($this->_mimes !== null) {
            return $this->_mimes;
        }

        $fileData = new CMap((array)require(Yii::getPathOfAlias(sprintf($this->alias, 'common')) . '.php'));
        if (is_file($customFile = Yii::getPathOfAlias(sprintf($this->alias, 'common') .'-custom') . '.php')) {
            $fileData->mergeWith((array)require($customFile));
        }
        if (is_file($customFile = Yii::getPathOfAlias(sprintf($this->alias, MW_APP_NAME)) . '.php')) {
            $fileData->mergeWith((array)require($customFile));
        }
        if (is_file($customFile = Yii::getPathOfAlias(sprintf($this->alias, MW_APP_NAME) .'-custom') . '.php')) {
            $fileData->mergeWith((array)require($customFile));
        }
        
        return $this->_mimes = Yii::app()->hooks->applyFilters('file_extensions_mimes_map', $fileData);
    }
    
}