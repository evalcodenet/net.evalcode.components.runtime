<?php


namespace cache;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage cache.session
     *
     * @author evalcode.net
     */


    if(false===isset($GLOBALS['libstd/cache']))
    {
      if(PHP_SESSION_ACTIVE===session_status())
      {
        if(false===isset($_SESSION['libstd/cache']))
          $_SESSION['libstd/cache']=[];

        $GLOBALS['libstd/cache']=&$_SESSION['libstd/cache'];
      }
      else
      {
        $GLOBALS['libstd/cache']=[];
      }
    }

    include_once __DIR__.'/local.php';
    //--------------------------------------------------------------------------
?>
