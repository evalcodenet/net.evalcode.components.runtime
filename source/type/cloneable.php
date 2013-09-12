<?php


namespace Components;


  /**
   * Cloneable
   *
   * @api
   * @package net.evalcode.components.type
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
