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

    public static function constantsDefine($key_, array $constants_)
    {
      return apc_define_constants(COMPONENTS_CACHE_NAMESPACE."/$key_", $constants_, true);
    }

    public static function constantsLoad($key_)
    {
      return apc_load_constants(COMPONENTS_CACHE_NAMESPACE."/$key_");
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @see \Components\Cache_Backend::exists() \Components\Cache_Backend::exists()
     */
    public function exists($key_)
    {
      return apc_exists(COMPONENTS_CACHE_NAMESPACE."/$key_");
    }

    /**
     * @see \Components\Cache_Backend::get() \Components\Cache_Backend::get()
     */
    public function get($key_)
    {
      return apc_fetch(COMPONENTS_CACHE_NAMESPACE."/$key_");
    }

    /**
     * @see \Components\Cache_Backend::set() \Components\Cache_Backend::set()
     */
    public function set($key_, $value_, $ttl_=0)
    {
      return apc_store(COMPONENTS_CACHE_NAMESPACE."/$key_", $value_, $ttl_);
    }

    /**
     * @see \Components\Cache_Backend::remove() \Components\Cache_Backend::remove()
     */
    public function remove($key_)
    {
      return apc_delete(COMPONENTS_CACHE_NAMESPACE."/$key_");
    }

    /**
     * @see \Components\Cache_Backend::dump() \Components\Cache_Backend::dump()
     */
    public function dump($filename_)
    {
      // FIXME Produces segfault on load if parameters are set to null / everything is dumped.
      return apc_bin_dumpfile([], [], $filename_);
    }

    /**
     * @see \Components\Cache_Backend::load() \Components\Cache_Backend::load()
     */
    public function load($filename_)
    {
      return apc_bin_loadfile($filename_);
    }

    /**
     * @see \Components\Cache_Backend::clear() \Components\Cache_Backend::clear()
     */
    public function clear($prefix_=null)
    {
      if(null===$prefix_)
      {
        apc_delete(new \APCIterator('user', '/'.COMPONENTS_CACHE_NAMESPACE.'/'));

        apc_delete_file(new \APCIterator('system', '/'.addcslashes(Environment::pathComponents(), '/ ').'/'));
        apc_delete_file(new \APCIterator('system', '/'.addcslashes(Environment::pathApplication(), '/ ').'/'));
        apc_delete_file(new \APCIterator('system', '/'.addcslashes(realpath(Environment::pathComponents()), '/ ').'/'));
        apc_delete_file(new \APCIterator('system', '/'.addcslashes(realpath(Environment::pathApplication()), '/ ').'/'));
      }
      else
      {
        apc_delete(new \APCIterator('user', "/^$prefix_*/", APC_ITER_KEY));
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
