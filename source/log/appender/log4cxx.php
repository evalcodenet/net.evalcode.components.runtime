<?php


namespace Components;


  /**
   * Log_Appender_Log4cxx
   *
   * @package net.evalcode.components
   * @subpackage log.appender
   *
   * @author evalcode.net
   */
  abstract class Log_Appender_Log4cxx extends Log_Appender_Abstract
  {
    // PREDEFINED PROPERTIES
    const CONFIG_FILE='log4j.properties';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    public static function isSupported()
    {
      return @class_exists('\\LoggerManager');
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::append()
     */
    public function append($level_, array $args_=array())
    {
      $this->m_logger->{self::$m_mapLevelToName[$level_]}(
        '['.array_shift($args_).'] '.array_shift($args_), $args_
      );
    }

    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::initialize()
     */
    public function initialize()
    {
      if($this->m_initialized)
        return;

      \LoggerPropertyConfigurator::configure($this->getConfigurationFile());

      $this->m_logger=$this->getLoggerImpl(
        str_replace(' ', '', lcfirst(ucwords(strtr(trim(get_class($this)), '_', ' '))))
      );

      $this->level=self::$m_mapNameToLevel[strtolower($this->m_logger->getEffectiveLevel())];

      $this->m_initialized=true;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    protected $m_initialized=false;
    /**
     * @var \Logger $logger
     */
    protected $m_logger;
    //-----


    /**
     * @param string $namespace_
     *
     * @return Logger
     */
    protected function getLoggerImpl($namespace_)
    {
      return \LoggerManager::getLogger($namespace_);
    }

    /**
     * @return string
     */
    protected function getConfigurationFile()
    {
      if(is_file($file=Environment::pathConfigLocal().'/'.self::CONFIG_FILE))
        return $file;

      return Environment::pathConfigGlobal().'/'.self::CONFIG_FILE;
    }
    //--------------------------------------------------------------------------
  }
?>
