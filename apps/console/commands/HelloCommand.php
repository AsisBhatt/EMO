<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * HelloCommand
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class HelloCommand extends ConsoleCommand 
{
    public function actionIndex() 
    {
        echo 'Hello World!' . "\n";
    }
}