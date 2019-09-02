<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * HTMLPurifier_URIFilter_HostCustomFieldTagPost
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.1
 */

class HTMLPurifier_URIFilter_HostCustomFieldTagPost extends HTMLPurifier_URIFilter
{
    public $name = 'HostCustomFieldTagPost';

    public $post = true;

    public function filter(&$uri, $config, $context)
    {
        return true;
    }
}