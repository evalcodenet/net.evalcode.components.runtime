<?php


namespace Components;


  /**
   * Value_Float
   *
   * @package net.evalcode.components
   * @subpackage type.value
   *
   * @author evalcode.net
   */
  interface Value_Float extends Value
  {
    // ACCESSORS
    /**
     * (non-PHPdoc)
     * @see Components\Value::value()
     *
     * @return float
     */
    function value();

    /**
     * (non-PHPdoc)
     * @see Components\Value::valueOf()
     *
     * @param float $value_
     */
    static function valueOf($value_);
    //--------------------------------------------------------------------------
  }
?>
