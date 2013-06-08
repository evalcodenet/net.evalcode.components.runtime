<?php


namespace Components;


  /**
   * Properties
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   *
   * @property mixed *
   * @method Properties *
   */
  class Properties implements Collection, Cloneable,
    Serializable_Php, Serializable_Json
  {
    // CONSTRUCTION
    public function __construct(array $properties_=array())
    {
      $this->m_properties=$properties_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components\Collection::arrayValue()
     */
    public function arrayValue()
    {
      return $this->m_properties;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Collection::isEmpty()
     */
    public function isEmpty()
    {
      return 0===count($this->m_properties);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Collection_Mutable::isEmpty()
     *
     * @todo Add interface \Components\Collection_Mutable.
     */
    public function clear()
    {
      $this->m_properties=array();
    }

    /**
     * (non-PHPdoc)
     * @see Components\Countable::count()
     */
    public function count()
    {
      return count($this->m_properties);
    }

    public function __get($name_)
    {
      if(array_key_exists($name_, $this->m_properties))
        return $this->m_properties[$name_];

      return null;
    }

    public function __set($name_, $value_)
    {
      $this->m_properties[$name_]=$value_;

      return $this;
    }

    public function __isset($name_)
    {
      return array_key_exists($name_, $this->m_properties);
    }

    public function __unset($name_)
    {
      if(array_key_exists($name_, $this->m_properties))
        unset($this->m_properties[$name_]);

      return $this;
    }

    public function __call($name_, array $args_=array())
    {
      if(0===count($args_))
      {
        if(array_key_exists($name_, $this->m_properties))
          return $this->m_properties[$name_];

        return null;
      }

      if(1===count($args_))
      {
        if(isset($this->m_properties[$name_][$args_[0]]))
          return $this->m_properties[$name_][$args_[0]];

        return null;
      }

      if(2===count($args_))
        $this->m_properties[$name_][$args_[0]]=$args_[1];

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Cloneable::__clone()
     */
    public function __clone()
    {
      // FIXME Evaluate performant solution for deep cloning ...
      return unserialize(serialize($this));
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable_Json::serializeJson()
     */
    public function serializeJson()
    {
      return json_encode($this->m_properties);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable_Json::unserializeJson()
     *
     * @return Compoents\Properties
     */
    public function unserializeJson($json_)
    {
      $this->m_properties=json_decode($json_, true);

      return $this;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable_Php::__sleep()
     */
    public function __sleep()
    {
      return array('m_properties');
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable_Php::__wakeup()
     */
    public function __wakeup()
    {

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
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{}',
        __CLASS__,
        $this->hashCode()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array|mixed
     */
    protected $m_properties=array();
    //--------------------------------------------------------------------------
  }
?>