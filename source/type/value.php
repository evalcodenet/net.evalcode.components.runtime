<?php


namespace Components;


  /**
   * Value
   *
   * @api
   * @package net.evalcode.components.type
   *
   * @author evalcode.net
   */
  interface Value
  {
    // ACCESSORS
    /**
     * Returns a single primitive value that defines
     * the complete state of this object.
     *
     * @return mixed
     */
    function value();
    /**
     * Returns a instance of this type for given value.
     *
     * @param mixed $value_
     */
    static function valueOf($value_);
    //--------------------------------------------------------------------------
  }
?>
