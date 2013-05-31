<?php


namespace Components;


  /**
   * Enumeration
   *
   * @package net.evalcode.components
   * @subpackage type
   *
   * @author evalcode.net
   */
  // FIXME (CSH) Create empty instance for unserialize/unserializeJson or make unserialize/unserializeJson static!?
  abstract class Enumeration implements Object, Comparable, Value_String, Serializable_Php
  {
    // CONSTRUCTION
    public function __construct($name_)
    {
      $type=get_class($this);

      $this->m_name=constant("$type::$name_");
      $this->m_key=self::$m_enums[$type][$name_];
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $name_
     * @param array $args_
     *
     * @return \Components\Enumeration
     *
     * @throws \Components\Runtime_Exception
     */
    public static function __callStatic($name_, array $args_=array())
    {
      $type=get_called_class();

      if(__CLASS__===$type)
      {
        throw new Runtime_Exception('runtime/type/enumeration',
          'Enumeration can not be invoked directly.'
        );
      }

      if(isset(self::$m_enumInstances[$type][$name_]))
        return self::$m_enumInstances[$type][$name_];

      if(false===isset(self::$m_enums[$type]))
      {
        if(false===self::$m_initialized)
        {
          if(false===(self::$m_enums=Cache::get('components/runtime/type/enumeration')))
            self::$m_enums=array();
        }

        if(false===array_key_exists($type, self::$m_enums))
        {
          self::$m_enums[$type]=array_flip(static::values());

          Cache::set('components/runtime/type/enumeration', self::$m_enums);
        }
      }

      if(false===array_key_exists($name_, self::$m_enums[$type]))
      {
        $trace=debug_backtrace(false);
        $caller=$trace[1];

        throw new Runtime_Exception('runtime/type/enumeration', sprintf(
          'Call to undefined method %1$s::%2$s() in %3$s on line %4$d.',
            $type,
            $name_,
            $caller['file'],
            $caller['line']
        ));
      }

      if(0<count($args_))
      {
        array_unshift($args_, $name_);
        $class=new \ReflectionClass($type);

        return $class->newInstanceArgs($args_);
      }

      // Cache simple enum instances.
      return self::$m_enumInstances[$type][$name_]=new $type($name_);
    }

    /**
     * @param string $name_
     *
     * @return boolean
     */
    public static function contains($name_)
    {
      return array_key_exists($name_, self::$m_enums[get_called_class()]);
    }

    /**
     * @param string $name_
     *
     * @return boolean
     */
    public static function containsKey($name_)
    {
      return array_key_exists($name_, self::$m_enums[get_called_class()]);
    }

    /**
     * @param string $name_
     *
     * @return \Components\Enumeration
     */
    public static function valueOf($name_)
    {
      return static::$name_();
    }

    /**
     * @return array|string
     */
    public static function values()
    {
      throw new Exception_Abstract_Method('components/runtime/type/enumeration', 'Abstract method.');
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return string
     */
    public function name()
    {
      return $this->m_name;
    }

    /**
     * @return string
     */
    public function key()
    {
      return $this->m_key;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components\Comparable::compareTo()
     */
    public function compareTo($object_)
    {
      if($object_ instanceof self)
      {
        if($this->m_key===$object_->m_key)
          return 0;

        return $this->m_key<$object_->m_key?-1:1;
      }

      return false;
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
     * @see Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_key===$object_->m_key;

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
     */
    public function __toString()
    {
      return $this->m_name;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable_Php::__sleep()
     */
    public function __sleep()
    {
      return array('m_name');
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable_Php::__wakeup()
     */
    public function __wakeup()
    {
      $type=get_class($this);
      $this->m_key=self::$m_enums[$type][$this->m_name];
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable::serialVersionUid()
     */
    public function serialVersionUid()
    {
      return 1;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Value_String::value()
     */
    public function value()
    {
      return $this->m_name;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    private static $m_initialized=false;
    /**
     * @var array
     */
    private static $m_enums=array();
    /**
     * @var array|Components\Enumeration
     */
    private static $m_enumInstances=array();

    protected $m_name;
    protected $m_key;
    //--------------------------------------------------------------------------
  }
?>
