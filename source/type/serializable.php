<?php


namespace Components;


  /**
   * Serializable
   *
   * @package net.evalcode.components
   * @subpackage type
   *
   * @author evalcode.net
   */
  interface Serializable
  {
    // ACCESSORS
    /**
     * Returns version of type associated to this instance.
     *
     * Can be used to invalidate or migrate serialized objects.
     * A smart implementation could allow us to selectivly invalidate
     * e.g. cached objects on access after source code has been updated,
     * instead of sweeping all caches as it is currently common practice.
     *
     * Currently Components\Cache is using a hashed namespace consisting
     * out of components filesystem location as well as the runtimes'
     * last modification time to achive isolation. The modification time
     * should be removed from this hash if we want to make proper use of
     * type versioning.
     *
     * All implementations relying on/working with cached/serialized
     * type information would need to be refactored accordingly, e.g.
     * Components\Annotations, instances of Components\Classloader,
     * Components\Marshaller etc.
     *
     * @return mixed
     */
    function serialVersionUid();
    //--------------------------------------------------------------------------
  }
?>
