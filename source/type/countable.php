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
  interface Countable extends \Countable
  {
    // ACCESSORS
    /**
     * @return integer
     */
    function count();
    //--------------------------------------------------------------------------
  }
?>
