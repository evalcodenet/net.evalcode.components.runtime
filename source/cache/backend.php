<?php


namespace Components;


  /**
   * Cache_Backend
   *
   * @api
   * @package net.evalcode.components.cache
   *
   * @author evalcode.net
   */
  interface Cache_Backend
  {
    // ACCESSORS
    /**
     * Determines whether a value is cached and not expired for given key.
     *
     * Returns 'true' if a value is cached and valid, returns 'false' if
     * no value has been found for given key or if value expired.
     *
     * @param string $key_
     *
     * @return boolean
     */
    function exists($key_);
    /**
     * Returns cached value for given key or 'false' if value can not
     * be found / has been expired.
     *
     * @param string $key_
     *
     * @return mixed|false
     */
    function get($key_);
    /**
     * Caches given value for given key. Optional $ttl_ can be passed
     * to define seconds until cached value expires - default 1 day.
     *
     * Returns 'true' when value has been cached successfully, otherwise
     * returns 'false'.
     *
     * @param string $key_
     * @param mixed $value_
     * @param integer $ttl_
     *
     * @return boolean
     */
    function set($key_, $value_, $ttl_=86400);
    /**
     * Removes cached value for given key.
     *
     * Returns 'true' when value has been removed successfully, otherwise
     * returns 'false'.
     *
     * @param string $key_
     *
     * @return boolean
     */
    function remove($key_);
    /**
     * Dumps cache contents into file for given filename.
     *
     * Returns 'true' on success, otherwise returns 'false'.
     *
     * @param string $filename_
     *
     * @return boolean
     */
    function dump($filename_);
    /**
     * Loads contents of file for given filename into cache.
     *
     * Returns 'true' on success, otherwise returns 'false'.
     *
     * @param string $filename_
     *
     * @return boolean
     */
    function load($filename_);
    /**
     * Clears all cache contents created by current runtime instance.
     *
     * $prefix_ can be passed to clear only contents thats
     * keys are introduced by given prefix.
     *
     * @param string $prefix_
     */
    function clear($prefix_=null);
    //--------------------------------------------------------------------------
  }
?>
