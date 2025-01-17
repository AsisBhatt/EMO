<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * NetDnsHelper
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.3.1
 */

class NetDnsHelper
{
    /**
     * NetDnsHelper::isIpBlacklistedAtDnsbls()
     *
     * @param mixed $ipAddress
     * @param mixed $dnsBls
     * @return mixed
     */
    public static function isIpBlacklistedAtDnsbls($ipAddress, array $dnsBls = array())
    {
        static $checkdnsrrExists;
        static $execExists;
        static $digLookupExists;
        static $checked = array();

        if (isset($checked[$ipAddress])) {
            return $checked[$ipAddress];
        }

        $blackListed = false;

        if (!FilterVarHelper::ip($ipAddress)) {
            if (($_ipAddress = self::getHostByName($ipAddress)) == $ipAddress) {
                return $checked[$ipAddress] = $blackListed;
            }
            $ipAddress = $_ipAddress;
        }

        if ($checkdnsrrExists === null) {
            $checkdnsrrExists = CommonHelper::functionExists('checkdnsrr');
        }

        if ($execExists === null) {
            $execExists = CommonHelper::functionExists('exec');
        }

        if (!$checkdnsrrExists && !$execExists) {
            return $checked[$ipAddress] = $blackListed;
        }

        if (empty($dnsBls)) {
            $dnsBls = (array)Yii::app()->options->get('system.email_blacklist.remote_dnsbls', array());
            if (empty($dnsBls)) {
                return $checked[$ipAddress] = $blackListed;
            }
        }

        shuffle($dnsBls);

        $ipBlocks  = explode('.', $ipAddress);
        $reverseIp = implode('.', array_reverse($ipBlocks));

        if ($execExists) {
            if ($digLookupExists !== false) {
                if ($digLookupExists === null) {
                    exec('command -v dig >/dev/null 2>&1', $lines, $status);
                    $digLookupExists = $status == 0;
                }
                if ($digLookupExists) {
                    foreach ($dnsBls as $host) {
                        exec(sprintf('dig %s +nocomments +noquestion +noauthority +noadditional +nostats', escapeshellarg($reverseIp.'.'.$host.'.')), $lines, $status);
                        foreach ($lines as $line) {
                            if (strpos($line, $reverseIp.'.'.$host) === 0) {
                                return $checked[$ipAddress] = $host;
                            }
                        }
                    }
                }
            }
        }

        if ($checkdnsrrExists && !$digLookupExists) {
            foreach ($dnsBls as $host) {
                if (@checkdnsrr($reverseIp.'.'.$host.'.', 'A')) {
                    return $checked[$ipAddress] = $host;
                }
            }
        }

        return $checked[$ipAddress] = $blackListed;
    }

    /**
     * NetDnsHelper::getHostByName()
     *
     * @param mixed $hostname
     * @return string
     */
    public static function getHostByName($hostname)
    {
        static $gethostbynameExists;
        static $execExists;
        static $digExists;
        static $checked = array();

        if (isset($checked[$hostname])) {
            return $checked[$hostname];
        }

        if ($gethostbynameExists === null) {
            $gethostbynameExists = CommonHelper::functionExists('gethostbyname');
        }

        if ($execExists === null) {
            $execExists = CommonHelper::functionExists('exec');
        }

        if (!$gethostbynameExists && !$execExists) {
            return $checked[$hostname] = $hostname;
        }

        if ($execExists) {
            if ($digExists !== false) {
                if ($digExists === null) {
                    exec('command -v dig >/dev/null 2>&1', $lines, $status);
                    $digExists = $status == 0;
                }
                if ($digExists) {
                    exec(sprintf('dig +short %s', escapeshellarg($hostname)), $lines, $status);
                    foreach ($lines as $line) {
                        if (FilterVarHelper::ip($line)) {
                            return $checked[$hostname] = $line;
                        }
                    }
                }
            }
        }

        if ($gethostbynameExists && !$digExists) {
            if (($ip = @gethostbyname($hostname)) != $hostname) {
                return $checked[$hostname] = $ip;
            }
        }

        return $checked[$hostname] = $hostname;
    }
}
