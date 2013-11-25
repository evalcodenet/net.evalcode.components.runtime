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

    /**
     * TODO [CSH] Refactor to a single & useful append method.
     *
     * @param integer $severity_
     * @param string $message_
     * @param string[] $lines_
     */
    function appendGroup($severity_, $message_, array $lines_);

    function clear();
    function flush();
    //--------------------------------------------------------------------------
  }
?>
