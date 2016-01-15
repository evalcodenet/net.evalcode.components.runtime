<?php


namespace js;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage js
     *
     * @author evalcode.net
     */


    // PROPERTIES
    libstd_set('libstd', 'runtime/js/libstd.js', 'js');
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param string $string_
     *
     * @return string
     */
    function escape($string_)
    {
      static $match=["/\\\\/", "/\n/", "/\r/", "/\"/", "/\'/", "/&/", "/</", "/>/"];
      static $replace=["\\\\\\\\", "\\n", "\\r", "\\\"", "\\'", "\\x26", "\\x3C", "\\x3E"];

      return str_replace($match, $replace, $string_);
    }


    // libstd.js API
    /**
     * @return string
     */
    function libstdJsLocation()
    {
      return libstd_get('libstd', 'js');
    }

    /**
     * @param string $property_
     * @param string $selector_
     *
     * @return string
     */
    function equal($property_, $selector_)
    {
      return 'libstd-dom="'.
        htmlentities(
          json_encode(['equal'=>[$property_=>$selector_]]),
          ENT_COMPAT, null, false
        ).'"';
    }

    /**
     * @param string $selector_
     *
     * @return string
     */
    function equalHeight($selector_)
    {
      return equal('height', $selector_);
    }

    /**
     * @param string $selector_
     *
     * @return string
     */
    function equalWidth($selector_)
    {
      return equal('width', $selector_);
    }
    //--------------------------------------------------------------------------
?>
