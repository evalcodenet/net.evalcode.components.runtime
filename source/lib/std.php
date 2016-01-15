<?php


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     *
     * @author evalcode.net
     */


    // MODULES
    include_once __DIR__.'/std/env.php';

    include_once __DIR__.'/std/cache.php';
    include_once __DIR__.'/std/html.php';
    include_once __DIR__.'/std/io.php';
    include_once __DIR__.'/std/js.php';
    include_once __DIR__.'/std/log.php';
    include_once __DIR__.'/std/math.php';
    include_once __DIR__.'/std/obj.php';
    include_once __DIR__.'/std/str.php';
    include_once __DIR__.'/std/xml.php';
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @param string $module_
     *
     * @return mixed[]
     */
    function libstd_all($module_='global')
    {
      if(false===isset($GLOBALS['libstd'][$module_]))
        return [];

      return (array)$GLOBALS['libstd'][$module_];
    }

    /**
     * @param string $property_
     * @param string $module_
     *
     * @return mixed
     */
    function libstd_get($property_, $module_='global')
    {
      if(false===isset($GLOBALS['libstd'][$module_][$property_]))
        return null;

      return $GLOBALS['libstd'][$module_][$property_];
    }

    /**
     * @param string $property_
     * @param mixed $value_
     * @param string $module_
     *
     * @return mixed
     */
    function libstd_set($property_, $value_, $module_='global')
    {
      return $GLOBALS['libstd'][$module_][$property_]=$value_;
    }

    /**
     * @param string $property_
     * @param string $module_
     */
    function libstd_isset($property_, $module_='global')
    {
      return isset($GLOBALS['libstd'][$module_][$property_]);
    }

    /**
     * @param string $property_
     * @param string $module_
     */
    function libstd_unset($property_, $module_='global')
    {
      $GLOBALS['libstd'][$module_][$property_]=null;
    }
    //--------------------------------------------------------------------------
?>
