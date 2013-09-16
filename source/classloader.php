<?php


namespace Components;


  /**
   * Classloader
   *
   * <p>
   *   Components runtime classloader API.
   * </p>
   *
   * @api
   * @package net.evalcode.components.classloader
   *
   * @author evalcode.net
   *
   * @see \Components\Classloader_Embedded \Components\Classloader_Embedded
   * @see \Components\Classloader_Standalone \Components\Classloader_Standalone
   */
  interface Classloader extends Object
  {
    // ACCESSORS
    /**
     * @return string[]
     */
    function getClasspaths();
    /**
     * @param string $class_
     */
    function loadClass($class_);
    /**
     * Triggered during bootstrap.
     */
    function initialize();
    //--------------------------------------------------------------------------
  }
?>
