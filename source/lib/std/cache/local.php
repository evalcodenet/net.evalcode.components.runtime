<?php


namespace cache;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage cache.local
     *
     * @author evalcode.net
     */


    if(false===isset($GLOBALS['libstd/cache']))
      $GLOBALS['libstd/cache']=[];


    function constants_define($key_, array $constants_)
    {
      $GLOBALS['libstd/cache'][$key_]=$constants_;

      foreach($constants_ as $name=>$value)
      {
        if(false===defined($name))
          define($name, $value);
      }

      return true;
    }

    function constants_load($key_)
    {
      if(isset($GLOBALS['libstd/cache'][$key_]))
      {
        foreach((array)$GLOBALS['libstd/cache'][$key_] as $name=>$value)
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
      return isset($GLOBALS['libstd/cache'][$key_]);
    }

    function has_t($key_)
    {
      if(false===isset($GLOBALS['libstd/cache'][$key_]))
        return false;

      $entry=$GLOBALS['libstd/cache'][$key_];

      if(0<$entry['ttl'] && time()>$entry['time']+$entry['ttl'])
      {
        $has_=false;

        $GLOBALS['libstd/cache'][$key_]=null;

        return false;
      }

      return true;
    }

    function get($key_, &$has_=false)
    {
      if(false===isset($GLOBALS['libstd/cache'][$key_]))
      {
        $has_=false;

        return false;
      }

      $has_=true;

      if(LIBSTD_CACHE_NULL===$GLOBALS['libstd/cache'][$key_])
        return null;

      return $GLOBALS['libstd/cache'][$key_];
    }

    function get_t($key_, &$has_=false)
    {
      if(false===isset($GLOBALS['libstd/cache'][$key_]))
      {
        $has_=false;

        return false;
      }

      $entry=$GLOBALS['libstd/cache'][$key_];

      if(0<$entry['ttl'] && time()>$entry['time']+$entry['ttl'])
      {
        $has_=false;

        $GLOBALS['libstd/cache'][$key_]=null;

        return false;
      }

      $has_=true;

      if(LIBSTD_CACHE_NULL===$entry['value'])
        return null;

      return $entry['value'];
    }

    function set($key_, $value_)
    {
      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      $GLOBALS['libstd/cache'][$key_]=$value_;

      return true;
    }

    function set_t($key_, $value_, $ttl_=0)
    {
      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      $GLOBALS['libstd/cache'][$key_]=[
        'value'=>$value_,
        'time'=>time(),
        'ttl'=>$ttl_
      ];

      return true;
    }

    function add($key_, $value_)
    {
      if(isset($GLOBALS['libstd/cache'][$key_]))
        return false;

      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      $GLOBALS['libstd/cache'][$key_]=$value_;

      return true;
    }

    function add_t($key_, $value_, $ttl_=0)
    {
      if(isset($GLOBALS['libstd/cache'][$key_]))
      {
        $entry=$GLOBALS['libstd/cache'][$key_];

        if(1>$entry['ttl'] || time()<$entry['time']+$entry['ttl'])
          return false;
      }

      if(null===$value_)
        $value_=LIBSTD_CACHE_NULL;

      $GLOBALS['libstd/cache'][$key_]=[
        'value'=>$value_,
        'time'=>time(),
        'ttl'=>$ttl_
      ];

      return true;
    }

    function upd_t($key_, $value_, $ttl_=null)
    {
      if(isset($GLOBALS['libstd/cache'][$key_]))
      {
        $entry=&$GLOBALS['libstd/cache'][$key_];

        if(null===$ttl_ && 0<$entry['ttl'] && time()>($entry['time']+$entry['ttl']))
        {
          $GLOBALS['libstd/cache'][$key_]=null;

          return false;
        }

        if(null!==$ttl_)
          $entry['ttl']=$ttl_;

        if(null===$value_)
          $value_=LIBSTD_CACHE_NULL;

        $entry['value']=$value_;

        return true;
      }

      return false;
    }

    function remove($key_)
    {
      $GLOBALS['libstd/cache'][$key_]=null;

      return true;
    }

    function clear()
    {
      $GLOBALS['libstd/cache']=[];
    }
    //--------------------------------------------------------------------------
?>
