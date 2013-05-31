<?php


namespace Components;


  /**
   * Log_Appender_Abstract
   *
   * @package net.evalcode.components
   * @subpackage log.appender
   *
   * @author evalcode.net
   */
  abstract class Log_Appender_Abstract implements Log_Appender
  {
    // PREDEFINED PROPERTIES
    const DEFAULT_LEVEL=Log::INFO;
    const DEFAULT_PATTERN="[%d.%u%z] [%l] [%n] %m\n";
    const DEFAULT_PATTERN_DATE='Y.m.d\TH:i:s';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, $level_=self::DEFAULT_LEVEL,
      $pattern_=self::DEFAULT_PATTERN, $patternDate_=self::DEFAULT_PATTERN_DATE)
    {
      $this->m_name=$name_;
      $this->m_level=$level_;
      $this->m_pattern=$pattern_;
      $this->m_patternDate=$patternDate_;
      $this->m_timezone=date('P');
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::name()
     */
    public function name()
    {
      return $this->m_name;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::level()
     */
    public function level()
    {
      return $this->m_level;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::debug()
     */
    public function debug($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::DEBUG, func_get_args());
    }

    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::info()
     */
    public function info($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::INFO, func_get_args());
    }

    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::warn()
     */
    public function warn($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::WARN, func_get_args());
    }

    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::error()
     */
    public function error($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::ERROR, func_get_args());
    }

    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::fatal()
     */
    public function fatal($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::FATAL, func_get_args());
    }

    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::initialize()
     */
    public function initialize()
    {
      // Override if necessary ..
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::equals()
     */
    public function equals($object_)
    {
      return $this===$object_;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return string_hash($this->m_name);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{name: %s, level: %s, pattern: %s}',
        get_class($this),
        $this->hashCode(),
        $this->m_name,
        $this->m_level,
        $this->m_pattern
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected static $m_mapNameToLevel=array(
      'debug'=>Log::DEBUG,
      'info'=>Log::INFO,
      'warn'=>Log::WARN,
      'error'=>Log::ERROR,
      'fatal'=>Log::FATAL
    );
    protected static $m_mapLevelToName=array(
      Log::DEBUG=>'debug',
      Log::INFO=>'info',
      Log::WARN=>'warn',
      Log::ERROR=>'error',
      Log::FATAL=>'fatal'
    );
    protected static $m_mapLevelToOutput=array(
      Log::DEBUG=>'DEBUG',
      Log::INFO=>'INFO',
      Log::WARN=>'WARN',
      Log::ERROR=>'ERROR',
      Log::FATAL=>'FATAL'
    );

    protected $m_name;
    protected $m_level;
    protected $m_pattern;
    protected $m_patternDate;

    private $m_timezone;
    //-----


    /**
     * Format log message and substitute placeholders.
     *
     * @param int $level_
     * @param array $args_
     *
     * @return string
     */
    protected function format($level_, array $args_=array())
    {
      $time=microtime(true);
      $time=array(substr($time, 0, COMPONENTS_TIMESTAMP_SIZE),
        str_pad(substr($time, COMPONENTS_TIMESTAMP_SIZE+1, 4), 4, 0, STR_PAD_LEFT)
      );

      return str_replace(
        array('%d', '%u', '%z', '%l', '%n', '%m'),
        array(
          date($this->m_patternDate, $time[0]),
          $time[1],
          $this->m_timezone,
          self::$m_mapLevelToOutput[$level_],
          array_shift($args_),
          vsprintf(array_shift($args_), $args_)
        ),
        $this->m_pattern
      );
    }
    //--------------------------------------------------------------------------
  }
?>
