<?php


namespace cache;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage cache.apc
     *
     * @author evalcode.net
     */


    if(false===isset($GLOBALS['libstd/cache']))
      $GLOBALS['libstd/cache']=[];


    function constants_define($key_, array $constants_)
    {
      return apc_define_constants(COMPONENTS_CACHE_NAMESPACE."-$key_", $constants_, true);
    }

    function constants_load($key_)
    {
      return apc_load_constants(COMPONENTS_CACHE_NAMESPACE."-$key_", true);
    }

    function has($key_)
    {
      if(isset($GLOBALS['libstd/cache'][$key_]))
        return true;

      return apc_exists(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    function has_t($key_)
    {
      if(isset($GLOBALS['libstd/cache'][$key_]))
        return true;

      return apc_exists(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    function get($key_, &$has_=false)
    {
      if(false===isset($GLOBALS['libstd/cache'][$key_]))
      {
        $value=apc_fetch(COMPONENTS_CACHE_NAMESPACE."-$key_", $has_);

        if(false===$has_)
          return false;

        $GLOBALS['libstd/cache'][$key_]=$value;

        if(LIBSTD_CACHE_NULL===$value)
          return null;

        return $value;
      }

      if(LIBSTD_CACHE_NULL===$GLOBALS['libstd/cache'][$key_])
        return null;

      return $GLOBALS['libstd/cache'][$key_];
    }

    function get_t($key_, &$has_=false)
    {
      if(false===isset($GLOBALS['libstd/cache'][$key_]))
      {
        $value=apc_fetch(COMPONENTS_CACHE_NAMESPACE."-$key_", $has_);

        if(false===$has_)
          return false;

        $GLOBALS['libstd/cache'][$key_]=$value;

        if(LIBSTD_CACHE_NULL===$value)
          return null;

        return $value;
      }

      if(LIBSTD_CACHE_NULL===$GLOBALS['libstd/cache'][$key_])
        return null;

      return $GLOBALS['libstd/cache'][$key_];
    }

    function set($key_, $value_)
    {
      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      $GLOBALS['libstd/cache'][$key_]=$value_;

      return apc_store(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_);
    }

    function set_t($key_, $value_, $ttl_=0)
    {
      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      $GLOBALS['libstd/cache'][$key_]=$value_;

      return apc_store(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_, $ttl_);
    }

    function add($key_, $value_)
    {
      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      if($result=apc_add($key_, $value_))
        $GLOBALS['libstd/cache'][$key_]=$value_;

      return $result;
    }

    function add_t($key_, $value_, $ttl_=0)
    {
      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      if($result=apc_add($key_, $value_, $ttl_))
        $GLOBALS['libstd/cache'][$key_]=$value_;

      return $result;
    }

    function upd_t($key_, $value_, $ttl_=null)
    {
      if(false===apc_exists(COMPONENTS_CACHE_NAMESPACE."-$key_"))
        return false;

      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      if(null===$ttl_)
        $result=apc_store(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_);
      else
        $result=apc_store(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_, $ttl_);

      if($result)
        $GLOBALS['libstd/cache'][$key_]=$value_;

      return $result;
    }

    function remove($key_)
    {
      $GLOBALS['libstd/cache'][$key_]=null;

      return apc_delete(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    function clear($prefix_=null)
    {
      if(null===$prefix_)
        $prefix_=COMPONENTS_CACHE_NAMESPACE;
      else
        $prefix_=COMPONENTS_CACHE_NAMESPACE."-$prefix_";

      $GLOBALS['libstd/cache']=[];

      apc_delete(new \APCIterator('user', "/^$prefix_*/", APC_ITER_KEY));

      if(function_exists('opcache_invalidate'))
      {
        $invalidate=function($path_) {
          opcache_invalidate($path_, true);
        };

        // FIXME Only if containerized.
        \io\pathApplyFiltered('/', '/\.php$/', $invalidate);
        // \io\pathApplyFiltered(\Components\Environment::pathApplication(), '/\.php$/', $invalidate);
        // \io\pathApplyFiltered(\Components\Environment::pathComponents(), '/\.php$/', $invalidate);
      }
      else if(function_exists('opcache_reset'))
      {
        opcache_reset();
      }
    }
    //--------------------------------------------------------------------------
?>
