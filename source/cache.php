<?php


namespace Components;


  /**
   * Cache
   *
   * @api
   * @package net.evalcode.components.cache
   *
   * @author evalcode.net
   */
  class Cache
  {
    // STATIC ACCESSORS
    /**
     * @param \Components\Cache_Backend $backend_
     */
    public static function create(Cache_Backend $backend_=null)
    {
      if(Cache_Backend_Apc::isSupported())
      {
        self::$m_backend=new Cache_Backend_Apc();
      }
      else
      {
        $storage=array();

        if(session_id())
        {
          if(false===isset($_SESSION[COMPONENTS_CACHE_NAMESPACE]))
            $_SESSION[COMPONENTS_CACHE_NAMESPACE]=array();

          $storage=&$_SESSION[COMPONENTS_CACHE_NAMESPACE];
        }

        self::$m_backend=new Cache_Backend_Local($storage);
      }

      if(false===self::$m_enabled)
      {
        self::$m_backendDisabled=self::$m_backend;
        self::$m_backend=new Cache_Backend_Null();
      }
    }

    public static function enable()
    {
      self::$m_backend=self::$m_backendDisabled;
      self::$m_enabled=true;
    }

    public static function disable()
    {
      self::$m_backendDisabled=self::$m_backend;
      self::$m_backend=new Cache_Backend_Null();
      self::$m_enabled=false;
    }

    /**
     * @return boolean
     */
    public static function enabled()
    {
      return true===self::$m_enabled;
    }

    /**
     * @return \Components\Cache_Backend
     */
    public static function backend()
    {
      return self::$m_backend;
    }

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
    public static function exists($key_)
    {
      return self::$m_backend->exists($key_);
    }

    /**
     * Returns cached value for given key or 'false' if value can not
     * be found / has been expired.
     *
     * @param string $key_
     *
     * @return mixed|false
     */
    public static function get($key_)
    {
      return self::$m_backend->get($key_);
    }

    /**
     * Caches given value for given key. Optional $ttl_ can be passed
     * to define seconds until cached value expires.
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
    public static function set($key_, $value_, $ttl_=0)
    {
      return self::$m_backend->set($key_, $value_, $ttl_);
    }

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
    public static function remove($key_)
    {
      return self::$m_backend->remove($key_);
    }

    /**
     * Dumps cache contents into file for given filename.
     *
     * Returns 'true' on success, otherwise returns 'false'.
     *
     * @param string $filename_
     *
     * @return boolean
     */
    public static function dump($filename_)
    {
      return self::$m_backend->dump($filename_);
    }

    /**
     * Loads contents of file for given filename into cache.
     *
     * Returns 'true' on success, otherwise returns 'false'.
     *
     * @param string $filename_
     *
     * @return boolean
     */
    public static function load($filename_)
    {
      return self::$m_backend->load($filename_);
    }

    /**
     * Clears all cache contents created by current runtime instance.
     *
     * $prefix_ can be passed to clear only contents thats
     * keys are introduced by given prefix.
     *
     * @param string $prefix_
     */
    public static function clear($prefix_=null)
    {
      return self::$m_backend->clear($prefix_);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    private static $m_enabled;
    /**
     * @var \Components\Cache_Backend
     */
    private static $m_backend;
    /**
     * @var \Components\Cache_Backend
     */
    private static $m_backendDisabled;
    //--------------------------------------------------------------------------
  }
?>
