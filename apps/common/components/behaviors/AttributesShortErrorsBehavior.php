<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * AttributesShortErrorsBehavior
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class AttributesShortErrorsBehavior extends CBehavior
{
    /**
     * AttributesShortErrorsBehavior::getAll()
     * 
     * @return array
     */
    public function getAll()
    {
        $_errors = array();
        foreach ($this->owner->getErrors() as $attribute => $errors) {
            if (empty($errors)) {
                continue;
            }
            $_errors[$attribute] = is_array($errors) ? reset($errors) : $errors;
        }
        return $_errors;
    }
    
    /**
     * AttributesShortErrorsBehavior::getAllAsString()
     * 
     * @param string $separator
     * @return string
     */
    public function getAllAsString($separator = '<br />')
    {
        return implode($separator, array_values($this->getAll()));
    }
}