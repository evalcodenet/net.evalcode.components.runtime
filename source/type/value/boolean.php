<?php


namespace Components;


  /**
   * Value_Boolean
   *
   * @package net.evalcode.components
   * @subpackage type.value
   *
   * @author evalcode.net
   */
  interface Value_Boolean extends Value
  {
    // ACCESSORS
    /**
     * (non-PHPdoc)
     * @see Components\Value::value()
     *
     * @return boolean
     */
    function value();

    /**
     * (non-PHPdoc)
     * @see Components\Value::valueOf()
     *
     * @param boolean $value_
     */
    static function valueOf($value_);
    //--------------------------------------------------------------------------
  }
?>
