<?php


namespace Components;


  /**
   * Serializable_Php
   *
   * @api
   * @package net.evalcode.components.type
   * @subpackage serializable
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
     * @return \Components\Serializable_Php
     */
    function __wakeup();
    //--------------------------------------------------------------------------
  }
?>
