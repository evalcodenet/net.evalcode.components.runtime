<?php


namespace Components;


  /**
   * Environment
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  class Environment implements Object, Cloneable, Value_String
  {
    // PREDEFINED PROPERTIES
    const LIVE=1;
    const DEV=2;

    const ALPHA=4;
    const BETA=8;
    const GAMMA=16;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($type_)
    {
      $this->m_type=$type_;
      $this->m_name=self::$m_names[$type_];

      $this->m_pathComponents=dirname(dirname(__DIR__));

      $this->m_pathApplication=COMPONENTS_PATH_APP;
      $this->m_pathConfig=COMPONENTS_PATH_APP.'/config';
      $this->m_pathResource=COMPONENTS_PATH_APP.'/resource';
      $this->m_pathWeb=COMPONENTS_PATH_APP.'/resource';

      $this->m_uriComponents='/components';
      $this->m_uriComponentsEmbedded=$this->m_uriComponents.'/embedded';

      $this->m_uriResource='/';
      $this->m_uriComponentsResource='/resource';
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Environment
     */
    public static function current()
    {
      return self::$m_current;
    }

    /**
     * @param \Components\Environment $environment_
     *
     * @return \Components\Environment
     */
    public static function push(Environment $environment_)
    {
      array_push(self::$m_stack, $environment_);

      return self::$m_current=end(self::$m_stack);
    }

    /**
     * @return \Components\Environment
     */
    public static function pop()
    {
      $environment=array_pop(self::$m_stack);
      self::$m_current=end(self::$m_stack);

      return $environment;
    }

    /**
     * @param string $name_
     *
     * @return \Components\Environment
     *
     * @throws \ComponentsRuntime_Exception
     */
    public static function valueOf($name_)
    {
      $types=array_flip(self::$m_names);

      if(false===isset($types[$name_]))
        throw new Exception_IllegalArgument('components/environment', sprintf('Passed environment name is not valid [%s].', $name_));

      return new static($types[$name_]);
    }

    /**
     * Creates and returns a new empty instance.
     *
     * Expected stage can be defined via one of:
     * <li>Constant COMPONENTS_ENV</li>
     *
     * Returns a LIVE instance if no stage is defined.
     *
     * @return \Components\Environment
     */
    public static function create()
    {
      $types=array_flip(self::$m_names);
      $name=strtolower(COMPONENTS_ENV);

      if(isset($types[$name]))
        return new static($types[$name]);

      return new static(self::LIVE);
    }

    /**
     * @return \Components\Environment
     */
    public static function LIVE()
    {
      return new static(self::LIVE);
    }

    /**
     * @return \Components\Environment
     */
    public static function DEV()
    {
      return new static(self::DEV);
    }

    /**
     * @return \Components\Environment
     */
    public static function ALPHA()
    {
      return new static(self::ALPHA);
    }

    /**
     * @return \Components\Environment
     */
    public static function BETA()
    {
      return new static(self::BETA);
    }

    /**
     * @return \Components\Environment
     */
    public static function GAMMA()
    {
      return new static(self::GAMMA);
    }

    /**
     * @return boolean
     */
    public static function isDev()
    {
      return self::DEV===self::$m_current->m_type;
    }

    /**
     * @return boolean
     */
    public static function isLive()
    {
      return self::LIVE===self::$m_current->m_type;
    }

    /**
     * @return boolean
     */
    public static function isAlpha()
    {
      return self::ALPHA===self::$m_current->m_type;
    }

    /**
     * @return boolean
     */
    public static function isBeta()
    {
      return self::BETA===self::$m_current->m_type;
    }

    /**
     * @return boolean
     */
    public static function isGamma()
    {
      return self::GAMMA===self::$m_current->m_type;
    }

    /**
     * @return string
     */
    public static function pathApplication()
    {
      return self::$m_current->m_pathApplication;
    }

    /**
     * @return string
     */
    public static function pathComponents()
    {
      return self::$m_current->m_pathComponents;
    }

    /**
     * @return string
     */
    public static function pathConfigGlobal($file_=null)
    {
      if(null===$file_)
        return self::$m_current->m_pathConfig;

      return self::$m_current->m_pathConfig."/$file_";
    }

    /**
     * @return string
     */
    public static function pathConfigLocal($file_=null)
    {
      if(null===$file_)
        return self::$m_current->m_pathConfig.'/'.self::$m_current->m_name;

      return self::$m_current->m_pathConfig.'/'.self::$m_current->m_name."/$file_";
    }

    /**
     * @param string $component_
     *
     * @return string
     */
    public static function pathComponent($component_)
    {
      return self::$m_current->m_pathComponents."/$component_";
    }

    /**
     * @param string $component_
     *
     * @return string
     */
    public static function pathComponentConfig($component_, $file_='default.php')
    {
      return self::$m_current->m_pathComponents."/$component_/config/$file_";
    }

    /**
     * @param string $component_
     * @param string... $path0_
     *
     * @return string
     */
    public static function pathComponentsResource($component_, $path0_=null/*, $path1_...*/)
    {
      return self::$m_current->m_pathComponents.'/'.implode('/', func_get_args());
    }

    /**
     * @param string... $path_
     *
     * @return string
     */
    public static function pathResource($path_=null/*, $path1_...*/)
    {
      if(0===func_num_args())
        return self::$m_current->m_pathResource;

      return self::$m_current->m_pathResource.'/'.ltrim(implode('/', func_get_args()), '/');
    }

    /**
     * @param string... $path_
     *
     * @return string
     */
    public static function pathWeb($path_=null/*, $path1_...*/)
    {
      if(0===func_num_args())
        return self::$m_current->m_pathWeb;

      return self::$m_current->m_pathWeb.'/'.ltrim(implode('/', func_get_args()), '/');
    }

    /**
     * @param string $path_
     *
     * @return string
     */
    public static function uriResource($path0_=null/*, $path1_...*/)
    {
      if(0===func_num_args())
        return self::$m_current->m_uriResource;

      return rtrim(self::$m_current->m_uriResource, '/').'/'.ltrim(implode('/', func_get_args()), '/');
    }

    /**
     * @param string $path_
     *
     * @return string
     */
    public static function uriComponents($path0_=null/*, $path1_...*/)
    {
      if(0===func_num_args())
        return self::$m_current->m_uriComponents;

      return rtrim(self::$m_current->m_uriComponents, '/').'/'.ltrim(implode('/', func_get_args()), '/');
    }

    /**
     * @param string $path_
     *
     * @return string
     */
    public static function uriComponentsEmbedded($path0_=null/*, $path1_...*/)
    {
      if(0===func_num_args())
        return self::$m_current->m_uriComponentsEmbedded;

      return rtrim(self::$m_current->m_uriComponentsEmbedded, '/').'/'.ltrim(implode('/', func_get_args()), '/');
    }


    /**
     * @param string $path_
     *
     * @return string
     */
    public static function uriComponentsResource($path0_=null/*, $path1_...*/)
    {
      if(0===func_num_args())
        return self::$m_current->m_uriComponentsResource;

      return rtrim(self::$m_current->m_uriComponentsResource, '/').'/'.ltrim(implode('/', func_get_args()), '/');
    }

    /**
     * @return string
     */
    public static function uriComponentsResourceLibstd()
    {
      return static::uriComponentsResource(\js\libstdJsLocation());
    }

    /**
     * @param string $file_
     */
    public static function includeConfig($file_)
    {
      if(is_file($file=(self::$m_current->m_pathConfig."/$file_")))
        @include_once $file;
      if(is_file($file=(self::$m_current->m_pathConfig.'/'.self::$m_current->m_name."/$file_")))
        @include_once $file;
    }

    /**
     * @param string $file_
     */
    public static function includeComponentConfig($component_, $file_='default.php')
    {
      if(is_file($file=self::$m_current->m_pathComponents."/$component_/config/$file_"))
        @include_once $file;
      if(is_file($file=self::$m_current->m_pathConfig."/$component_.php"))
        @include_once $file;
      if(is_file($file=self::$m_current->m_pathConfig."/".self::$m_current->m_name."/$component_.php"))
        @include_once $file;
    }

    /**
     * @param boolean $embedded_
     *
     * @return boolean
     */
    public static function isEmbedded($embedded_=null)
    {
      if(null===$embedded_)
        return self::$m_embedded;

      if(true===$embedded_)
        return self::$m_embedded=true;

      return self::$m_embedded=false;
    }

    /**
     * @param string  $environment_
     *
     * @return integer
     */
    public static function logLevelDefault($environment_=null)
    {
      if(null==$environment_)
        $environment_=COMPONENTS_ENV;

      $types=array_flip(self::$m_names);

      if(isset($types[$environment_]) && 1<$types[$environment_])
        return Log::DEBUG;

      return Log::INFO;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return string
     */
    public function getName()
    {
      return $this->m_name;
    }

    /**
     * @return integer
     */
    public function getType()
    {
      return $this->m_type;
    }

    /**
     * @return string
     */
    public function getPathApplication()
    {
      return $this->m_pathApplication;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setPathApplication($path_)
    {
      $this->m_pathApplication=$path_;

      return $this;
    }

    /**
     * @return string
     */
    public function getPathComponents()
    {
      return $this->m_pathComponents;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setPathComponents($path_)
    {
      $this->m_pathComponents=$path_;

      return $this;
    }

    /**
     * @return string
     */
    public function getPathConfig()
    {
      return $this->m_pathConfig;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setPathConfig($path_)
    {
      $this->m_pathConfig=$path_;
      $this->m_pathConfigLocal=$path_;

      return $this;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setPathConfigLocal($path_)
    {
      $this->m_pathConfigLocal=$path_;

      return $this;
    }

    /**
     * @return string
     */
    public function getPathResource()
    {
      return $this->m_pathResource;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setPathResource($path_)
    {
      $this->m_pathResource=$path_;

      return $this;
    }

    /**
     * @return string
     */
    public function getPathWeb()
    {
      return $this->m_pathWeb;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setPathWeb($uri_)
    {
      $this->m_pathWeb=$uri_;

      return $this;
    }

    /**
     * @return string
     */
    public function getUriResource()
    {
      return $this->m_uriResource;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setUriResource($uri_)
    {
      $this->m_uriResource=$uri_;

      return $this;
    }

    /**
     * @return string
     */
    public function getUriComponents()
    {
      return $this->m_uriComponents;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setUriComponents($uri_)
    {
      $this->m_uriComponents=$uri_;

      return $this;
    }

    /**
     * @return string
     */
    public function getUriComponentsEmbedded()
    {
      return $this->m_uriComponentsEmbedded;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setUriComponentsEmbedded($uri_)
    {
      $this->m_uriComponentsEmbedded=$uri_;

      return $this;
    }

    /**
     * @return string
     */
    public function getUriComponentsResource()
    {
      return $this->m_uriComponentsResource;
    }

    /**
     * @param string $path_
     *
     * @return \Components\Environment
     */
    public function setUriComponentsResource($uri_)
    {
      $this->m_uriComponentsResource=$uri_;

      return $this;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Value_String::value() value
     */
    public function value()
    {
      return $this->m_name;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hashs($this->m_name);
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_type===$object_->m_type;

      return false;
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return $this->m_name;
    }

    /**
     * @see \Components\Cloneable::__clone() __clone
     */
    public function __clone()
    {
      return new self($this->m_type);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string[]
     */
    private static $m_names=[
      self::ALPHA=>'alpha',
      self::BETA=>'beta',
      self::DEV=>'dev',
      self::GAMMA=>'gamma',
      self::LIVE=>'live'
    ];
    /**
     * @var \Components\Environment[]
     */
    private static $m_stack=[];
    /**
     * @var boolean
     */
    private static $m_embedded=false;
    /**
     * @var \Components\Environment
     */
    private static $m_current;
    /**
     * @var string
     */
    private $m_name;
    /**
     * @var integer
     */
    private $m_type;
    /**
     * @var string
     */
    private $m_pathApplication;
    /**
     * @var string
     */
    private $m_pathComponents;
    /**
     * @var string
     */
    private $m_pathConfig;
    /**
     * @var string
     */
    private $m_pathResource;
    /**
     * @var string
     */
    private $m_pathWeb;
    /**
     * @var string
     */
    private $m_uriResource;
    /**
     * @var string
     */
    private $m_uriComponents;
    /**
     * @var string
     */
    private $m_uriComponentsEmbedded;
    /**
     * @var string
     */
    private $m_uriComponentsResource;
    //--------------------------------------------------------------------------
  }
?>
