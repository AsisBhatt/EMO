<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * DateTimeHelper
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class DateTimeHelper
{
    /**
     * DateTimeHelper::getTimeZones()
     *
     * @return array
     */
    public static function getTimeZones()
    {
        return self::getSystemInternalTimeZones();
    }

    /**
     * DateTimeHelper::getSystemInternalTimeZones()
     *
     * @link http://davidhancock.co/2013/05/generating-a-list-of-timezones-with-php/
     * @return array
     */
    public static function getSystemInternalTimeZones()
    {
        static $timezoneList = array();

        if (!empty($timezoneList)) {
            return $timezoneList;
        }

        $timezoneIdentifiers = DateTimeZone::listIdentifiers();
		
        if (empty($timezoneIdentifiers)) {
            return self::getStaticTimeZones();
        }

        $utcTime = new DateTime('now', new DateTimeZone('UTC'));
        $tempTimezones = array();
		
        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new DateTimeZone($timezoneIdentifier);

            $tempTimezones[] = array(
                'offset' => (int)$currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier
            );
        }

        // Sort the array by offset,identifier ascending
        usort($tempTimezones, array('DateTimeHelper', '_sortTempIdentifiers'));

        $timezoneList = array();
        foreach ($tempTimezones as $tz) {
            $sign = $tz['offset'] > 0 ? '+' : '-';
            $offset = gmdate('H:i', abs($tz['offset']));
            $timezoneList[$tz['identifier']] = '(GMT' . $sign . $offset . ') ' . $tz['identifier'];
        }

        // since 1.3.5.9
        if (class_exists('Yii', false)) {
            $timezoneList = Yii::app()->hooks->applyFilters('timezones_list', $timezoneList);
        }

        return $timezoneList;
    }

    /**
     * DateTimeHelper::_sortTempIdentifiers()
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    public static function _sortTempIdentifiers($a, $b)
    {
        return ($a['offset'] == $b['offset']) ? strcmp($a['identifier'], $b['identifier']) : $a['offset'] - $b['offset'];
    }

    /**
     * DateTimeHelper::getStaticTimeZones()
     *
     * @link http://www.ultramegatech.com/2009/04/working-with-time-zones-in-php/
     * @return array
     */
    public static function getStaticTimeZones()
    {
        $timezoneList = array(
			'America/New_York' => '(GMT-05:00) Eastern Time (US & Canada)',
			'America/Chicago' => '(GMT-06:00) Central Time (US & Canada)',
			'America/Denver' => '(GMT-07:00) Mountain Time (US & Canada)',
			'America/Los_Angeles' => '(GMT-08:00) Pacific Time (US & Canada)',
            'Kwajalein' => '(GMT-12:00) International Date Line West',
            'Pacific/Midway' => '(GMT-11:00) Midway Island',
            'Pacific/Samoa' => '(GMT-11:00) Samoa',
            'Pacific/Honolulu' => '(GMT-10:00) Hawaii',
            'America/Anchorage' => '(GMT-09:00) Alaska',
            'America/Tijuana' => '(GMT-08:00) Tijuana, Baja California',
            'America/Chihuahua' => '(GMT-07:00) Chihuahua',
            'America/Mazatlan' => '(GMT-07:00) Mazatlan',
            'America/Phoenix' => '(GMT-07:00) Arizona',
            'America/Regina' => '(GMT-06:00) Saskatchewan',
            'America/Tegucigalpa' => '(GMT-06:00) Central America',
            'America/Mexico_City' => '(GMT-06:00) Mexico City',
            'America/Monterrey' => '(GMT-06:00) Monterrey',
            'America/Bogota' => '(GMT-05:00) Bogota',
            'America/Lima' => '(GMT-05:00) Lima',
            'America/Rio_Branco' => '(GMT-05:00) Rio Branco',
            'America/Indiana/Indianapolis' => '(GMT-05:00) Indiana (East)',
            'America/Caracas' => '(GMT-04:30) Caracas',
            'America/Halifax' => '(GMT-04:00) Atlantic Time (Canada)',
            'America/Manaus' => '(GMT-04:00) Manaus',
            'America/Santiago' => '(GMT-04:00) Santiago',
            'America/La_Paz' => '(GMT-04:00) La Paz',
            'America/St_Johns' => '(GMT-03:30) Newfoundland',
            'America/Argentina/Buenos_Aires' => '(GMT-03:00) Georgetown',
            'America/Sao_Paulo' => '(GMT-03:00) Brasilia',
            'America/Godthab' => '(GMT-03:00) Greenland',
            'America/Montevideo' => '(GMT-03:00) Montevideo',
            'Atlantic/South_Georgia' => '(GMT-02:00) Mid-Atlantic',
            'Atlantic/Azores' => '(GMT-01:00) Azores',
            'Atlantic/Cape_Verde' => '(GMT-01:00) Cape Verde Is.',
            'Europe/Dublin' => '(GMT) Dublin',
            'Europe/Lisbon' => '(GMT) Lisbon',
            'Europe/London' => '(GMT) London',
            'Africa/Monrovia' => '(GMT) Monrovia',
            'Atlantic/Reykjavik' => '(GMT) Reykjavik',
            'Africa/Casablanca' => '(GMT) Casablanca',
            'Europe/Belgrade' => '(GMT+01:00) Belgrade',
            'Europe/Bratislava' => '(GMT+01:00) Bratislava',
            'Europe/Budapest' => '(GMT+01:00) Budapest',
            'Europe/Ljubljana' => '(GMT+01:00) Ljubljana',
            'Europe/Prague' => '(GMT+01:00) Prague',
            'Europe/Sarajevo' => '(GMT+01:00) Sarajevo',
            'Europe/Skopje' => '(GMT+01:00) Skopje',
            'Europe/Warsaw' => '(GMT+01:00) Warsaw',
            'Europe/Zagreb' => '(GMT+01:00) Zagreb',
            'Europe/Brussels' => '(GMT+01:00) Brussels',
            'Europe/Copenhagen' => '(GMT+01:00) Copenhagen',
            'Europe/Madrid' => '(GMT+01:00) Madrid',
            'Europe/Paris' => '(GMT+01:00) Paris',
            'Africa/Algiers' => '(GMT+01:00) West Central Africa',
            'Europe/Amsterdam' => '(GMT+01:00) Amsterdam',
            'Europe/Berlin' => '(GMT+01:00) Berlin',
            'Europe/Rome' => '(GMT+01:00) Rome',
            'Europe/Stockholm' => '(GMT+01:00) Stockholm',
            'Europe/Vienna' => '(GMT+01:00) Vienna',
            'Europe/Minsk' => '(GMT+02:00) Minsk',
            'Africa/Cairo' => '(GMT+02:00) Cairo',
            'Europe/Helsinki' => '(GMT+02:00) Helsinki',
            'Europe/Riga' => '(GMT+02:00) Riga',
            'Europe/Sofia' => '(GMT+02:00) Sofia',
            'Europe/Tallinn' => '(GMT+02:00) Tallinn',
            'Europe/Vilnius' => '(GMT+02:00) Vilnius',
            'Europe/Athens' => '(GMT+02:00) Athens',
            'Europe/Bucharest' => '(GMT+02:00) Bucharest',
            'Europe/Istanbul' => '(GMT+02:00) Istanbul',
            'Asia/Jerusalem' => '(GMT+02:00) Jerusalem',
            'Asia/Amman' => '(GMT+02:00) Amman',
            'Asia/Beirut' => '(GMT+02:00) Beirut',
            'Africa/Windhoek' => '(GMT+02:00) Windhoek',
            'Africa/Harare' => '(GMT+02:00) Harare',
            'Asia/Kuwait' => '(GMT+03:00) Kuwait',
            'Asia/Riyadh' => '(GMT+03:00) Riyadh',
            'Asia/Baghdad' => '(GMT+03:00) Baghdad',
            'Africa/Nairobi' => '(GMT+03:00) Nairobi',
            'Asia/Tbilisi' => '(GMT+03:00) Tbilisi',
            'Europe/Moscow' => '(GMT+03:00) Moscow',
            'Europe/Volgograd' => '(GMT+03:00) Volgograd',
            'Asia/Tehran' => '(GMT+03:30) Tehran',
            'Asia/Muscat' => '(GMT+04:00) Muscat',
            'Asia/Baku' => '(GMT+04:00) Baku',
            'Asia/Yerevan' => '(GMT+04:00) Yerevan',
            'Asia/Yekaterinburg' => '(GMT+05:00) Ekaterinburg',
            'Asia/Karachi' => '(GMT+05:00) Karachi',
            'Asia/Tashkent' => '(GMT+05:00) Tashkent',
            'Asia/Kolkata' => '(GMT+05:30) Calcutta',
            'Asia/Colombo' => '(GMT+05:30) Sri Jayawardenepura',
            'Asia/Katmandu' => '(GMT+05:45) Kathmandu',
            'Asia/Dhaka' => '(GMT+06:00) Dhaka',
            'Asia/Almaty' => '(GMT+06:00) Almaty',
            'Asia/Novosibirsk' => '(GMT+06:00) Novosibirsk',
            'Asia/Rangoon' => '(GMT+06:30) Yangon (Rangoon)',
            'Asia/Krasnoyarsk' => '(GMT+07:00) Krasnoyarsk',
            'Asia/Bangkok' => '(GMT+07:00) Bangkok',
            'Asia/Jakarta' => '(GMT+07:00) Jakarta',
            'Asia/Brunei' => '(GMT+08:00) Beijing',
            'Asia/Chongqing' => '(GMT+08:00) Chongqing',
            'Asia/Hong_Kong' => '(GMT+08:00) Hong Kong',
            'Asia/Urumqi' => '(GMT+08:00) Urumqi',
            'Asia/Irkutsk' => '(GMT+08:00) Irkutsk',
            'Asia/Ulaanbaatar' => '(GMT+08:00) Ulaan Bataar',
            'Asia/Kuala_Lumpur' => '(GMT+08:00) Kuala Lumpur',
            'Asia/Singapore' => '(GMT+08:00) Singapore',
            'Asia/Taipei' => '(GMT+08:00) Taipei',
            'Australia/Perth' => '(GMT+08:00) Perth',
            'Asia/Seoul' => '(GMT+09:00) Seoul',
            'Asia/Tokyo' => '(GMT+09:00) Tokyo',
            'Asia/Yakutsk' => '(GMT+09:00) Yakutsk',
            'Australia/Darwin' => '(GMT+09:30) Darwin',
            'Australia/Adelaide' => '(GMT+09:30) Adelaide',
            'Australia/Canberra' => '(GMT+10:00) Canberra',
            'Australia/Melbourne' => '(GMT+10:00) Melbourne',
            'Australia/Sydney' => '(GMT+10:00) Sydney',
            'Australia/Brisbane' => '(GMT+10:00) Brisbane',
            'Australia/Hobart' => '(GMT+10:00) Hobart',
            'Asia/Vladivostok' => '(GMT+10:00) Vladivostok',
            'Pacific/Guam' => '(GMT+10:00) Guam',
            'Pacific/Port_Moresby' => '(GMT+10:00) Port Moresby',
            'Asia/Magadan' => '(GMT+11:00) Magadan',
            'Pacific/Fiji' => '(GMT+12:00) Fiji',
            'Asia/Kamchatka' => '(GMT+12:00) Kamchatka',
            'Pacific/Auckland' => '(GMT+12:00) Auckland',
            'Pacific/Tongatapu' => '(GMT+13:00) Nukualofa'
        );

        // since 1.3.5.9
        if (class_exists('Yii', false)) {
            $timezoneList = Yii::app()->hooks->applyFilters('timezones_list', $timezoneList);
        }
        
        return $timezoneList;
    }

    /**
     * DateTimeHelper::timespan()
     *
     * Based on CodeIgniter's date helper timespan function
     * http://ellislab.com/codeigniter/user-guide/helpers/date_helper.html
     *
     * @param integer $seconds
     * @param integer $time
     * @return string
     */
    public static function timespan($seconds = 1, $time = null)
	{
		if (!is_numeric($seconds)) {
			$seconds = 1;
		}

		if (empty($time) || !is_numeric($time)) {
			$time = time();
		}

		if ($time <= $seconds) {
			$seconds = 1;
		} else {
			$seconds = $time - $seconds;
		}

		$str = '';
		$years = floor($seconds / 31536000);

		if ($years > 0) {
			$str .= Yii::t('app', '{n} year|{n} years', $years).', ';
		}

		$seconds -= $years * 31536000;
		$months = floor($seconds / 2628000);

		if ($years > 0 || $months > 0) {
			if ($months > 0) {
				$str .= Yii::t('app', '{n} month|{n} months', $months).', ';
			}

			$seconds -= $months * 2628000;
		}

		$weeks = floor($seconds / 604800);

		if ($years > 0 || $months > 0 || $weeks > 0) {
			if ($weeks > 0) {
				$str .= Yii::t('app', '{n} week|{n} weeks', $weeks).', ';
			}

			$seconds -= $weeks * 604800;
		}

		$days = floor($seconds / 86400);

		if ($months > 0 || $weeks > 0 || $days > 0) {
			if ($days > 0) {
				$str .= Yii::t('app', '{n} day|{n} days', $days).', ';
			}

			$seconds -= $days * 86400;
		}

		$hours = floor($seconds / 3600);

		if ($days > 0 || $hours > 0) {
			if ($hours > 0) {
				$str .= Yii::t('app', '{n} hour|{n} hours', $hours).', ';
			}

			$seconds -= $hours * 3600;
		}

		$minutes = floor($seconds / 60);

		if ($days > 0 || $hours > 0 || $minutes > 0) {
			if ($minutes > 0) {
				$str .= Yii::t('app', '{n} minute|{n} minutes', $minutes).', ';
			}

			$seconds -= $minutes * 60;
		}

		if ($str == '') {
			$str .= Yii::t('app', '{n} second|{n} seconds', $seconds).', ';
		}

		return substr(trim($str), 0, -1);
	}
}
