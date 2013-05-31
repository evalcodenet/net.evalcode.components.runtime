<?php


namespace Components;


  /**
   * Serializable_Php
   *
   * @package net.evalcode.components
   * @subpackage type.serializable
   *
   * @author evalcode.net
   */
  interface Serializable_Php extends Serializable
  {
    // ACCESSORS
    /**
     * @return string
     */
    function __sleep();

    /**
     * @return Serializable_Php
     */
    function __wakeup();
    //--------------------------------------------------------------------------
  }
?>
