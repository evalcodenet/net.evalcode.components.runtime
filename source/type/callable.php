<?php


namespace Components;


  /**
   * Callable
   *
   * @api
   * @package net.evalcode.components.type
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
