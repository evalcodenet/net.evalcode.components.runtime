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
    const PATTERN_DEBUG="[%D] [%L] [%h] [%B] [%r] [%n]\n\t%m\n";
    const PATTERN_DEFAULT="%d %l %h %b %c %r [%n] %m\n";

    const PATTERN_DATE='c';
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
    public $level;
    /**
     * @var string
     */
    public $pattern=self::PATTERN_DEFAULT;
    /**
     * @var string
     */
    public $patternDate=self::PATTERN_DATE;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($name_, $level_=null)
    {
      if(null===$level_)
        $level_=Environment::logLevelDefault();

      $this->name=$name_;
      $this->level=$level_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Log_Appender::debug() debug
     */
    public function debug($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::DEBUG, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::info() info
     */
    public function info($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::INFO, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::warn() warn
     */
    public function warn($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::WARN, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::error() error
     */
    public function error($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::ERROR, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::fatal() fatal
     */
    public function fatal($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      $this->append(Log::FATAL, func_get_args());
    }

    /**
     * @see \Components\Log_Appender::initialize() initialize
     */
    public function initialize()
    {
      if(false===$this->m_initialized)
      {
        if(null===$this->host)
          $this->host=\env\hostname();

        $this->compilePattern($this->pattern);

        $this->m_initialized=true;
      }
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_name===$object_->m_name;

      return false;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hashs($this->name);
    }

    /**
     * @see \Components\Object::__toString() __toString
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
    protected static $m_mapNameToLevel=[
      'debug'=>Log::DEBUG,
      'info'=>Log::INFO,
      'warn'=>Log::WARN,
      'error'=>Log::ERROR,
      'fatal'=>Log::FATAL
    ];
    protected static $m_mapLevelToName=[
      Log::DEBUG=>'debug',
      Log::INFO=>'info',
      Log::WARN=>'warn',
      Log::ERROR=>'error',
      Log::FATAL=>'fatal'
    ];
    protected static $m_mapLevelToOutput=[
      Log::DEBUG=>'DEBUG',
      Log::INFO=>'INFO',
      Log::WARN=>'WARN',
      Log::ERROR=>'ERROR',
      Log::FATAL=>'FATAL'
    ];

    /**
     * @var boolean
     */
    protected $m_initialized=false;
    /**
     * @var string
     */
    protected $m_patternCompiled;
    /**
     * @var string
     */
    protected $m_patternPlaceholder;
    /**
     * @var \Closure[]
     */
    protected $m_patternCallbacks=[];

    /**
     * @internal
     */
    private $__level;
    /**
     * @internal
     */
    private $__namespace;
    /**
     * @internal
     */
    private $__message;
    //-----


    /**
     * Format log message and substitute placeholders.
     *
     * @param int $level_
     * @param mixed[] $args_
     *
     * @return string
     */
    protected function format($level_, array $args_=[])
    {
      // FIXME Define scope of / pass scope to preg_replace_callback callback!?
      $this->__level=$level_;
      $this->__namespace=array_shift($args_);

      $message=array_shift($args_);

      foreach($args_ as $key=>$value)
      {
        if(is_array($value))
          $args_[$key]=Arrays::toString($value);
      }

      $this->__message=vsprintf($message, $args_);

      return preg_replace_callback(
        $this->m_patternPlaceholder,
        function($placeholder_) {
          return $this->m_patternCallbacks[reset($placeholder_)]();
        },
        $this->m_patternCompiled
      );
    }

    protected function compilePattern()
    {
      $available=[
        'l'=>function() {
          return $this->__level;
        },
        'L'=>function() {
          return self::$m_mapLevelToOutput[$this->__level];
        },
        'd'=>function() {
          return microtime(true);
        },
        'D'=>function() {
          $microtime=(string)microtime(true);
          return date($this->patternDate, $microtime).substr($microtime, strpos($microtime, '.'));
        },
        'b'=>function() {
          return memory_get_usage(true);
        },
        'B'=>function() {
          return \io\convertToMB(memory_get_usage(true)).' MB';
        },
        'm'=>function() {
          return $this->__message;
        },
        'n'=>function() {
          return $this->__namespace;
        }
      ];

      $clientIp=Runtime::getClientAddress();
      if(!$clientIpLong=ip2long($clientIp))
        $clientIpLong=0;

      $requestUri='/';
      if(isset($_SERVER['REQUEST_URI']))
        $requestUri=$_SERVER['REQUEST_URI'];
      else if(isset($argv) && 0<$argc)
        $requestUri=http_build_query($argv);

      $this->m_patternCompiled=\str\replace($this->pattern, ['%h', '%c', '%C', '%r'],
        [$this->host, $clientIpLong, $clientIp, $requestUri]
      );

      $chars=\str\split($this->pattern, 1);
      $count=count($chars);

      $placeholders='';
      $callbacks=[];

      for($idx=0; $idx<$count; $idx++)
      {
        if('%'==$chars[$idx] && ++$idx<$count)
        {
          $placeholder=$chars[$idx];

          if(isset($available[$placeholder]))
          {
            $placeholders.=$placeholder;
            $callbacks["%$placeholder"]=$available[$placeholder];
          }
        }
      }

      $this->m_patternPlaceholder="/(%[$placeholders])+/";
      $this->m_patternCallbacks=$callbacks;
    }
    //--------------------------------------------------------------------------
  }
?>
