<?php


namespace Components;


  /**
   * Value_Double
   *
   * @package net.evalcode.components
   * @subpackage type.value
   *
   * @author evalcode.net
   */
  interface Value_Double extends Value
  {
    // ACCESSORS
    /**
     * (non-PHPdoc)
     * @see Components\Value::value()
     *
     * @return double
     */
    function value();

    /**
     * (non-PHPdoc)
     * @see Components\Value::valueOf()
     *
     * @param double $value_
     */
    static function valueOf($value_);
    //--------------------------------------------------------------------------
  }
?>
