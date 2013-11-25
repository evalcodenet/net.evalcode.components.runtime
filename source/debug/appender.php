<?php


namespace Components;


  /**
   * Debug_Appender
   *
   * @api
   * @package net.evalcode.components.debug
   *
   * @author evalcode.net
   */
  interface Debug_Appender extends Object
  {
    // ACCESSORS/MUTATORS
    /**
     * @param integer $severity_
     * @param mixed[] $args_
     */
    function append($severity_, array $args_);

    function clear();
    function flush();
    //--------------------------------------------------------------------------
  }
?>
