<?php


namespace Components;


  /**
   * Cache_Backend_Null
   *
   * @api
   * @package net.evalcode.components.cache
   * @subpackage backend
   *
   * @author evalcode.net
   */
  class Cache_Backend_Null implements Cache_Backend
  {
    // ACCESSORS
    /**
     * @see \Components\Cache_Backend::exists() exists
     */
    public function exists($key_)
    {
      return false;
    }

    /**
     * @see \Components\Cache_Backend::get() get
     */
    public function get($key_)
    {
      return false;
    }

    /**
     * @see \Components\Cache_Backend::set() set
     */
    public function set($key_, $value_, $ttl_=0)
    {
      return true;
    }

    /**
     * @see \Components\Cache_Backend::remove() remove
     */
    public function remove($key_)
    {
      return true;
    }

    /**
     * @see \Components\Cache_Backend::dump() dump
     */
    public function dump($filename_)
    {
      return false;
    }

    /**
     * @see \Components\Cache_Backend::load() load
     */
    public function load($filename_)
    {
      return false;
    }

    /**
     * @see \Components\Cache_Backend::clear() clear
     */
    function clear($prefix_=null)
    {
      return true;
    }
    //--------------------------------------------------------------------------
  }
?>
