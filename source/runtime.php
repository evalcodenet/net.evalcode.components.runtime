<?php


namespace Components;


  // INCLUDES
  include_once __DIR__.'/util.php';

  require_once __DIR__.'/type/object.php';
  require_once __DIR__.'/classloader.php';

  require_once __DIR__.'/cache/backend.php';
  require_once __DIR__.'/cache/backend/apc.php';
  require_once __DIR__.'/cache/backend/local.php';
  require_once __DIR__.'/cache/backend/null.php';
  require_once __DIR__.'/cache.php';


  /**
   * Runtime
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   */
  class Runtime implements Object
  {
    // STATIC ACCESSORS
    /**
     * @return \Components\Runtime
     */
    public static function create()
    {
      if(null===self::$m_instance)
      {
        self::$m_instance=new self();

        set_error_handler(array(self::$m_instance, 'onErrorException'), error_reporting());
        set_exception_handler(array(self::$m_instance, 'onException'));

        self::$m_cacheFile=sys_get_temp_dir().DIRECTORY_SEPARATOR.COMPONENTS_INSTANCE_NAMESPACE.'-'.COMPONENTS_CACHE_NAMESPACE.'.cache';
        register_shutdown_function(array(self::$m_instance, 'onExit'));

        if('cli'===PHP_SAPI)
        {
          if(@is_file(self::$m_cacheFile))
            Cache::load(self::$m_cacheFile);
        }

        self::$m_version=new Version(
          COMPONENTS_RUNTIME_VERSION_MAJOR, COMPONENTS_RUNTIME_VERSION_MINOR, COMPONENTS_RUNTIME_VERSION_REVISION
        );
      }

      return self::$m_instance;
    }

    /**
     * @return \Components\Runtime
     */
    public static function get()
    {
      return self::$m_instance;
    }

    /**
     * @return \Components\Version
     */
    public static function version()
    {
      return self::$m_version;
    }

    /**
     * @return string
     */
    public static function getInstanceNamespace()
    {
      return COMPONENTS_INSTANCE_NAMESPACE;
    }

    /**
     * @return integer
     */
    public static function getTimestampLastUpdate()
    {
      return COMPONENTS_LAST_UPDATE;
    }

    /**
     * @return boolean
     */
    public static function isManagementAccess()
    {
      return isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], self::$m_managementIps);
    }

    /**
     * @return array|string
     */
    public static function getManagementIps()
    {
      return self::$m_managementIps;
    }

    /**
     * @param array|string $managementIps_
     */
    public static function setManagementIps(array $managementIps_)
    {
      self::$m_managementIps=$managementIps_;
    }

    /**
     * @return array|Components\Runtime_Error_Handler
     */
    public static function getRuntimeErrorHandlers()
    {
      return self::$m_runtimeErrorHandlers;
    }

    /**
     * @param \Components\Runtime_Error_Handler $errorHandler_
     */
    public static function addRuntimeErrorHandler(Runtime_Error_Handler $errorHandler_)
    {
      self::$m_runtimeErrorHandlers[]=$errorHandler_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function onException(\Exception $e_)
    {
      exception_log($e_);

      // TODO Collect/queue for further developer notifications...
      if(Environment::isCli())
      {
        printf('
          %1$s in %3$s
          %5$s
          %2$s
          %5$s
          %4$s
          %5$s',
            get_class($e_),
            $e_->getMessage(),
            implode(':', array($e_->getFile(), $e_->getLine())),
            $e_->getTraceAsString(),
            PHP_EOL
        );
      }
      else if(Environment::isDev())
      {
        exception_header($e_);

        printf('<?xml encoding="utf-8" version="1.0"?>%4$s
          <!DOCTYPE HTML>%4$s
          <html>
            <head>
              <meta charset="utf-8"/>
              <title>%1$s</title>
            </head>
            <body>
              <h1>%1$s</h1>
              <h2>%2$s</h2>
              <pre>%3$s</pre>
            </body>
          </html>',
            $e_->getMessage(),
            implode(':', array($e_->getFile(), $e_->getLine())),
            $e_->getTraceAsString(),
            PHP_EOL
        );
      }
      else
      {
        ob_clean();
        exception_header($e_);

        exit;
      }
    }

    public function onErrorException($type_, $message_, $filename_, $line_)
    {
      $error=new Runtime_ErrorException(
        'components/runtime', $message_, $type_, $filename_, $line_
      );

      exception_log($error);

      foreach(self::$m_runtimeErrorHandlers as $errorHandler)
      {
        if(true===$errorHandler->onError($error))
          return;
      }
    }

    public function onExit()
    {
      $error=error_get_last();

      if(null!==$error)
      {
        $this->onErrorException(
          $error['type'], $error['message'], $error['file'], $error['line']
        );
      }

      // TODO (CSH) What if is_file or Cache::dump throws an exception here?
      if(false===is_file(self::$m_cacheFile))
        Cache::dump(self::$m_cacheFile);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{version: %s, lastUpdate: %s}',
        __CLASS__,
        $this->hashCode(),
        self::$m_version,
        COMPONENTS_LAST_UPDATE
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|string
     */
    private static $m_managementIps=array();
    /**
     * @var array|Components\Runtime_Error_Handler
     */
    private static $m_runtimeErrorHandlers=array();
    /**
     * @var string
     */
    private static $m_cacheFile;
    /**
     * @var Components\Runtime
     */
    private static $m_instance;
    /**
     * @var Components\Version
     */
    private static $m_version;
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Classloader
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   */
  class Runtime_Classloader implements Classloader
  {
    // CONSTRUCTION
    public function __construct()
    {
      self::$m_instance=$this;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Runtime_Classloader
     */
    public static function get()
    {
      return self::$m_instance;
    }

    /**
     * @return array|Components\Classloader
     */
    public static function getClassloaders()
    {
      return array_merge(array(self::$m_instance), self::$m_classloaders);
    }

    /**
     * @param \Components\Classloader $classloader_
     *
     * @return \Components\Classloader
     */
    public static function push(Classloader $classloader_)
    {
      array_push(self::$m_classloaders, $classloader_);

      return $classloader_;
    }

    /**
     * @return \Components\Classloader
     */
    public static function pop()
    {
      return array_pop(self::$m_classloaders);
    }

    /**
     * @param string $name_
     *
     * @return boolean
     */
    public static function lookup($name_)
    {
      if(isset(self::$m_instance->m_resourceTypeForName[$name_]))
        return self::$m_instance->m_resourceTypeForName[$name_];

      return null;
    }

    /**
     * @param string $name_
     *
     * @return boolean
     */
    public static function lookupName($type_)
    {
      if(isset(self::$m_instance->m_resourceNameForType[$type_]))
        return self::$m_instance->m_resourceNameForType[$type_];

      return null;
    }

    /**
     * @param string $clazz_
     *
     * @return boolean
     */
    public static function classExists($clazz_)
    {
      return isset(self::$m_instance->m_classpaths[$clazz_]);
    }

    /**
     * @param string $clazz_
     *
     * @return boolean
     */
    public static function classExistsSearch($clazz_)
    {
      if(isset(self::$m_instance->m_classpaths[$clazz_]))
        return true;

      return class_exists($clazz_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**     * @see Components\Classloader::getClasspaths() Components\Classloader::getClasspaths()
     */
    public function getClasspaths()
    {
      return $this->m_classpaths;
    }

    /**
     * Returns location of given class.
     *
     * @param string $clazz_
     *
     * @return string
     */
    public function getClasspath($clazz_)
    {
      if(isset($this->m_classpaths[$clazz_]))
        return $this->m_classpaths[$clazz_];

      return null;
    }

    /**     * @see Components\Classloader::initialize() Components\Classloader::initialize()
     */
    public function initialize()
    {
      if($this->m_refresh || false===($this->m_classpaths=Cache::get('components/runtime/classpath')))
      {
        $iterator=new \RegexIterator(new \RecursiveIteratorIterator(
          new \RecursiveDirectoryIterator(__DIR__)),
          '/.php$/',
          \RecursiveRegexIterator::MATCH
        );

        $this->m_classpaths=array();
        foreach($iterator as $entry)
        {
          $matches=array();
          preg_match_all('/\n\s*(?:(?:abstract|final)+\s+)*(?:class|interface|trait)\s*(\w+)\s/',
            file_get_contents($entry->getPathname()), $matches
          );

          if(isset($matches[1]))
          {
            foreach($matches[1] as $match)
              $this->m_classpaths["Components\\$match"]=$entry->getPathname();
          }
        }

        Cache::set('components/runtime/classpath', $this->m_classpaths);

        $this->m_refresh=false;
      }

      if($this->m_resourceTypeForName=Cache::get('components/runtime/resources'))
        $this->m_resourceNameForType=array_flip($this->m_resourceTypeForName);
    }

    /**     * @see Components\Classloader::loadClass() Components\Classloader::loadClass()
     */
    public function loadClass($clazz_)
    {
      if(isset($this->m_classpaths[$clazz_]))
      {
        require_once $this->m_classpaths[$clazz_];

        return true;
      }

      // FIXME (CSH) Avoid re-initialization if combined with other classloaders, yet keep laziness, merge sub-classloaders & provide resource names/lookup.
      if(2<++self::$m_count)
        return false;

      $this->initialize();

      if(isset($this->m_classpaths[$clazz_]))
      {
        require_once $this->m_classpaths[$clazz_];

        return true;
      }

      $this->m_refresh=true;
      $this->initialize();

      if(isset($this->m_classpaths[$clazz_]))
      {
        require_once $this->m_classpaths[$clazz_];

        return true;
      }

      /* @var $classloader \Components\Classloader */
      foreach(self::$m_classloaders as $classloader)
      {
        $classloader->initialize();
        $this->m_classpaths=array_merge($this->m_classpaths, $classloader->getClasspaths());
      }

      foreach($this->m_classpaths as $clazz=>$path)
        $this->m_resourceTypeForName[strtolower(strtr(strtr($clazz, '\\', '_'), '_', '/'))]=$clazz;

      $this->m_resourceNameForType=array_flip($this->m_resourceTypeForName);

      Cache::set('components/runtime/classpath', $this->m_classpaths);
      Cache::set('components/runtime/resources', $this->m_resourceTypeForName);

      if(isset($this->m_classpaths[$clazz_]))
      {
        require_once $this->m_classpaths[$clazz_];

        return true;
      }

      return false;
    }

    /**     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      // We do not support multiple instances of Runtime_Classloader (currently).
      if($object_ instanceof self)
        return true;

      return false;
    }

    /**     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|\Components\Classloader
     */
    private static $m_classloaders=array();
    /**
     * @var \Components\Runtime_Classloader
     */
    private static $m_instance;
    private static $m_count=0;

    /**
     * @var array|string
     */
    private $m_classpaths=array();
    /**
     * @var array|string
     */
    private $m_resourceTypeForName=array();
    /**
     * @var array|string
     */
    private $m_resourceNameForType=array();
    /**
     * @var boolean
     */
    private $m_refresh=false;
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Exception
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   */
  class Runtime_Exception extends \Exception implements Object
  {
    // CONSTRUCTION
    public function __construct($namespace_, $message_, \Exception $cause_=null, $logEnabled_=true)
    {
      // TODO Upgrade servers and remove this ...
      if(4<PHP_MAJOR_VERSION && 2<PHP_MINOR_VERSION)
        parent::__construct($message_, null, $cause_);
      else
        parent::__construct($message_);

      $this->m_namespace=$namespace_;
      $this->m_logEnabled=$logEnabled_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function getNamespace()
    {
      return $this->m_namespace;
    }

    public function log()
    {
      if($this->m_logEnabled)
        Log::error($this->m_namespace, '[%s] %s', md5($this->hashCode()), $this);
    }

    /**
     * Sends exception details as header: Components-Exception.
     */
    public function sendHeader()
    {
      header('HTTP/1.1 500 Internal Server Error', true, 500);

      $hash=md5($this->hashCode());
      header("Components-Exception: $hash");
      if(Runtime::isManagementAccess())
        header("$hash: {$this->toJson()}");
    }

    /**
     * @return string
     */
    public function toJson()
    {
      return json_encode(array(
        'type'=>get_class($this),
        'code'=>$this->code,
        'namespace'=>$this->getNamespace(),
        'message'=>$this->getMessage(),
        'stack'=>$this->getTraceAsString()
      ));
    }

    /**
     * @return string
     */
    public function toXml()
    {
      // TODO Embed stack trace.
      return sprintf('<?xml version="1.0" encoding="utf-8"?>%7$s
        <exception>%7$s
          <type>%1$s</type>%7$s
          <pre>%2$s</pre>%7$s
          <namespace>%3$s</namespace>%7$s
          <message>%4$s</message>%7$s
          <source>%5$s</source>%7$s
          <stack>%6$s</stack>%7$s
        </exception>',
        get_class($this),
        $this->code,
        $this->getNamespace(),
        $this->getMessage(),
        implode(':', array($this->getFile(), $this->getLine())),
        json_encode($this->getTraceAsString()),
        PHP_EOL
      );
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf("%s\n\n%s\n",
        $this->message,
        $this->getTraceAsString()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_logEnabled;
    protected $m_namespace;
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_ErrorException
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   */
  class Runtime_ErrorException extends \ErrorException implements Object
  {
    // CONSTRUCTION
    public function __construct($namespace_, $message_, $code_=null,
      $filename_=null, $line_=0, \Exception $cause_=null, $logEnabled_=true)
    {
      parent::__construct($message_, $code_, E_USER_ERROR, $filename_, $line_, $cause_);

      $this->m_namespace=$namespace_;
      $this->m_logEnabled=$logEnabled_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function getNamespace()
    {
      return $this->m_namespace;
    }

    public function log()
    {
      if($this->m_logEnabled)
        Log::error($this->m_namespace, '[%s] %s', md5($this->hashCode()), $this);
    }

    /**
     * Sends exception details as header: Components-Exception.
     */
    public function sendHeader()
    {
      header('HTTP/1.1 500 Internal Server Error', true, 500);

      $hash=md5($this->hashCode());
      header("Components-Exception: $hash");
      if(Runtime::isManagementAccess())
        header("$hash: {$this->toJson()}");
    }

    /**
     * @return string
     */
    public function toJson()
    {
      return json_encode(array(
        'type'=>get_class($this),
        'code'=>$this->code,
        'namespace'=>$this->getNamespace(),
        'message'=>$this->getMessage(),
        'stack'=>$this->getTraceAsString()
      ));
    }

    /**
     * @return string
     */
    public function toXml()
    {
      // TODO Embed stack trace.
      return sprintf('<?xml version="1.0" encoding="utf-8"?>%7$s
        <exception>%7$s
          <type>%1$s</type>%7$s
          <pre>%2$s</pre>%7$s
          <namespace>%3$s</namespace>%7$s
          <message>%4$s</message>%7$s
          <source>%5$s</source>%7$s
          <stack>%6$s</stack>%7$s
        </exception>',
        get_class($this),
        $this->code,
        $this->getNamespace(),
        $this->getMessage(),
        implode(':', array($this->getFile(), $this->getLine())),
        json_encode($this->getTraceAsString()),
        PHP_EOL
      );
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf("%s\n\n%s\n",
        $this->message,
        $this->getTraceAsString()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_logEnabled;
    protected $m_namespace;
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Error_Handler
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   */
  interface Runtime_Error_Handler extends Object
  {
    // ACCESSORS
    /**
     * @param \Components\Runtime_ErrorException $e_
     *
     * @return boolean
     */
    function onError(Runtime_ErrorException $e_);
    //--------------------------------------------------------------------------
  }


  Cache::create();
  spl_autoload_register(array(new Runtime_Classloader(), 'loadClass'));
  Log::push(new Log_Appender_Null('null', Log::FATAL));
?>
