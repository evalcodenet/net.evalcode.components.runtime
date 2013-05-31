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
     * Returns indentical duplicate of this object.
     *
     * @return mixed
     */
    function __clone();
    //--------------------------------------------------------------------------
  }
?>
