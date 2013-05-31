<?php


namespace Components;


  /**
   * Collection
   *
   * @package net.evalcode.components
   * @subpackage type
   *
   * @author evalcode.net
   */
  interface Collection extends Object, Countable
  {
    // ACCESSORS
    /**
     * Determines whether this collection is empty.
     *
     * @return boolean
     */
    function isEmpty();

    /**
     * Returns copy of array used as internal storage for this collection.
     *
     * @return array
     */
    function arrayValue();
    //--------------------------------------------------------------------------
  }
?>
