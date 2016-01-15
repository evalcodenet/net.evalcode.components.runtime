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
      parent::__construct();

      $this->m_component=$component_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $name_
     * @param scalar[] $args_
     *
     * @return \Components\Config
     */
    public static function __callstatic($name_, array $args_=[])
    {
      if(0===count($args_))
        return static::get($name_);

      return static::get($name_)->{$args_[0]};
    }

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
     * @see \Components\Value_String::valueOf() valueOf
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

        if(isset($this->validate) && (($validate=$this->validate) instanceof \Closure))
        {
          $validate->bindTo($this);
          $validate();
        }
      }

      $this->m_loaded=true;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Value_String::value() value
     */
    public function value()
    {
      return $this->m_component;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Config[]
     */
    private static $m_instances=[];

    /**
     * @var boolean
     */
    private $m_loaded=false;
    /**
     * @var string
     */
    private $m_component;
    //--------------------------------------------------------------------------
  }
?>
