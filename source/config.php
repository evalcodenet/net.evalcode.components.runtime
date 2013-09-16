<?php


namespace Components;


  /**
   * Config
   *
   * @api
   * @package net.evalcode.components.runtime
   *
   * @author evalcode.net
   */
  class Config extends Properties implements Value_String
  {
    // CONSTRUCTION
    public function __construct($component_)
    {
      $this->m_component=$component_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $component_
     *
     * @return \Components\Config
     */
    public static function get($component_)
    {
      if(false===isset(self::$m_instances[$component_]))
      {
        self::$m_instances[$component_]=new static($component_);
        self::$m_instances[$component_]->load();
      }

      return self::$m_instances[$component_];
    }

    /**
     * @see \Components\Value_String::valueOf() \Components\Value_String::valueOf()
     *
     * @return \Components\Config
     */
    public static function valueOf($component_)
    {
      return static::get($component_);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function load()
    {
      if(false===$this->m_loaded)
      {
        if(is_file($file=Environment::pathComponentConfig($this->m_component)))
          include_once $file;
        if(is_file($file=Environment::pathConfigGlobal("{$this->m_component}.php")))
          include_once $file;
        if(is_file($file=Environment::pathConfigLocal("{$this->m_component}.php")))
          include_once $file;
      }

      $this->m_loaded=true;
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
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_instances=array();

    private $m_loaded=false;
    private $m_component;
    //--------------------------------------------------------------------------
  }
?>
