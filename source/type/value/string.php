<?php


namespace Components;


  /**
   * Value_String
   *
   * @package net.evalcode.components
   * @subpackage type.value
   *
   * @author evalcode.net
   */
  interface Value_String extends Value
  {
    // ACCESSORS
    /**
     * (non-PHPdoc)
     * @see Components\Value::value()
     *
     * @return string
     */
    function value();

    /**
     * (non-PHPdoc)
     * @see Components\Value::valueOf()
     *
     * @param string $value_
     */
    static function valueOf($value_);
    //--------------------------------------------------------------------------
  }
?>
