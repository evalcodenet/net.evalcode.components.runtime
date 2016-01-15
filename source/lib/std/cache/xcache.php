<?php


namespace cache;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage cache.xcache
     *
     * @author evalcode.net
     */


    if(false===isset($GLOBALS['libstd/cache']))
      $GLOBALS['libstd/cache']=[];


    function constants_define($key_, array $constants_)
    {
      foreach($constants_ as $name=>$value)
      {
        if(false===defined($name))
          define($name, $value);
      }

      return xcache_set(COMPONENTS_CACHE_NAMESPACE."-$key_", $constants_);
    }

    function constants_load($key_)
    {
      $constants=xcache_get(COMPONENTS_CACHE_NAMESPACE."-$key_");

      if(is_array($constants))
      {
        foreach($constants as $name=>$value)
        {
          if(false===defined($name))
            define($name, $value);
        }

        return true;
      }

      return false;
    }

    function has($key_)
    {
      if(isset($GLOBALS['libstd/cache'][$key_]))
        return true;

      return xcache_isset(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    function has_t($key_)
    {
      if(isset($GLOBALS['libstd/cache'][$key_]))
        return true;

      return xcache_isset(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    function get($key_, &$has_=false)
    {
      if(false===isset($GLOBALS['libstd/cache'][$key_]))
      {
        $value=xcache_get(COMPONENTS_CACHE_NAMESPACE."-$key_");

        if(null===$value)
        {
          $has_=false;

          return false;
        }

        $has_=true;

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
        $value=xcache_get(COMPONENTS_CACHE_NAMESPACE."-$key_");

        if(null===$value)
        {
          $has_=false;

          return false;
        }

        $has_=true;

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

      return xcache_set(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_);
    }

    function set_t($key_, $value_, $ttl_=0)
    {
      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      $GLOBALS['libstd/cache'][$key_]=$value_;

      return xcache_set(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_, $ttl_);
    }

    function add($key_, $value_)
    {
      if(xcache_isset(COMPONENTS_CACHE_NAMESPACE."-$key_"))
        return false;

      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      $GLOBALS['libstd/cache'][$key_]=$value_;

      return xcache_set(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_);
    }

    function add_t($key_, $value_, $ttl_=0)
    {
      if(xcache_isset(COMPONENTS_CACHE_NAMESPACE."-$key_"))
        return false;

      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      $GLOBALS['libstd/cache'][$key_]=$value_;

      return xcache_set(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_, $ttl_);
    }

    function upd_t($key_, $value_, $ttl_=null)
    {
      if(false===xcache_isset(COMPONENTS_CACHE_NAMESPACE."-$key_"))
        return false;

      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      if(null===$ttl_)
        $result=xcache_set(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_);
      else
        $result=xcache_set(COMPONENTS_CACHE_NAMESPACE."-$key_", $value_, $ttl_);

      if($result)
        $GLOBALS['libstd/cache'][$key_]=$value_;

      return $result;
    }

    function remove($key_)
    {
      $GLOBALS['libstd/cache'][$key_]=null;

      return xcache_unset(COMPONENTS_CACHE_NAMESPACE."-$key_");
    }

    function clear($prefix_=null)
    {
      if(null===$prefix_)
        xcache_unset_by_prefix(COMPONENTS_CACHE_NAMESPACE);
      else
        xcache_unset_by_prefix(COMPONENTS_CACHE_NAMESPACE."-$prefix_");
    }
    //--------------------------------------------------------------------------
?>
