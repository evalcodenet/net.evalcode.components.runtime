<?php


namespace Components;


  /**
   * Iterable
   *
   * @package net.evalcode.components
   * @subpackage type
   *
   * @author evalcode.net
   */
  interface Iterable extends \IteratorAggregate
  {
    // ACCESSORS
    /**
     * @return \Components\Iterator
     */
    function getIterator();
    //--------------------------------------------------------------------------
  }
?>
