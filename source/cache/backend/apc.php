<?php


namespace Components;


  /**
   * Cache_Backend_Apc
   *
   * @deprecated
   *
   * @package net.evalcode.components.cache
   * @subpackage backend
   *
   * @author evalcode.net
   */
  class Cache_Backend_Apc implements Cache_Backend
  {
    // STATIC ACCESSORS
    /**
     * @return boolean
     */
    public static function isSupported()
    {
      if(null===self::$m_isSupported)
      {
        if(extension_loaded('apc') && (true===@apc_exists('components/cache/backend/apc/supported') || false!==@apc_store('components/cache/backend/apc/supported', true)))
          return self::$m_isSupported=true;

        return self::$m_isSupported=false;
      }

      return self::$m_isSupported;
    }

    /**
     * @param string $key_
     * @param scalar[] $constants_
     *
     * @return boolean
     */
    public static function constantsDefine($key_, array $constants_)
    {
      return apc_define_constants(COMPONENTS_CACHE_NAMESPACE."-$key_", $constants_, true);
    }

    /**
     * @param string $key_
     *
     * @return boolean
     */
    public static function constantsLoad($key_)
    {
      return apc_load_constants(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @see \Components\Cache_Backend::exists() exists
     */
    public function exists($key_)
    {
      return apc_exists(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    /**
     * @see \Components\Cache_Backend::get() get
     */
    public function get($key_)
    {
      return apc_fetch(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    /**
     * @see \Components\Cache_Backend::set() set
     */
    public function set($key_, $value_, $ttl_=86400)
    {
      if(false===@apc_store(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_, $ttl_))
      {
        Log::warn('components/cache/backend/apc',
          'Unable to cache value [key: %s, value: %s].', $key_, $value_
        );

        return false;
      }

      return true;
    }

    /**
     * @see \Components\Cache_Backend::remove() remove
     */
    public function remove($key_)
    {
      return apc_delete(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    /**
     * @see \Components\Cache_Backend::dump() dump
     */
    public function dump($filename_)
    {
      // FIXME Produces segfault on load if parameters are set to null / everything is dumped.
      return apc_bin_dumpfile([], [], $filename_);
    }

    /**
     * @see \Components\Cache_Backend::load() load
     */
    public function load($filename_)
    {
      return apc_bin_loadfile($filename_);
    }

    /**
     * @see \Components\Cache_Backend::clear() clear
     */
    public function clear($prefix_=null)
    {
      if(null===$prefix_)
        $prefix_=COMPONENTS_CACHE_NAMESPACE;
      else
        $prefix_=COMPONENTS_CACHE_NAMESPACE."-$prefix_";

      apc_delete(new \APCIterator('user', "/^$prefix_*/", APC_ITER_KEY));

      // FIXME Implement cache/backend/opcache or separate into opcache/zend & opcache/apc in independent of cache/backend/* userland caches.
      if(function_exists('opcache_invalidate'))
      {
        Io::pathApplyRecursive(Environment::pathApplication(), function(Io_Path $path_) {
          if($path_->hasFileExtension(Io_Mimetype::EXTENSION_PHP))
            opcache_invalidate($path_, true);
        });
      }
      else if(function_exists('opcache_reset'))
      {
        opcache_reset();
      }
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    private static $m_isSupported;
    //--------------------------------------------------------------------------
  }


    // COMPATIBILITY HELPERS
    if(false===function_exists('apc_exists'))
    {
      function apc_exists($key_)
      {
        return false!==apc_fetch($key_);
      }
    }
    //--------------------------------------------------------------------------
?>
