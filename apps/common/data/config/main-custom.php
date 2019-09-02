<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Custom application main configuration file
 * 
 * This file can be used to overload config/components/etc
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2014 MailWizz EMA (http://www.mailwizz.com)
 * @license http://www.unikainfocom.in/support
 * @since 1.1
 */
    
return array(

    // application components
    'components' => array(
        'db' => array(
            'connectionString'  => '{DB_CONNECTION_STRING}',
            'username'          => '{DB_USER}',
            'password'          => '{DB_PASS}',
            'tablePrefix'       => '{DB_PREFIX}',
        ),
    ),
);