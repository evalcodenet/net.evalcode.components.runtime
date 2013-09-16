<?php


namespace Components;


  /**
   * Iterator
   *
   * @api
   * @package net.evalcode.components.type
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
