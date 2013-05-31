<?php


namespace Components;


  /**
   * Cache_Backend_Null
   *
   * @package net.evalcode.components
   * @subpackage runtime.cache.backend
   *
   * @author evalcode.net
   */
  class Cache_Backend_Null implements Cache_Backend
  {
    // ACCESSORS
    /**
     * (non-PHPdoc)
     * @see Components\Cache_Backend::exists()
     */
    public function exists($key_)
    {
      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Cache_Backend::get()
     */
    public function get($key_)
    {
      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Cache_Backend::set()
     */
    public function set($key_, $value_, $ttl_=0)
    {
      return true;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Cache_Backend::remove()
     */
    public function remove($key_)
    {
      return true;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Cache_Backend::dump()
     */
    public function dump($filename_)
    {
      return true;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Cache_Backend::load()
     */
    public function load($filename_)
    {
      return true;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Cache_Backend::clear()
     */
    function clear($prefix_=null)
    {
      return true;
    }
    //--------------------------------------------------------------------------
  }
?>
