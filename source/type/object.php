<?php


namespace Components;


  /**
   * Object
   *
   * @package net.evalcode.components
   * @subpackage type
   *
   * @author evalcode.net
   */
  interface Object
  {
    // ACCESSORS
    /**
     * Calculates and returns a hash that represents the state of this
     * object.
     *
     * <p>
     *   The definition of 'state' may depend on the actual type of the
     *   object. Common sense would be to define 'state' with everything
     *   that is neccessary to clone and/or replace the object to/with
     *   an 100% identical one.
     * </p>
     *
     * @return string
     */
    function hashCode();

    /**
     * Determines whether this object is equal to the passed one.
     *
     * <p>
     *   For now we go with the simple definition that equals must return
     *   'true' if passed object is of same type as this one as well as if
     *   the value returned by $object_->hashCode() equals to the one returned
     *   by $this->hashCode(). Any other case should return 'false'.
     * </p>
     *
     * @param mixed $object_
     *
     * @return boolean
     */
    function equals($object_);

    /**
     * Returns a string representation of this object.
     *
     * <p>
     *   Normally this would be a string in a standardized format representing
     *   the object's state for e.g. logging and debugging, similar to this:
     * </p>
     * <pre>
     *   return sprintf('%s@%s{member: %s}', __CLASS__, $this->hashCode(), $member);
     * </pre>
     *
     * <p>
     *   However there are cases where the preferred implementation differs.
     *   For example objects which represent a simple string themselves rather
     *   return their object state / string value directly.
     * </p>
     *
     * <p>
     *   Prefer to implement this method in every type instead of writing
     *   "too smart" super-types with helpers like get_class() etc. that
     *   apparently do the job with less code for more types.
     * </p>
     *
     * <p>
     *   Over time this may cause inconsistencies, copy &amp; paste mistakes,
     *   performance deficits etc.
     * </p>
     *
     * @return string
     */
    function __toString();
    //--------------------------------------------------------------------------
  }
?>
