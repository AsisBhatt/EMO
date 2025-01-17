<?php if ( ! defined('MW_PATH')) exit('No direct script access allowed');

/**
 * Mailer
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.2
 */
 
class Mailer extends CApplicationComponent
{
    const DEFAULT_MAILER = 'SwiftMailer';
    
    // holds the active mailer name
    protected $_activeMailer;
    
    // holds CMap of available mailers and their configuration/instances
    protected $_mailers;

    /**
     * Mailer::send()
     * 
     * @param array $params
     * @return bool
     */
    public function send(array $params = array())
    {
        return $this->getMailer()->send($params);
    }
    
    /**
     * Mailer::getEmailMessage()
     * 
     * @param array $params
     * @return string
     */
    public function getEmailMessage(array $params = array())
    {
        return $this->getMailer()->getEmailMessage($params);
    }
    
    /**
     * Mailer::reset()
     * 
     */
    public function reset($clearLogs = true)
    {
        return $this->getMailer()->reset($clearLogs);
    }
    
    /**
     * Mailer::getName()
     * 
     * @return string
     */
    public function getName()
    {
        return $this->getMailer()->getName();
    }
    
    /**
     * Mailer::getMailerDescription()
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->getMailer()->getDescription();
    }
    
    /**
     * Mailer::getEmailMessageId()
     * 
     * @return string
     */
    public function getEmailMessageId()
    {
        return $this->getMailer()->getEmailMessageId();
    }
    
    /**
     * Mailer::addLog()
     * 
     * @param mixed $log
     * @return
     */
    public function addLog($log)
    {
        $this->getMailer()->addLog($log);
        return $this;
    }
    
    /**
     * Mailer::getLogs()
     * 
     * @param bool $clear
     * @return array
     */
    public function getLogs($clear = true)
    {
        return $this->getMailer()->getLogs($clear);
    }
    
    /**
     * Mailer::getLog()
     * 
     * @param string $glue
     * @param bool $clear
     * @return string
     */
    public function getLog($glue = "\n", $clear = true)
    {
        return $this->getMailer()->getLog($glue, $clear);
    }
    
    /**
     * Mailer::clearLogs()
     * 
     */
    public function clearLogs()
    {
        return $this->getMailer()->clearLogs();
    }

    /**
     * Mailer::setActiveMailer()
     * 
     * @param string $mailer
     * @return Mailer
     */
    public function setActiveMailer($mailer)
    {    
        $this->_activeMailer = $mailer;
        return $this;
    }
    
    /**
     * Mailer::getActiveMailer()
     * 
     * @return string
     */
    public function getActiveMailer()
    {
        if (empty($this->_activeMailer) || !$this->getMailers()->contains($this->_activeMailer)) {
            $this->_activeMailer = $this->getDefaultMailer();
        }
        return $this->_activeMailer;
    }

    /**
     * Mailer::getAllInstances()
     * 
     * @return CMap
     */
    public function getAllInstances()
    {
        foreach ($this->getMailers() as $key => $value) {
            $this->instance($key);
        }
        return $this->getMailers();
    }
    
    /**
     * Mailer::findEmailAndName()
     * 
     * @return array
     */
    public function findEmailAndName($data)
    {
        return $this->getMailer()->findEmailAndName($data);
    }
    
    /**
     * Mailer::getMailer()
     * 
     * @return MailerAbstract
     */
    protected function getMailer()
    {
        return $this->instance($this->getActiveMailer());
    }

    /**
     * Mailer::instance()
     * 
     * @param string $mailerName
     * @return mixed
     */
    protected function instance($mailerName)
    {
        $mailer = $this->getMailers()->itemAt($mailerName);
        if (is_array($mailer)) {
            $mailer = Yii::createComponent($mailer);
            $mailer->init();
            $this->getMailers()->add($mailerName, $mailer);
        }
        if (!$mailer) {
            $this->getMailers()->remove($mailerName);
        }
        return $mailer;
    }
    
    /**
     * Mailer::getDefaultMailer()
     * 
     * @return string
     */
    protected function getDefaultMailer()
    {
        $defaultMailer = Yii::app()->options->get('system.common.default_mailer', self::DEFAULT_MAILER);
        if (!$this->getMailers()->contains($defaultMailer)) {
            if ($this->getMailers()->getCount() > 0) {
                foreach ($this->getMailers()->toArray() as $name => $value) {
                    $defaultMailer = $name;
                    break;
                }
            } else {
                $defaultMailer = self::DEFAULT_MAILER;
            }
        }
        return $defaultMailer;
    }
    
    /**
     * Mailer::getMailers()
     * 
     * @return CMap
     */
    protected function getMailers()
    {
        if ($this->_mailers !== null && $this->_mailers instanceof CMap) {
            return $this->_mailers;
        }    
        $mailers = array(
            'SwiftMailer' => array(
                'class'   => 'common.components.mailer.MailerSwiftMailer',
            ),
            'PHPMailer'   => array(
                'class'   => 'common.components.mailer.MailerPHPMailer',
            ),
            'DummyMailer' => array(
                'class'   => 'common.components.mailer.MailerDummyMailer',
            ),
        );
        $mailers = (array)Yii::app()->hooks->applyFilters('mailer_get_mailers_list', $mailers);
        return $this->_mailers = new CMap($mailers);
    }
    
}