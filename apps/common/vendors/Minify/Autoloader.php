<?php

/**
 * Minify_Autoloader
 * 
 * The autoloader class for minify classes.
 * This file is not part of minify project, it is here to match mailwizz requirements.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2014 MailWizz EMA (http://www.mailwizz.com)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.2
 */
 
class Minify_Autoloader 
{
    /**
     * Minify_Autoloader::autoloader()
     * 
     * @param string $class
     * @return
     */
    public static function autoloader($class) 
    {
        if (strpos($class, 'Minify_') === 0) {
            $class = str_replace('_', '/', $class);
            if (is_file($classFile = dirname(__FILE__) . '/min/lib/' . $class . '.php')) {
                require_once $classFile;
            }
        }
    }
    
    /**
     * Minify_Autoloader::register()
     * 
     */
    public static function register() 
    {
        spl_autoload_register(array('Minify_Autoloader', 'autoloader'));
    }
}