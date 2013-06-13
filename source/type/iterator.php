<?php


namespace Components;


  /**
   * Iterator
   *
   * @package net.evalcode.components
   * @subpackage type
   *
   * @author evalcode.net
   *
   * @method mixed current
   * @method mixed key
   * @method mixed next
   * @method mixed rewind
   * @method boolean valid
   */
  interface Iterator extends \Iterator
  {
    // ACCESSORS
    function hasNext();
    function hasPrevious();

    function previous();
    //--------------------------------------------------------------------------
  }
?>
