<?php


  /**
   * Tmo_ClassLoader
   *
   * @package net.evalcode.components
   * @subpackage core
   *
   * @author evalcode.net
   */
  interface Tmo_ClassLoader
  {
    // ACCESSORS/MUTATORS
    function load($clazz_);

    function initialize();
    //--------------------------------------------------------------------------
  }
?>
