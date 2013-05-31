<?php


namespace Components;


  /**
   * Iterator
   *
   * @package net.evalcode.components
   * @subpackage type
   *
   * @author evalcode.net
   */
  interface Iterator extends \Iterator
  {
    // ACCESSORS
    function current();
    function key();

    function hasNext();
    function hasPrevious();

    function next();
    function previous();

    function rewind();

    function valid();
    //--------------------------------------------------------------------------
  }
?>
