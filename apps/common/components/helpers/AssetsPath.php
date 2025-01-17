<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * AssetsPath
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.1
 */
 
class AssetsPath 
{
    public static function base($path = null, $appName = null)
    {
        if ($appName === null) {
            $appName = Yii::app()->apps->getCurrentAppName();
        }
        
        $base = Yii::getPathOfAlias('root.'.$appName.'.assets');
        $base = $base . '/' . $path;
        
        return str_replace('//', '/', $base);
    }
    
    public static function img($path, $appName = null)
    {
        $folderName = 'img';
        return self::base($folderName.'/'.$path, $appName);
    }
    
    public static function css($path, $appName = null)
    {
        $folderName = 'css';
        return self::base($folderName.'/'.$path, $appName);
    }
    
    public static function js($path, $appName = null)
    {
        $folderName = 'js';
        return self::base($folderName.'/'.$path, $appName);
    }
    
    public static function themeBase($path = null, $appName = null)
    {
        if (!Yii::app()->hasComponent('themeManager') || !Yii::app()->getTheme()) {
            throw new CHttpException(500, __METHOD__ . ' can only be called from within a theme');
        }
        
        if ($appName === null) {
            $appName = Yii::app()->apps->getCurrentAppName();
        }
        
        $name = Yii::app()->getTheme()->getName();
        $base = Yii::getPathOfAlias('root.'.$appName.'.themes.'.$name.'.assets');
        $base = $base . '/' . $path;
        
        return str_replace('//', '/', $base);
    }
    
    public static function themeImg($path, $appName = null)
    {
        $folderName = 'img';
        return self::themeBase($folderName.'/'.$path, $appName);
    }
    
    public static function themeCss($path, $appName = null)
    {
        $folderName = 'css';
        return self::themeBase($folderName.'/'.$path, $appName);
    }
    
    public static function themeJs($path, $appName = null)
    {
        $folderName = 'js';
        return self::themeBase($folderName.'/'.$path, $appName);
    }
}