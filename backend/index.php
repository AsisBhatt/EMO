<?php

/**
 * Backend application bootstrap file
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
// define the type of application we are creating.
define('MW_APP_NAME', 'backend');

// and start an instance of it.
require_once(dirname(__FILE__) . '/../apps/init.php');