<?php


namespace xml;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage xml
     *
     * @author evalcode.net
     */


    // ACCESSORS
    /**
     * @param string $string_
     * @param string $charset_
     *
     * @return string
     */
    function escape($string_, $charset_=null)
    {
      if(null===$charset_)
        $charset_=libstd_get('charset', 'env');

      return htmlentities($string_, ENT_XML1|ENT_COMPAT|ENT_DISALLOWED, $charset_);
    }
    //--------------------------------------------------------------------------
?>
