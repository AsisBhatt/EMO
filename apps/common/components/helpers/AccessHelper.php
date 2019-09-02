<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * AccessHelper
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5
 */
 
class AccessHelper
{   
    // shortcut method
    public static function hasRouteAccess($route)
    {
        $app = Yii::app();
        if ($app->apps->isAppName('backend') && $app->hasComponent('user') && $app->user->getId() && $app->user->getModel()) {
            return (bool)$app->user->getModel()->hasRouteAccess($route);
        }
        return true;
    }
}