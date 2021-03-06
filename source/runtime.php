<?php


namespace Components;


  // INCLUDES
  require_once __DIR__.'/type/object.php';
  require_once __DIR__.'/classloader.php';


  /**
   * Runtime
   *
   * @api
   * @package net.evalcode.components.runtime
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

        self::$m_isCli='cli'===PHP_SAPI;
        set_error_handler([self::$m_instance, 'onError'], error_reporting());
        set_exception_handler([self::$m_instance, 'onException']);

        self::$m_cacheFile=sys_get_temp_dir().DIRECTORY_SEPARATOR.COMPONENTS_INSTANCE_CODE.'.cache';
        register_shutdown_function([self::$m_instance, 'onExit']);

        if(self::$m_isCli)
        {
          if(@is_file(self::$m_cacheFile))
            Cache::load(self::$m_cacheFile);
        }

        self::$m_version=new Version(
          COMPONENTS_RUNTIME_VERSION_MAJOR,
          COMPONENTS_RUNTIME_VERSION_MINOR,
          COMPONENTS_RUNTIME_VERSION_BUILD
        );

        Environment::includeComponentConfig('runtime');
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
      return COMPONENTS_INSTANCE_CODE;
    }

    /**
     * @return boolean
     */
    public static function isCli()
    {
      return self::$m_isCli;
    }

    /**
     * @return boolean
     */
    public static function isManagementAccess()
    {
      return in_array(self::getClientAddress(), self::$m_managementIps);
    }

    /**
     * @param string $managementIp_
     */
    public static function addManagementIp($managementIp_)
    {
      if(null===self::$m_managementIps)
        self::$m_managementIps=[];

      self::$m_managementIps[]=$managementIp_;
    }

    /**
     * @return string[]
     */
    public static function getManagementIps()
    {
      return self::$m_managementIps;
    }

    /**
     * @param string[] $managementIps_
     */
    public static function setManagementIps(array $managementIps_)
    {
      self::$m_managementIps=$managementIps_;
    }

    /**
     * @return string
     */
    public static function getHost()
    {
      if(null===self::$m_host)
      {
        if(isset($_SERVER['HTTP_HOST']))
        {
          self::$m_host=strtolower($_SERVER['HTTP_HOST']);

          if(false!==($pos=strpos(self::$m_host, ':')))
            self::$m_host=substr(self::$m_host, $pos);
        }
        else
        {
          self::$m_host='localhost';
        }
      }

      return self::$m_host;
    }

    /**
     * @return string
     */
    public static function getHostAddress()
    {
      if(null===self::$m_hostAddress)
      {
        if(isset($_SERVER['SERVER_ADDR']))
          self::$m_hostAddress=$_SERVER['SERVER_ADDR'];
        else
          self::$m_hostAddress='127.0.0.1';
      }

      return self::$m_hostAddress;
    }

    /**
     * @return string
     */
    public static function getClientAddress()
    {
      if(null===self::$m_clientAddress)
      {
        self::$m_clientAddress='0.0.0.0';

        // FIXME Support IPv6.
        if(self::isCli())
          self::$m_clientAddress='127.0.0.1';
        // FIXME Set REMOTE_ADDR via nginx/fastcgi_params if request comes from varnish.
        else if(isset($_SERVER['REMOTE_ADDR']) && false===isset($_SERVER['HTTP_X_VARNISH']))
          self::$m_clientAddress=$_SERVER['REMOTE_ADDR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
          self::$m_clientAddress=$_SERVER['HTTP_X_FORWARDED_FOR'];
      }

      return self::$m_clientAddress;
    }

    /**
     * @return \Components\Runtime_Error_Handler[]
     */
    public static function getRuntimeErrorHandlers()
    {
      return self::$m_runtimeErrorHandlers;
    }

    /**
     * @param \Components\Runtime_Error_Handler $errorHandler_
     */
    public static function pushRuntimeErrorHandler(Runtime_Error_Handler $errorHandler_)
    {
      array_unshift(self::$m_runtimeErrorHandlers, $errorHandler_);
    }

    /**
     * @param \Components\Runtime_Error_Handler $errorHandler_
     */
    public static function popRuntimeErrorHandler()
    {
      return array_shift(self::$m_runtimeErrorHandlers);
    }

    /**
     * @return \Components\Runtime_Exception_Handler[]
     */
    public static function getRuntimeExceptionHandlers()
    {
      return self::$m_runtimeExceptionHandlers;
    }

    /**
     * @param \Components\Runtime_Exception_Handler $exceptionHandler_
     */
    public static function pushRuntimeExceptionHandler(Runtime_Exception_Handler $exceptionHandler_)
    {
      array_unshift(self::$m_runtimeExceptionHandlers, $exceptionHandler_);
    }

    /**
     * @param \Components\Runtime_Exception_Handler $exceptionHandler_
     */
    public static function popRuntimeExceptionHandler()
    {
      return array_shift(self::$m_runtimeExceptionHandlers);
    }

    /**
     * @return boolean
     */
    public static function hasExceptions()
    {
      return 0<count(self::$m_exceptions);
    }

    /**
     * @return \Exception[]
     */
    public static function getExceptions()
    {
      return self::$m_exceptions;
    }

    /**
     * Returns and clears exceptions.
     *
     * @return \Exception[]
     */
    public static function clearExceptions()
    {
      $exceptions=self::$m_exceptions;
      self::$m_exceptions=[];

      return $exceptions;
    }

    /**
     * @param \Exception $e_
     */
    public static function addException(\Exception $e_)
    {
      exception_header($e_);
      exception_log($e_);

      array_push(self::$m_exceptions, $e_);
    }

    /**
     * @param \Exception $e_
     */
    public static function removeException(\Exception $e_)
    {
      $hash=\math\hasho_md5($e_);

      $exceptions=[];
      foreach(self::$m_exceptions as $exception)
      {
        if($hash!==\math\hasho_md5($exception))
          $exceptions[]=$exception;
      }

      self::$m_exceptions=$exception;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @link http://php.net/manual/en/function.set-exception-handler.php
     */
    public function onException(\Exception $e_)
    {
      foreach(self::$m_runtimeExceptionHandlers as $exceptionHandler)
      {
        if(true===$exceptionHandler->onException($e_))
          return true;
      }

      self::addException($e_);

      return true;
    }

    /**
     * @link http://php.net/manual/en/function.set-error-handler.php
     */
    public function onError($type_, $message_, $filename_, $line_)
    {
      if(false!==strpos($message_, 'Allowed memory size'))
      {
        ob_clean();

        if('cli'===PHP_SAPI)
        {
          echo "Out of memory!\n";
        }
        else
        {
          Log::error('components/runtime', 'Out of memory.');

          @header('HTTP/1.1 500 Internal Server Error', true, 500);
          if(self::isManagementAccess())
            @header('Components-Exception-0: Out of memory.');
        }

        exit;
      }

      $error=new Runtime_Error_Internal(
        'components/runtime', $message_, $type_, $filename_, $line_
      );

      foreach(self::$m_runtimeErrorHandlers as $errorHandler)
      {
        if(true===$errorHandler->onError($error))
          return true;
      }

      self::addException($error);

      return true;
    }

    /**
     * @link http://php.net/manual/en/function.register-shutdown-function.php
     */
    public function onExit()
    {
      $error=error_get_last();

      if(null!==$error)
      {
        $this->onError(
          $error['type'],
          $error['message'],
          $error['file'],
          $error['line']
        );
      }

      if(self::$m_isCli)
      {
        $hasErrors=0<count(self::$m_exceptions);

        foreach(self::$m_exceptions as $exception)
          exception_print_cli($exception, true, true);

        if(false===@is_file(self::$m_cacheFile))
          Cache::dump(self::$m_cacheFile);

        exit(false===$hasErrors?0:-1);
      }

      if(Debug::active() && (self::isManagementAccess() || Environment::isDev()))
      {
        if(0<count(self::$m_exceptions))
          Debug::verror(self::$m_exceptions);

        Debug::flush();

        self::$m_exceptions=[];
      }

      if(false===Environment::isDev())
        self::$m_exceptions=[];

      foreach(self::$m_exceptions as $exception)
        exception_print_html($exception, true, true);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s{version: %s}',
        __CLASS__,
        $this->hashCode(),
        self::$m_version
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    private static $m_isCli=false;
    /**
     * @var string
     */
    private static $m_clientAddress;
    /**
     * @var string
     */
    private static $m_host;
    /**
     * @var string
     */
    private static $m_hostAddress;
    /**
     * @var boolean
     */
    private static $m_isManagementAccess;
    /**
     * @var string[]
     */
    private static $m_managementIps=[];
    /**
     * @var \Components\Runtime_Error_Handler[]
     */
    private static $m_runtimeErrorHandlers=[];
    /**
     * @var \Components\Runtime_Exception_Handler[]
     */
    private static $m_runtimeExceptionHandlers=[];
    /**
     * @var \Exception[]
     */
    private static $m_exceptions=[];
    /**
     * @var string
     */
    private static $m_cacheFile;
    /**
     * @var \Components\Runtime
     */
    private static $m_instance;
    /**
     * @var \Components\Version
     */
    private static $m_version;
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Classloader
   *
   * @api
   * @package net.evalcode.components.runtime
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
     * @return \Components\Classloader[]
     */
    public static function getClassloaders()
    {
      return array_merge([self::$m_instance], self::$m_classloaders);
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


    // IMPLEMENTS
    /**
     * @see \Components\Classloader::getClasspaths() getClasspaths
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

    /**
     * @see \Components\Classloader::initialize() initialize
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

        $this->m_classpaths=[];
        foreach($iterator as $entry)
        {
          $matches=[];
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

    /**
     * @see \Components\Classloader::loadClass() loadClass
     */
    public function loadClass($clazz_)
    {
      if(isset($this->m_classpaths[$clazz_]))
      {
        if(@include_once $this->m_classpaths[$clazz_])
          return true;
      }

      // FIXME (CSH) Avoid re-initialization if combined with other classloaders, yet keep laziness, merge sub-classloaders & provide resource names/lookup.
      if(2<++self::$m_count)
        return false;

      $this->initialize();

      if(isset($this->m_classpaths[$clazz_]))
      {
        if(@include_once $this->m_classpaths[$clazz_])
          return true;
      }

      $this->m_refresh=true;
      $this->initialize();

      if(isset($this->m_classpaths[$clazz_]))
      {
        if(@include_once $this->m_classpaths[$clazz_])
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
        if(@include_once $this->m_classpaths[$clazz_])
          return true;
      }

      return false;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      // We do not support multiple instances of Runtime_Classloader (currently).
      if($object_ instanceof self)
        return true;

      return false;
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Classloader[]
     */
    private static $m_classloaders=[];
    /**
     * @var integer
     */
    private static $m_count=0;
    /**
     * @var \Components\Runtime_Classloader
     */
    private static $m_instance;

    /**
     * @var string[]
     */
    private $m_classpaths=[];
    /**
     * @var string[]
     */
    private $m_resourceTypeForName=[];
    /**
     * @var string[]
     */
    private $m_resourceNameForType=[];
    /**
     *  @var boolean
     */
    private $m_refresh=false;
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Exception
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  interface Runtime_Exception extends Object
  {
    // ACCESSORS
    /**
     * @return string
     */
    function getNamespace();

    /**
     * @return void
     */
    function log();
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Exception_Transformable
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  interface Runtime_Exception_Transformable
  {
    // ACCESSORS
    /**
     * @param boolean $includeStackTrace_
     * @param boolean $stackTraceAsArray_
     *
     * @return mixed[]
     */
    function toArray($includeStackTrace_=false, $stackTraceAsArray_=false);
    /**
     * @param boolean $includeStackTrace_
     * @param boolean $stackTraceAsArray_
     *
     * @return string
     */
    function toJson($includeStackTrace_=false, $stackTraceAsArray_=false);
    /**
     * @param boolean $includeStackTrace_
     * @param boolean $stackTraceAsArray_
     *
     * @return string
     */
    function toXml($includeStackTrace_=false, $stackTraceAsArray_=false);
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Exception_Transformable_Default
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  trait Runtime_Exception_Transformable_Default/* implements Runtime_Exception_Transformable*/
  {
    // IMPLEMENTS
    /**
     * @see \Components\Runtime_Exception_Transformable::toArray() toArray
     */
    public function toArray($includeStackTrace_=false, $stackTraceAsArray_=false)
    {
      $asArray=[
        'id'=>\math\hasho_md5($this),
        'type'=>get_class($this),
        'code'=>$this->getCode(),
        'namespace'=>$this->getNamespace(),
        'message'=>$this->getMessage(),
        'file'=>$this->getFile(),
        'line'=>$this->getLine()
      ];

      if($includeStackTrace_ && $stackTraceAsArray_)
        $asArray['stack']=exception_stacktrace_as_array($this);
      else if($includeStackTrace_)
        $asArray['stack']=$this->getTraceAsString();

      return $asArray;
    }

    /**
     * @see \Components\Runtime_Exception_Transformable::toJson() toJson
     */
    public function toJson($includeStackTrace_=false, $stackTraceAsArray_=false)
    {
      return json_encode($this->toArray($includeStackTrace_, $stackTraceAsArray_));
    }

    /**
     * @see \Components\Runtime_Exception_Transformable::toXml() toXml
     */
    public function toXml($includeStackTrace_=false, $stackTraceAsArray_=false)
    {
      $xml="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
      $xml.="<exception>\n";

      foreach($this->toArray($includeStackTrace_, $stackTraceAsArray_) as $key=>$value)
      {
        if(is_array($value))
        {
          $xml.="\t<stack>\n";
          foreach($value as $k=>$v)
            $xml.="\t\t<$k>".String::escapeJs($v)."</$k>\n";
          $xml.="\t</stack>\n";
        }
        else
        {
          $xml.="\t<$key>".String::escapeJs($value)."</$key>\n";
        }
      }

      $xml.="</exception>\n";

      return $xml;
    }
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Exception_Abstract
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  abstract class Runtime_Exception_Abstract extends \Exception
    implements Runtime_Exception, Runtime_Exception_Transformable
  {
    // DEFAULTS
    use Runtime_Exception_Transformable_Default;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($namespace_, $message_, \Exception $cause_=null, $logEnabled_=true)
    {
      parent::__construct($message_, null, $cause_);

      $this->m_namespace=$namespace_;
      $this->m_logEnabled=$logEnabled_;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTS
    /**
     * @see \Components\Runtime_Exception::getNamespace() getNamespace
     */
    public function getNamespace()
    {
      return $this->m_namespace;
    }

    /**
     * @see \Components\Runtime_Exception::log() log
     */
    public function log()
    {
      if($this->m_logEnabled)
      {
        Log::error($this->m_namespace, '[%s] %s%s',
         \math\hasho_md5($this),
          get_class($this),
          $this
        );
      }
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      if(!$file=$this->getFile())
        $file='internal';
      if(!$line=$this->getLine())
        $line=0;

      return sprintf("\n\n#0 %s\n#0 %s(%d)\n#0\n%s\n",
        $this->message,
        $file,
        $line,
        $this->getTraceAsString()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    protected $m_logEnabled;
    /**
     * @var string
     */
    protected $m_namespace;
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Exception_Internal
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  class Runtime_Exception_Internal extends Runtime_Exception_Abstract
  {

  }


  /**
   * Runtime_Error
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  interface Runtime_Error extends Runtime_Exception
  {

  }


  /**
   * Runtime_Error_Abstract
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  abstract class Runtime_Error_Abstract extends \ErrorException
    implements Runtime_Error, Runtime_Exception_Transformable
  {
    // DEFAULTS
    use Runtime_Exception_Transformable_Default;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($namespace_, $message_, $code_=null,
      $filename_=null, $line_=0, \Exception $cause_=null, $logEnabled_=true)
    {
      parent::__construct($message_, $code_, E_USER_ERROR, $filename_, $line_, $cause_);

      $this->m_namespace=$namespace_;
      $this->m_logEnabled=$logEnabled_;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTS
    /**
     * @see \Components\Runtime_Exception::getNamespace() getNamespace
     */
    public function getNamespace()
    {
      return $this->m_namespace;
    }

    /**
     * @see \Components\Runtime_Exception::log() log
     */
    public function log()
    {
      if($this->m_logEnabled)
      {
        Log::error($this->m_namespace, '[%s] %s%s',
         \math\hasho_md5($this),
          get_class($this),
          $this
        );
      }
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      if(!$file=$this->getFile())
        $file='internal';
      if(!$line=$this->getLine())
        $line=0;

      return sprintf("\n\n#0 %s\n#0 %s(%d)\n#0\n%s\n",
        $this->message,
        $file,
        $line,
        $this->getTraceAsString()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    protected $m_logEnabled;
    /**
     * @var string
     */
    protected $m_namespace;
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Error_Internal
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  class Runtime_Error_Internal extends Runtime_Error_Abstract
  {

  }


  /**
   * Runtime_Error_Handler
   *
   * @api
   * @package net.evalcode.components.runtime
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
    function onError(\ErrorException $e_);
    //--------------------------------------------------------------------------
  }


  /**
   * Runtime_Exception_Handler
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  interface Runtime_Exception_Handler extends Object
  {
    // ACCESSORS
    /**
     * @param \Components\Runtime_Exception $e_
     *
     * @return boolean
     */
    function onException(\Exception $e_);
    //--------------------------------------------------------------------------
  }


    // SETUP
    include_once __DIR__.'/util.php';
    include_once __DIR__.'/lib/std.php';

    require_once __DIR__.'/cache/backend.php';
    require_once __DIR__.'/cache/backend/apc.php';
    require_once __DIR__.'/cache/backend/local.php';
    require_once __DIR__.'/cache/backend/null.php';
    require_once __DIR__.'/cache/backend/xcache.php';
    require_once __DIR__.'/cache.php';

    Cache::create();
    spl_autoload_register([new Runtime_Classloader(), 'loadClass']);
    Log::push(new Log_Appender_Null('null', Log::FATAL));
    //--------------------------------------------------------------------------
?>
