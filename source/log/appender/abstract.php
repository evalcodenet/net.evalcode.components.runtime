<?php


namespace Components;


  /**
   * Log_Appender_Abstract
   *
   * @api
   * @package net.evalcode.components.log
   * @subpackage appender
   *
   * @author evalcode.net
   */
  abstract class Log_Appender_Abstract implements Log_Appender
  {
    // PREDEFINED PROPERTIES
    const DEFAULT_LEVEL=Log::INFO;
    const DEFAULT_PATTERN="[%d.%u%z] [%h] [%l] [%n] %m\n";
    const DEFAULT_PATTERN_DATE='Y.m.d\TH:i:s';
    //--------------------------------------------------------------------------


    // PROPERTIES
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $host;
    /**
     * @var integer
     */
    public $level=self::DEFAULT_LEVEL;
    /**
     * @var string
     */
    public $pattern=self::DEFAULT_PATTERN;
    /**
     * @var string
     */
    public $patternDate=self::DEFAULT_PATTERN_DATE;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, $level_=self::DEFAULT_LEVEL)
    {
      $this->name=$name_;
      $this->level=$level_;

      $this->m_timezone=date('P');
      $this->host=hostname();
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Log_Appender::debug() \Components\Log_Appender::debug()
     */
    public function debug($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::DEBUG, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::info() \Components\Log_Appender::info()
     */
    public function info($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::INFO, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::warn() \Components\Log_Appender::warn()
     */
    public function warn($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::WARN, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::error() \Components\Log_Appender::error()
     */
    public function error($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::ERROR, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::fatal() \Components\Log_Appender::fatal()
     */
    public function fatal($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::FATAL, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::initialize() \Components\Log_Appender::initialize()
     */
    public function initialize()
    {
      // Override if necessary ..
    }

    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_name===$object_->m_name;

      return false;
    }

    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return string_hash($this->name);
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{name: %s, level: %s, pattern: %s}',
        get_class($this),
        $this->hashCode(),
        $this->name,
        $this->level,
        $this->pattern
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
    protected function format($level_, array $args_=[])
    {
      $time=microtime(true);
      $time=array(substr($time, 0, COMPONENTS_TIMESTAMP_SIZE),
        str_pad(substr($time, COMPONENTS_TIMESTAMP_SIZE+1, 3), 3, 0, STR_PAD_LEFT)
      );

      return str_replace(
        array('%d', '%u', '%z', '%h', '%l', '%n', '%m'),
        array(
          date($this->patternDate, $time[0]),
          $time[1],
          $this->m_timezone,
          $this->host,
          self::$m_mapLevelToOutput[$level_],
          array_shift($args_),
          vsprintf(array_shift($args_), $args_)
        ),
        $this->pattern
      );
    }
    //--------------------------------------------------------------------------
  }
?>
