<?php


namespace html;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage html
     *
     * @author evalcode.net
     */


    // ACCESSORS
    /**
     * Encode to (X)HTML entities or entities of given type.
     *
     * @param string $string_
     * @param string $charset_
     * @param integer $type_
     *
     * @return string
     */
    function escape($string_, $charset_=null, $type_=ENT_XHTML, $flags_=ENT_QUOTES)
    {
      static $debug;
      static $charset;

      if(null===$debug)
        $debug=\env\debug();

      if(null===$charset_)
      {
        if(null===$charset)
          $charset=libstd_get('charset', 'env');

        $charset_=$charset;
      }

      if($debug)
        $flags_|=ENT_SUBSTITUTE;

      return htmlentities($string_, $type_|$flags_, $charset_, false);
    }


    /**
     * Encode to HTML4 entities.
     *
     * @param string $string_
     * @param string $charset_
     *
     * @return string
     */
    function escape4($string_, $charset_=null, $flags_=ENT_QUOTES)
    {
      return \html\escape($string_, $charset_, ENT_HTML401, $flags_);
    }

    /**
     * Encode to HTML5 entities.
     *
     * @param string $string_
     * @param string $charset_
     *
     * @return string
     */
    function escape5($string_, $charset_=null, $flags_=ENT_QUOTES)
    {
      return \html\escape($string_, $charset_, ENT_HTML5, $flags_);
    }

    /**
     * Removes HTML tags & encodes HTML entities.
     *
     * @param string $string_
     * @param string $ignoreTags_
     * @param string $charset_
     * @param bool $escape_
     * @param integer $type_
     *
     * @return string
     */
    function strip($string_, $ignoreTags_=null, $charset_=null, $escape_=true, $type_=ENT_XHTML, $flags_=ENT_QUOTES)
    {
      if($escape_)
        return escape(strip_tags($string_, $ignoreTags_), $charset_, $type_, $flags_);

      return strip_tags($string_, $ignoreTags_);
    }
    //--------------------------------------------------------------------------
?>
