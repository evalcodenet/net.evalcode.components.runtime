<?php


namespace log;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage log
     *
     * @author evalcode.net
     */


    // ACCESSORS
    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed... $args_
     */
    function d($namespace_, $message_/*, $arg0_, $arg1_, ...*/)
    {
      if(\Components\Log::isLevelActive(\Components\Log::DEBUG))
        \Components\Log::current()->append(\Components\Log::DEBUG, func_get_args());
    }

    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed... $args_
     */
    function i($namespace_, $message_/*, $arg0_, $arg1_, ...*/)
    {
      if(\Components\Log::isLevelActive(\Components\Log::INFO))
        \Components\Log::current()->append(\Components\Log::INFO, func_get_args());
    }

    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed... $args_
     */
    function w($namespace_, $message_/*, $arg0_, $arg1_, ...*/)
    {
      if(\Components\Log::isLevelActive(\Components\Log::WARN))
        \Components\Log::current()->append(\Components\Log::WARN, func_get_args());
    }

    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed... $args_
     */
    function e($namespace_, $message_/*, $arg0_, $arg1_, ...*/)
    {
      \Components\Log::current()->append(\Components\Log::ERROR, func_get_args());
    }

    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed... $args_
     */
    function f($namespace_, $message_/*, $arg0_, $arg1_, ...*/)
    {
      \Components\Log::current()->append(\Components\Log::FATAL, func_get_args());
    }
    //--------------------------------------------------------------------------
?>
