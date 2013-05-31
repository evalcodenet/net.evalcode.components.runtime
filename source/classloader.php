<?php


namespace Components;


  /**
   * Classloader
   *
   * <p>
   *   Components runtime classloader API.
   * </p>
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   *
   * @see Components\Classloader_Embedded
   * @see Components\Classloader_Standalone
   */
  interface Classloader extends Object
  {
    // ACCESSORS
    /**
     * @return string|array
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
