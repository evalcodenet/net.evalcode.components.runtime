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
     * @param mixed $arg_
     *
     * @return \Components\Ns
     */
    public static function of($arg_)
    {
      if(is_object($arg_))
        return new self(get_class($arg_));

      return new self(gettype($arg_));
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return string
     */
    public function name()
    {
      if(null===$this->m_name)
        $this->m_name=strtolower(strtr($this->m_type, '\\_', '//'));

      return $this->m_name;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return string_hash($this->m_type);
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
      return $this->name();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_type;
    private $m_name;
    //--------------------------------------------------------------------------
  }
?>
