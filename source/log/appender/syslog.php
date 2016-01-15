<?php


namespace Components;


  /**
   * Log_Appender_Syslog
   *
   * @api
   * @package net.evalcode.components.log
   * @subpackage appender
   *
   * @author evalcode.net
   */
  class Log_Appender_Syslog extends Log_Appender_Abstract
  {
    // PREDEFINED PROPERTIES
    const PATTERN_SYSLOG_DEBUG="[%L] [%B] [%n] %m (%r)";
    const PATTERN_SYSLOG_DEFAULT="%b %c [%n] %m (%r)";

    const FACILITY_AUTH=LOG_AUTH;
    const FACILITY_CRON=LOG_CRON;
    const FACILITY_USER=LOG_USER;
    const FACILITY_DAEMON=LOG_DAEMON;
    const FACILITY_SYSLOG=LOG_SYSLOG;
    const FACILITY_LOCAL0=LOG_LOCAL0;
    const FACILITY_LOCAL1=LOG_LOCAL1;
    const FACILITY_LOCAL2=LOG_LOCAL2;
    const FACILITY_LOCAL3=LOG_LOCAL3;
    const FACILITY_LOCAL4=LOG_LOCAL4;
    const FACILITY_LOCAL5=LOG_LOCAL5;
    const FACILITY_LOCAL6=LOG_LOCAL6;
    const FACILITY_LOCAL7=LOG_LOCAL7;
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var integer
     */
    public $options;
    /**
     * @var integer
     */
    public $facility=self::FACILITY_LOCAL7;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, $level_=null)
    {
      parent::__construct($name_, $level_);

      $this->options=LOG_CONS|LOG_PID;
      $this->pattern=self::PATTERN_SYSLOG_DEFAULT;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Log_Appender_Abstract::initialize() initialize
     */
    public function initialize()
    {
      if(false===$this->m_initialized)
      {
        parent::initialize();

        openlog($this->name, $this->options, $this->facility);
      }
    }

    /**
     * @see \Components\Log_Appender::append() append
     */
    public function append($level_, array $args_=[])
    {
      syslog(self::$m_mapLevelToSyslog[$level_], $this->format($level_, $args_));
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected static $m_mapLevelToSyslog=[
      Log::DEBUG=>LOG_DEBUG,
      Log::INFO=>LOG_INFO,
      Log::WARN=>LOG_WARNING,
      Log::ERROR=>LOG_ERR,
      Log::FATAL=>LOG_CRIT
    ];
    //--------------------------------------------------------------------------
  }
?>
