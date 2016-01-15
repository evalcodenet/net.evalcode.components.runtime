<?php


namespace Components;


  /**
   * Properties
   *
   * @api
   * @package net.evalcode.components.type
   *
   * @author evalcode.net
   *
   * @property mixed *
   * @method Properties *
   */
  // TODO Implement \Components\Collection_Mutable.
  class Properties implements Collection, Cloneable, Serializable_Php, Serializable_Json
  {
    // CONSTRUCTION
    public function __construct(array &$properties_=[])
    {
      $this->m_properties=&$properties_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Collection::arrayValue() arrayValue
     */
    public function arrayValue()
    {
      return $this->m_properties;
    }

    /**
     * @see \Components\Collection::isEmpty() isEmpty
     */
    public function isEmpty()
    {
      return 0===count($this->m_properties);
    }

    /**
     * @see \Components\Collection_Mutable::clear() clear
     */
    public function clear()
    {
      $this->m_properties=[];
    }

    /**
     * @see \Components\Countable::count() count
     */
    public function count()
    {
      return count($this->m_properties);
    }

    public function __get($name_)
    {
      if(isset($this->m_properties[$name_]))
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

    public function __call($name_, array $args_=[])
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
     * @see \Components\Cloneable::__clone() __clone
     */
    public function __clone()
    {
      // FIXME Evaluate performant solution for deep cloning ...
      return unserialize(serialize($this));
    }

    /**
     * @see \Components\Serializable_Json::serializeJson() serializeJson
     */
    public function serializeJson()
    {
      return json_encode($this->m_properties);
    }

    /**
     * @see \Components\Serializable_Json::unserializeJson() unserializeJson
     *
     * @return Compoents\Properties
     */
    public function unserializeJson($json_)
    {
      $this->m_properties=json_decode($json_, true);

      return $this;
    }

    /**
     * @see \Components\Serializable_Php::__sleep() __sleep
     */
    public function __sleep()
    {
      return ['m_properties'];
    }

    /**
     * @see \Components\Serializable_Php::__wakeup() __wakeup
     */
    public function __wakeup()
    {

    }

    /**
     * @see \Components\Serializable::serialVersionUid() serialVersionUid
     */
    public function serialVersionUid()
    {
      return 1;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::equals() equals)
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
      return sprintf('%s@%s{}', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array
     */
    protected $m_properties=[];
    //--------------------------------------------------------------------------
  }
?>
