<?php


namespace Components;


  /**
   * Manifest
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  class Manifest extends Properties implements Value_String
  {
    // PREDEFINED PROPERTIES
    const FILE_MANIFEST='.manifest';

    const SOURCE_TYPE_MAIN='main';
    const SOURCE_TYPE_TEST_UNIT='test.unit';
    const SOURCE_TYPE_TEST_INTEGRATION='test.integration';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($component_)
    {
      parent::__construct();

      $this->m_component=$component_;
      $this->m_path=Environment::pathComponent($component_);
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $name_
     *
     * @return \Components\Manifest
     */
    public static function forComponent($name_)
    {
      if(false===isset(self::$m_instances[$name_]))
        self::$m_instances[$name_]=new self($name_);

      return self::$m_instances[$name_];
    }

    /**
     * @see \Components\Value_String::valueOf() \Components\Value_String::valueOf()
     *
     * @return \Components\Manifest
     */
    public static function valueOf($value_)
    {
      return static::forComponent($value_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return string
     */
    public function getPath($subPath0_/*, $subPath1_, $subPath2_, ..*/)
    {
      if(0===func_num_args())
        return $this->initialized()->m_path;

      return $this->initialized()->m_path.DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, func_get_args());
    }

    /**
     * @return string
     */
    public function getProduct()
    {
      return $this->initialized()->product;
    }

    /**
     * @return string
     */
    public function getPackage()
    {
      return $this->initialized()->package;
    }

    /**
     * @return Version
     */
    public function getVersion()
    {
      if(false===isset(self::$m_versions[$this->m_component]))
        self::$m_versions[$this->m_component]=Version::parse($this->initialized()->version);

      return self::$m_versions[$this->m_component];
    }

    /**
     * @return string
     */
    public function getVendor()
    {
      return $this->initialized()->vendor;
    }

    /**
     * @return \Components\Manifest[]
     */
    public function getDependencies()
    {
      $dependencies=array();
      foreach($this->initialized()->dependencies as $name)
      {
        if(false===isset(self::$m_instances[$name]))
          self::$m_instances[$name]=static::forComponent($name);

        $dependencies[$name]=&self::$m_instances[$name];
      }

      return $dependencies;
    }

    /**
     * @param string $type_
     *
     * @return string
     */
    public function getNamespace($type_=self::SOURCE_TYPE_MAIN)
    {
      if(false===isset($this->initialized()->source[$type_]['namespace']))
        return null;

      return $this->initialized()->source[$type_]['namespace'];
    }

    /**
     * @param string $type_
     *
     * @return string
     */
    public function getClasspath($type_=self::SOURCE_TYPE_MAIN)
    {
      if(false===isset($this->initialized()->source[$type_]['path']))
        return null;

      return $this->initialized()->m_path.DIRECTORY_SEPARATOR.$this->initialized()->source[$type_]['path'];
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Value_String::value() \Components\Value_String::value()
     */
    public function value()
    {
      return $this->m_component;
    }

    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return string_hash($this->m_component);
    }

    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_component===$object_->m_component;

      return false;
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{name: %s, initialized: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_component,
        $this->m_initialized?'true':'false'
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Manifest[]
     */
    private static $m_instances=array();
    /**
     * @var \Components\Version[]
     */
    private static $m_versions=array();
    /**
     * @var string
     */
    private $m_component;
    /**
     * @var string
     */
    private $m_path;
    /**
     * @var boolean
     */
    private $m_initialized=false;
    //-----


    /**
     * @return \Components\Manifest
     *
     * @throws \Components\Runtime_Exception
     */
    private function initialized()
    {
      if(false===$this->m_initialized)
      {
        if(false===($json=Cache::get("components/manifest/{$this->m_component}")))
        {
          $file=$this->m_path.DIRECTORY_SEPARATOR.self::FILE_MANIFEST;

          if(false===is_file($file))
          {
            throw new Runtime_Exception('manifest', sprintf(
              'Unable to resolve component manifest [path: %s].', $file
            ));
          }

          $json=file_get_contents($file);

          Cache::set("components/manifest/{$this->m_component}", $json);
        }

        $this->unserializeJson($json);

        $this->m_initialized=true;
      }

      return $this;
    }
    //--------------------------------------------------------------------------
  }
?>
