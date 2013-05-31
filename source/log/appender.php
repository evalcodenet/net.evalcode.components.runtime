<?php


namespace Components;


  /**
   * Log_Appender
   *
   * @package net.evalcode.components
   * @subpackage log
   *
   * @author evalcode.net
   */
  interface Log_Appender extends Object
  {
    // ACCESSORS
    /**
     * @return string
     */
    function name();

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
    function append($level_, array $args_=array());

    /**
     * @return integer
     */
    function level();

    /**
     * Internal initialization.
     */
    function initialize();
    //--------------------------------------------------------------------------
  }
?>
