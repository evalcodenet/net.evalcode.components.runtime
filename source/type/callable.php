<?php


namespace Components;


  /**
   * Callable
   *
   * @package net.evalcode.components
   * @subpackage type
   *
   * @author evalcode.net
   */
  interface Callable /* extends \Closure (\Callable with PHP 5.4) */
  {
    /**
     * Invoke callable.
     *
     * @return mixed
     */
    function __invoke();
    //--------------------------------------------------------------------------
  }
?>
