<?php


namespace Components;


  /**
   * Cloneable
   *
   * @package net.evalcode.components
   * @subpackage type
   *
   * @author evalcode.net
   */
  interface Cloneable
  {
    // ACCESSORS
    /**
     * Returns instance of identical type & state to the invoked one.
     *
     * @return mixed
     */
    function __clone();
    //--------------------------------------------------------------------------
  }
?>
