<?php


namespace Components;


  /**
   * Log_Appender_Log4cxx
   *
   * @api
   * @package net.evalcode.components.log
   * @subpackage appender
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


    // OVERRIDES
    /**
     * @see \Components\Log_Appender::append() append
     */
    public function append($level_, array $args_=[])
    {
      $this->m_logger->{self::$m_mapLevelToName[$level_]}(
        '['.array_shift($args_).'] '.array_shift($args_), $args_
      );
    }

    /**
     * @see \Components\Log_Appender::initialize() initialize
     */
    public function initialize()
    {
      if(false===$this->m_initialized)
      {
        parent::initialize();

        \LoggerPropertyConfigurator::configure($this->getConfigurationFile());

        $this->m_logger=$this->getLoggerImpl(\str\underscoreToCamelCase(__CLASS__));
        $this->level=self::$m_mapNameToLevel[strtolower($this->m_logger->getEffectiveLevel())];

        $this->m_initialized=true;
      }
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
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
