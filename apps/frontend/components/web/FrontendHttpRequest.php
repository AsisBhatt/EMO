<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * FrontendHttpRequest
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.2
 */

class FrontendHttpRequest extends BaseHttpRequest
{
    /**
     * FrontendHttpRequest::checkCurrentRoute()
     *
     * @return bool
     */
    protected function checkCurrentRoute()
    {
        if (stripos($this->pathInfo, 'webhook') !== false) {
            return false;
        }
        return parent::checkCurrentRoute();
    }

}