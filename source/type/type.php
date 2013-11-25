<?php


namespace Components;


  /**
   * Type
   *
   * @api
   * @package net.evalcode.components.type
   *
   * @author evalcode.net
   */
  class Type implements Object
  {
    // CONSTRUCTION
    public function __construct($name_)
    {
      $this->m_name=$name_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param mixed $arg_
     *
     * @return Type
     */
    public static function of($arg_)
    {
      if(is_object($arg_))
        return new self(get_class($arg_));

      return new self(gettype($arg_));
    }

    /**
     * @param string $name_
     *
     * @return Type
     */
    public static function forName($name_)
    {
      return new self($name_);
    }

    /**
     * @param mixed $object_
     * @param string $type_
     *
     * @return boolean
     */
    public static function isInstanceOf($object_, $type_)
    {
      return $object_ instanceof $type_;
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
     * @return \Components\Ns
     */
    public function ns()
    {
      return new Ns($this->m_name);
    }

    /**
     * @param mixed $object_
     *
     * @return boolean
     */
    public function isTypeOf($object_)
    {
      return $object_ instanceof $this->m_name;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_name===$object_->m_name;

      return false;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return string_hash($this->m_name);
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return $this->m_name;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_name;
    //--------------------------------------------------------------------------
  }
?>
