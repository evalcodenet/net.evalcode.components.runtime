<?php


namespace Components;


  /**
   * Comparable
   *
   * @api
   * @package net.evalcode.components.type
   *
   * @author evalcode.net
   */
  interface Comparable
  {
    // ACCESSORS
    /**
     * Returns a negative integer, zero, or a positive integer as this object
     * is less than, equal to, or greater than the passed object.
     *
     * @param mixed $object_
     *
     * @return integer
     *
     * @throws \Components\Runtime_Exception If unable to compare to given argument.
     */
    function compareTo($object_);
    //--------------------------------------------------------------------------
  }
?>
