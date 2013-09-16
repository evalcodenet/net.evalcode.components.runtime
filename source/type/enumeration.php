<?php


namespace Components;


  /**
   * Enumeration
   *
   * @api
   * @package net.evalcode.components.type
   *
   * @author evalcode.net
   */
  // FIXME (CSH) Create empty instance for unserialize/unserializeJson or make unserialize/unserializeJson static!?
  abstract class Enumeration implements Object, Comparable, Value_String, Serializable_Php
  {
    // CONSTRUCTION
    public function __construct($key_, $name_)
    {
      $this->m_name=$name_;
      $this->m_key=$key_;
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
        self::initializeType($type);

      if(false===array_key_exists($name_, self::$m_enums[$type]))
      {
        $trace=debug_backtrace(false);
        $caller=$trace[1];

        throw new Runtime_Exception('components/type/enumeration', sprintf(
          'Call to undefined method %1$s::%2$s() in %3$s on line %4$d.',
            $type,
            $name_,
            $caller['file'],
            $caller['line']
        ));
      }

      if(!$concrete=Runtime_Classloader::lookup(String::typeToNamespace($type).'/'.String::typeToPath($name_)))
        $concrete=$type;

      if(0<count($args_))
      {
        array_unshift($args_, constant("$type::$name_"));
        array_unshift($args_, self::$m_enums[$type][$name_]);

        $class=new \ReflectionClass($concrete);

        return $class->newInstanceArgs($args_);
      }

      // Cache simple enum instances.
      // XXX Instances MUST be state-less.
      return self::$m_enumInstances[$type][$name_]=new $concrete(
        self::$m_enums[$type][$name_],
        constant("$type::$name_")
      );
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
     * @return string[]
     */
    public static function values()
    {
      throw new Exception_Abstract_Method('components/type/enumeration', 'Abstract method.');
    }

    /**
     * @param mixed $key_
     *
     * @return \Components\Enumeration
     */
    public static function forKey($key_)
    {
      $type=get_called_class();

      if(__CLASS__===$type)
      {
        throw new Runtime_Exception('components/type/enumeration',
          'Enumeration can not be invoked directly.'
        );
      }

      if(false===isset(self::$m_enums[$type]))
        self::initializeType($type);

      $names=array_flip(self::$m_enums[$type]);
      if(false===isset($names[$key_]))
        return null;

      return static::valueOf($names[$key_]);
    }

    /**
     * @param string $name_
     *
     * @return mixed
     *
     * @throws \Components\Runtime_Exception
     */
    public static function keyForName($name_)
    {
      $type=get_called_class();

      if(__CLASS__===$type)
      {
        throw new Runtime_Exception('components/type/enumeration',
          'Enumeration can not be invoked directly.'
        );
      }

      if(false===isset(self::$m_enums[$type]))
        self::initializeType($type);

      if(false===array_key_exists($name_, self::$m_enums[$type]))
        return null;

      return self::$m_enums[$type][$name_];
    }

    /**
     * @param mixed $key_
     *
     * @return string
     *
     * @throws \Components\Runtime_Exception
     */
    public static function nameForKey($key_)
    {
      $type=get_called_class();

      if(__CLASS__===$type)
      {
        throw new Runtime_Exception('components/type/enumeration',
          'Enumeration can not be invoked directly.'
        );
      }

      if(false===isset(self::$m_enums[$type]))
        self::initializeType($type);

      $names=array_flip(self::$m_enums[$type]);

      if(false===isset($names[$key_]))
        return null;

      return $names[$key_];
    }

    /**
     * @param string $name_
     *
     * @return boolean
     */
    public static function contains($name_)
    {
      $type=get_called_class();

      if(__CLASS__===$type)
      {
        throw new Runtime_Exception('components/type/enumeration',
          'Enumeration can not be invoked directly.'
        );
      }

      if(false===isset(self::$m_enums[$type]))
        self::initializeType($type);

      return array_key_exists($name_, self::$m_enums[$type]);
    }

    /**
     * @param string $key_
     *
     * @return boolean
     */
    public static function containsKey($key_)
    {
      $type=get_called_class();

      if(__CLASS__===$type)
      {
        throw new Runtime_Exception('components/type/enumeration',
          'Enumeration can not be invoked directly.'
        );
      }

      if(false===isset(self::$m_enums[$type]))
        self::initializeType($type);

      return in_array($key_, self::$m_enums[$type]);
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
     * @see \Components\Comparable::compareTo() \Components\Comparable::compareTo()
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
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return string_hash($this->m_name);
    }

    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_key===$object_->m_key;

      return false;
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return $this->m_name;
    }

    /**
     * @see \Components\Serializable_Php::__sleep() \Components\Serializable_Php::__sleep()
     */
    public function __sleep()
    {
      return array('m_key', 'm_name');
    }

    /**
     * @see \Components\Serializable_Php::__wakeup() \Components\Serializable_Php::__wakeup()
     */
    public function __wakeup()
    {

    }

    /**
     * (non-PHPdoc)
     * @see \Components\Serializable::serialVersionUid() \Components\Serializable::serialVersionUid()
     */
    public function serialVersionUid()
    {
      return 1;
    }

    /**
     * @see \Components\Value_String::value() \Components\Value_String::value()
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
     * @var \Components\Enumeration[]
     */
    private static $m_enumInstances=array();

    protected $m_name;
    protected $m_key;
    //----


    // HELPERS
    private static function initializeType($type_)
    {
      if(false===self::$m_initialized)
      {
        if(false===(self::$m_enums=Cache::get('components/type/enumeration')))
          self::$m_enums=array();

        self::$m_initialized=true;
      }

      if(false===array_key_exists($type_, self::$m_enums))
      {
        self::$m_enums[$type_]=array_flip(static::values());

        Cache::set('components/type/enumeration', self::$m_enums);
      }
    }
    //--------------------------------------------------------------------------
  }
?>
