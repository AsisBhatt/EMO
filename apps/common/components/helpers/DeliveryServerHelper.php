<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * DeliveryServerHelper
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.6.3
 */
class DeliveryServerHelper
{
    /**
     * @param $str
     * @return string
     */
    public static function getOptionCustomerCustomHeadersStringFromString($str)
    {
        $_headers = explode("\n", $str);
        $headers  = array();
        $prefix   = Yii::app()->params['email.custom.header.prefix'];

        foreach ($_headers as $header) {
            if (strpos($header, ':') === false) {
                continue;
            }

            list($name, $value) = explode(':', $header);

            if (stripos($name, 'x-') !== 0 || stripos($name, $prefix) === 0) {
                continue;
            }

            $headers[] = sprintf('%s:%s', $name, trim($value));
        }

        return implode("\n", $headers);
    }

    /**
     * @param $str
     * @return array
     */
    public static function getOptionCustomerCustomHeadersArrayFromString($str)
    {
        if (empty($str)) {
            return array();
        }

        $headers = array();
        $lines   = explode("\n", self::getOptionCustomerCustomHeadersStringFromString($str));
        foreach ($lines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }
            list($name, $value) = explode(':', $line);
            $headers[] = array('name' => $name, 'value' => $value);
        }

        return $headers;
    }
}