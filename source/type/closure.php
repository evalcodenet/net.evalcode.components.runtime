<?php


namespace Components;


  /**
   * Closure
   *
   * @api
   * @package net.evalcode.components.type
   *
   * @author evalcode.net
   */
  interface Closure
  {
    /**
     * Invoke closure.
     *
     * @return mixed
     */
    function __invoke();
    //--------------------------------------------------------------------------
  }
?>
