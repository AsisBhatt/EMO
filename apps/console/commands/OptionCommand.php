<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * OptionCommand
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4
 */
 
class OptionCommand extends ConsoleCommand 
{
    public function actionGet_option($name, $default = null)
    {
        exit((string)Yii::app()->options->get($name, $default));
    }
    
    public function actionSet_option($name, $value)
    {
        Yii::app()->options->set($name, $value);
    }
}