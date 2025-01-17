<?php defined('MW_PATH') || exit('No direct script access allowed');
/**
 * DateTimeFormatterBehavior
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class DateTimeFormatterBehavior extends CActiveRecordBehavior
{
    // the table column for date added
    public $dateAddedAttribute = 'date_added';
    
    // the table column for last updated
    public $lastUpdatedAttribute = 'last_updated';
    
    // the timezone
    private $_timeZone = null;

    /**
     * DateTimeFormatterBehavior::getDateAdded()
     * 
     * This will format the date added attribute depending on the locale and timezone.
     * 
     * @return string
     */
    public function getDateAdded()
    {
        $dateAdded = $this->owner->hasAttribute($this->dateAddedAttribute) ? $this->owner->getAttribute($this->dateAddedAttribute) : null;
        return $this->formatLocalizedDateTime($dateAdded);
    }
    
    /**
     * DateTimeFormatterBehavior::getLastUpdated()
     * 
     * This will format the date added attribute depending on the locale and timezone.
     * 
     * @return string
     */
    public function getLastUpdated()
    {
        $lastUpdated = $this->owner->hasAttribute($this->lastUpdatedAttribute) ? $this->owner->getAttribute($this->lastUpdatedAttribute) : null;
        return $this->formatLocalizedDateTime($lastUpdated);
    }
    
    /**
     * DateTimeFormatterBehavior::formatLocalizedDateTime()
     * 
     * @param mixed $dateTimeValue
     * @param mixed $inFormat
     * @param mixed $dateWidth
     * @patam mixed $timeWidth
     * @return string
     */
    public function formatLocalizedDateTime($dateTimeValue = null, $inFormat = null, $dateWidth = null, $timeWidth = null)
    {
        $dateWidth = ($dateWidth === null) ? 'short' : $dateWidth;
        $timeWidth = ($timeWidth === null) ? 'short' : $timeWidth;
        return Yii::app()->dateFormatter->formatDateTime($this->convertDateTime($dateTimeValue, $inFormat), $dateWidth, $timeWidth);
    }

    /**
     * DateTimeFormatterBehavior::formatLocalizedDate()
     * 
     * @param string $dateValue
     * @param string $inFormat
     * @param mixed $dateWidth
     * @return string
     */
    public function formatLocalizedDate($dateValue = null, $inFormat = null, $dateWidth = null)
    {
        $dateWidth = ($dateWidth === null) ? 'short' : $dateWidth;
        return Yii::app()->dateFormatter->formatDateTime($this->convertDate($dateValue, $inFormat), $dateWidth, null);
    }
    
    /**
     * DateTimeFormatterBehavior::formatTimeValueLocale()
     * 
     * @param mixed $dateTimeValue
     * @param mixed $inFormat
     * @param mixed $timeWidth
     * @return string
     */
    public function formatLocalizedTime($dateTimeValue = null, $inFormat = null, $timeWidth = null)
    {
        $timeWidth = ($timeWidth === null) ? 'short' : $timeWidth;
        return Yii::app()->dateFormatter->formatDateTime($this->convertDateTime($dateTimeValue, $inFormat), null, $timeWidth);
    }
    
    /**
     * DateTimeFormatterBehavior::formatDateTime()
     * 
     * @param mixed $dateTimeValue
     * @param mixed $inFormat
     * @param mixed $outFormat
     * @return string
     */
    public function formatDateTime($dateTimeValue = null, $inFormat = null, $outFormat = null)
    {
        $outFormat  = ($outFormat === null)  ? 'yyyy-MM-dd HH:mm:ss'  : $outFormat;
		//var_dump($outFormat);exit;
		$test = Yii::app()->dateFormatter->format($outFormat, $this->convertDateTime($dateTimeValue, $inFormat));
        return Yii::app()->dateFormatter->format($outFormat, $this->convertDateTime($dateTimeValue, $inFormat));
    }

    /**
     * DateTimeFormatterBehavior::convertDateTime()
     * 
     * @param mixed $utcDateTimeValue
     * @param mixed $inFormat 
     * @param mixed $outFormat
     * @return string
     */
    public function convertDateTime($utcDateTimeValue = null, $inFormat = null,  $outFormat = null)
    {
        $utcDateTimeValue  = ($utcDateTimeValue === null)       ? date('Y-m-d H:i:s')    : $utcDateTimeValue;
        $utcDateTimeValue  = ($utcDateTimeValue === 'NOW()')    ? date('Y-m-d H:i:s')    : $utcDateTimeValue;
        $inFormat          = ($inFormat === null)               ? 'yyyy-MM-dd HH:mm:ss'  : $inFormat;
        $outFormat         = ($outFormat === null)              ? 'yyyy-MM-dd HH:mm:ss'  : $outFormat;
        $dateFormatter     = Yii::app()->dateFormatter;
        $utcDateTimeValue  = $dateFormatter->format('yyyy-MM-dd HH:mm:ss', CDateTimeParser::parse($utcDateTimeValue, $inFormat));
        
        if (($this->getTimeZone())) {
            $dateTime = new DateTime($utcDateTimeValue);
            $dateTime->setTimezone(new DateTimeZone($this->getTimeZone()));
            $utcDateTimeValue = $dateTime->format('Y-m-d H:i:s');
        }
        
        return $dateFormatter->format($outFormat, $utcDateTimeValue);
    }
    
    /**
     * DateTimeFormatterBehavior::convertDate()
     * 
     * @param mixed $utcDateValue
     * @param mixed $inFormat 
     * @param mixed $outFormat
     * @return string
     */
    public function convertDate($utcDateValue = null, $inFormat = null, $outFormat = null)
    {
        $utcDateValue  = ($utcDateValue === null)       ? date('Y-m-d') : $utcDateValue;
        $utcDateValue  = ($utcDateValue === 'NOW()')    ? date('Y-m-d') : $utcDateValue;
        $inFormat      = ($inFormat === null)           ? 'yyyy-MM-dd'  : $inFormat;
        $outFormat     = ($outFormat === null)          ? 'yyyy-MM-dd'  : $outFormat;
        
        return $this->convertDateTime($utcDateValue, $inFormat, $outFormat);
    }
    
    /**
     * DateTimeFormatterBehavior::getTimeZone()
     * 
     * @return string
     */
    public function getTimeZone()
    {
        if ($this->_timeZone !== null) {
            return $this->_timeZone;
        }
        
        if (Yii::app()->hasComponent('user') && Yii::app()->user->getId() > 0 && !$this->owner->isNewRecord) {
            $this->_timeZone = Yii::app()->user->getModel()->timezone;
        }
        
        if (Yii::app()->hasComponent('customer') && Yii::app()->customer->getId() > 0 && !$this->owner->isNewRecord) {
            $this->_timeZone = Yii::app()->customer->getModel()->timezone;
        }
        
        return $this->_timeZone;
    }
    
    /**
     * DateTimeFormatterBehavior::setTimeZone()
     * 
     * @param mixed $value
     * @return DateTimeFormatterBehavior
     */
    public function setTimeZone($value)
    {
        $this->_timeZone = $value;
        return $this;
    }
    
}