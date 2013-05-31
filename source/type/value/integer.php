<?php


namespace Components;


  /**
   * Value_Integer
   *
   * @package net.evalcode.components
   * @subpackage type.value
   *
   * @author evalcode.net
   */
  interface Value_Integer extends Value
  {
    // ACCESSORS
    /**
     * (non-PHPdoc)
     * @see Components\Value::value()
     *
     * @return integer
     */
    function value();

    /**
     * (non-PHPdoc)
     * @see Components\Value::valueOf()
     *
     * @param integer $value_
     */
    static function valueOf($value_);
    //--------------------------------------------------------------------------
  }
?>
