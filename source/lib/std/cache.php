<?php


namespace cache;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage cache
     *
     * @author evalcode.net
     */


    // PREDEFINED PROPERTIES
    /**
     * @internal
     */
     define('LIBSTD_CACHE_NULL', 'LIBSTD_CACHE_NULL');


     // INITIALIZATION
     if(false===defined('LIBSTD_CACHE_BACKEND'))
     {
       $backend='apc';

       if(false===extension_loaded('apc'))
       {
         $backend='local';

         if(extension_loaded('xcache'))
           $backend='xcache';
         else if(PHP_SESSION_ACTIVE===session_status())
           $backend='session';
       }

       define('LIBSTD_CACHE_BACKEND', $backend);
     }


     include_once __DIR__.'/cache/'.LIBSTD_CACHE_BACKEND.'.php';
    //--------------------------------------------------------------------------
?>
