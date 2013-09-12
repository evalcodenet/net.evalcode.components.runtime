<?php


namespace Components;


  /**
   * Ns
   *
   * @api
   * @package net.evalcode.components.type
   *
   * @author evalcode.net
   */
  class Ns implements Object
  {
    // CONSTRUCTION
    public function __construct($type_)
    {
      $this->m_type=$type_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param mixed $object_
     *
     * @return \Components\Ns
     */
    public static function of($object_)
    {
      if(is_object($object_))
        return new self(get_class($object_));

      return new self(gettype($object_));
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function name()
    {
      if(null===$this->m_name)
        $this->m_name=strtolower(strtr($this->m_type, '\\_', '//'));

      return $this->m_name;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return string_hash($this->m_type);
    }

    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_type===$object_->m_type;

      return false;
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return $this->name();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_type;
    private $m_name;
    //--------------------------------------------------------------------------
  }
?>
