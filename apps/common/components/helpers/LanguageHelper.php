<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * LanguageHelper
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.1
 */
 
class LanguageHelper 
{

    /**
     * LanguageHelper::getAppLanguageCode()
     * 
     * @return string
     */
    public static function getAppLanguageCode()
    {
        $languageCode = $language = Yii::app()->language;
        if (strpos($language, '_') !== false) {
            $languageAndRegionCode = explode('_', $language);
            list($languageCode, $regionCode) = $languageAndRegionCode;
        }
        return $languageCode;  
    }
}