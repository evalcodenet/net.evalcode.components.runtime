<?php


namespace Components;


  /**
   * Cache_Backend_Xcache
   *
   * @api
   * @package net.evalcode.components.cache
   * @subpackage backend
   *
   * @author evalcode.net
   */
  class Cache_Backend_Xcache implements Cache_Backend
  {
    // STATIC ACCESSORS
    /**
     * @return boolean
     */
    public static function isSupported()
    {
      if(null===self::$m_isSupported)
      {
        if(extension_loaded('xcache') && (bool)ini_get('xcache.cacher'))
          return self::$m_isSupported=true;

        return self::$m_isSupported=false;
      }

      return self::$m_isSupported;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @see \Components\Cache_Backend::exists() exists
     */
    public function exists($key_)
    {
      return xcache_isset(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    /**
     * @see \Components\Cache_Backend::get() get
     */
    public function get($key_)
    {
      // TODO [CSH] Refactor interface to return 'null' for missing/expired keys.
      $value=xcache_get(COMPONENTS_CACHE_NAMESPACE."-$key_");

      if(null===$value)
        return false;

      return $value;
    }

    /**
     * @see \Components\Cache_Backend::set() set
     */
    public function set($key_, $value_, $ttl_=86400)
    {
      return xcache_set(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_, $ttl_);
    }

    /**
     * @see \Components\Cache_Backend::remove() remove
     */
    public function remove($key_)
    {
      return xcache_unset(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    /**
     * @see \Components\Cache_Backend::dump() dump
     */
    public function dump($filename_)
    {
      // TODO [CSH] Fallback implementation or deprecate & remove.
      return false;
    }

    /**
     * @see \Components\Cache_Backend::load() load
     */
    public function load($filename_)
    {
      // TODO [CSH] Fallback implementation or deprecate & remove.
      return false;
    }

    /**
     * @see \Components\Cache_Backend::clear() clear
     */
    public function clear($prefix_=null)
    {
      if(null===$prefix_)
        return xcache_unset_by_prefix(COMPONENTS_CACHE_NAMESPACE);

      return xcache_unset_by_prefix(COMPONENTS_CACHE_NAMESPACE."-$prefix_");
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    private static $m_isSupported;
    //--------------------------------------------------------------------------
  }
?>
