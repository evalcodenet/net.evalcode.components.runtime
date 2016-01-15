<?php


namespace Components;


  /**
   * Log_Appender
   *
   * @api
   * @package net.evalcode.components.log
   *
   * @author evalcode.net
   *
   * @property string $name
   * @property string $host
   * @property string $level
   * @property string $pattern
   * @property string $patternDate
   */
  interface Log_Appender extends Object
  {
    // ACCESSORS
    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    function debug($namespace_, $message_/*, $arg0_, $arg1_, ..*/);
    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    function info($namespace_, $message_/*, $arg0_, $arg1_, ..*/);
    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    function warn($namespace_, $message_/*, $arg0_, $arg1_, ..*/);
    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    function error($namespace_, $message_/*, $arg0_, $arg1_, ..*/);
    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    function fatal($namespace_, $message_/*, $arg0_, $arg1_, ..*/);
    /**
     * @param integer $level_
     * @param array $args_
     *
     * @internal
     */
    function append($level_, array $args_=[]);

    /**
     * Internal initialization.
     *
     * @internal
     */
    function initialize();
    //--------------------------------------------------------------------------
  }
?>
