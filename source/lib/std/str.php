<?php


namespace str;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage str
     *
     * @author evalcode.net
     */


    // PREDEFINED PROPERTIES
    define('LIBSTD_STR_TRUNCATE_END', 1);
    define('LIBSTD_STR_TRUNCATE_MIDDLE', 2);
    define('LIBSTD_STR_TRUNCATE_REVERSE', 4);

    define('LIBSTD_STR_LEFT', 1);
    define('LIBSTD_STR_RIGHT', 2);
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param mixed $value_
     *
     * @return string
     */
    function cast($value_)
    {
      return (string)$value_;
    }

    /**
     * Determines whether two passed strings are equal to each other.
     *
     * <p>
     * Returns 'true' if passed $string0_, $string1_ are equal,
     * otherwise returns 'false'.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return boolean
     */
    function equals($string0_, $string1_)
    {
      return 0===strnatcmp($string0_, $string1_);
    }

    /**
     * Determines whether two passed strings are equal to each other
     * ignoring case sensitivity.
     *
     * <p>
     * Returns 'true' if passed $string0_, $string1_ are equal,
     * otherwise returns 'false'.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return boolean
     */
    function equalsIgnoreCase($string0_, $string1_)
    {
      return 0===strnatcasecmp($string0_, $string1_);
    }

    /**
     * Calculates hash code for given string.
     *
     * @param string $string_
     *
     * @return integer
     */
    function hash($string_)
    {
      return \math\hashs_fnv($string_);
    }

    /**
     * Determines whether given string is 'null' or of zero-length.
     *
     * @param string $string_
     *
     * @return boolean
     */
    function isEmpty($string_)
    {
      return null===$string_ || 1>strlen($string_);
    }

    /**
     * Determines whether given string is null or only contains
     * '0' (zero) or 'null' characters.
     *
     * @param string $string_
     *
     * @return boolean
     */
    function isNullOrEmpty($string_)
    {
      return false===(bool)$string_;
    }

    /**
     * Determines whether given string is null or only contains
     * '0' (zero) or 'null' characters.
     *
     * @param string $string_
     *
     * @return boolean
     */
    function isNullOrZero($string_)
    {
      if((bool)$string_)
        return isZero($string_);

      return true;
    }

    /**
     * Determines whether given string is equal to '0' (zero) or 0.00 etc.
     *
     * @param string $string_
     *
     * @return boolean
     */
    function isZero($string_)
    {
      return 1===preg_match('/^[.,0]+[.,0]+$/', (string)$string_);
    }

    /**
     * Determines whether given string consists only of
     * absolute numbers / represents an integer.
     *
     * @param string $string_
     *
     * @return boolean
     */
    function isInteger($string_)
    {
      return 1===preg_match('/^[\d]+$/', (string)$string_);
    }

    /**
     * Determines whether given argument is of type or can be cast to string.
     *
     * @param mixed $mixed_
     *
     * @return boolean
     */
    function isTypeCompatible($mixed_)
    {
      return is_scalar($mixed_) || method_exists([$mixed_, '__toString']);
    }

    /**
     * Returns length of passed string.
     *
     * <p>
     * Returns length of passed $string_.
     * </p>
     *
     * @param string $string_
     *
     * @return integer
     */
    function length($string_)
    {
      return mb_strlen($string_);
    }

    /**
     * Transforms given string's characters to lowercase.
     *
     * @param string $string_
     */
    function lowercase($string_)
    {
      return mb_convert_case($string_, MB_CASE_LOWER, libstd_get('charset', 'env'));
    }

    /**
     * Transforms given string's characters to uppercase.
     *
     * @param string $string_
     */
    function uppercase($string_)
    {
      return mb_convert_case($string_, MB_CASE_UPPER, libstd_get('charset', 'env'));
    }

    /**
     * Capitalizes words (by white-space separated characters) in given string.
     *
     * @param string $string_
     */
    function capitalize($string_)
    {
      return mb_convert_case($string_, MB_CASE_TITLE, libstd_get('charset', 'env'));
    }

    /**
     * Returns first position of value of second parameter
     * in value of first parameter.
     *
     * <p>
     * Returns first position of $string1_ in $string0_ starting at $offset_.
     * Returns -1 if indexed (sub-)$string0_ does not contain $string1_.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     * @param integer $offset_
     *
     * @return integer
     */
    function indexOf($string0_, $string1_, $offset_=0)
    {
      if(false===($idx=mb_strpos($string0_, $string1_, $offset_)))
        return -1;

      return $idx;
    }

    /**
     * Returns last position of value of second parameter
     * in value of first parameter.
     *
     * <p>
     * Returns last position of $string1_ in $string0_ starting at $offset_.
     * Returns -1 if indexed (sub-)$string0_ does not contain $string1_.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     * @param integer $offset_
     *
     * @return integer
     */
    function lastIndexOf($string0_, $string1_, $offset_=0)
    {
      if(false===($idx=mb_strrpos($string0_, $string1_, $offset_)))
        return -1;

      return $idx;
    }

    /**
     * Returns specified range of characters of given string.
     *
     * <p>
     * Extracts and returns a string from passed $string_ ranging
     * from $offset_ to $offset_+$length_.
     * </p>
     *
     * @param string $string_
     * @param integer $offset_
     * @param integer $length_
     *
     * @return string
     */
    function substring($string_, $offset_, $length_=null)
    {
      if(null===$length_)
        return mb_substr($string_, $offset_);

      return mb_substr($string_, $offset_, $length_);
    }

    /**
     * Split given string into chunks of given length.
     *
     * @param string $string_
     * @param integer $lengthChunks_
     *
     * @return string[]
     */
    function split($string_, $lengthChunks_=1)
    {
      $length=mb_strlen($string_);

      if($lengthChunks_>=$length)
        return $string_;

      $lengthChunks_=(int)$lengthChunks_;

      $chunks=[];
      for($i=0; $i<$length; $i+=$lengthChunks_)
        $chunks[]=mb_substr($string_, $i, $lengthChunks_);

      return $chunks;
    }

    /**
     * Compares two strings to each other case-sensitive and returns
     * an numeric indicator of which one is the greater one.
     *
     * <p>
     * Returns an integer below, equal to or above zero indicating whether
     * passed $string0_ is less than, equal to or more than passed $string1_.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return integer
     */
    function compare($string0_, $string1_)
    {
      return strnatcmp($string0_, $string1_);
    }

    /**
     * Compares two strings to each other case-insensitive and returns
     * an numeric indicator of which one is the greater one.
     *
     * <p>
     * Returns an integer below, equal to or above zero indicating whether
     * passed $string0_ is less than, equal to or more than passed $string1_.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return integer
     */
    function compareIgnoreCase($string0_, $string1_)
    {
      return strnatcasecmp($string0_, $string1_);
    }

    /**
     * Determines whether passed string contains second passed string.
     *
     * <p>
     * Returns 'true' if $string0_ contains contents of $string1_,
     * otherwise returns 'false'.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return boolean
     */
    function contains($string0_, $string1_)
    {
      return false!==mb_strpos($string0_, $string1_);
    }

    /**
     * Determines whether passed string contains second passed string.
     *
     * <p>
     * Returns 'true' if $string0_ contains contents of $string1_,
     * otherwise returns 'false'.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return boolean
     */
    function containsIgnoreCase($string0_, $string1_)
    {
      return false!==mb_strpos(lowercase($string0_), lowercase($string1_));
    }

    /**
     * Determines whether passed string starts with second passed string.
     *
     * <p>
     * Returns 'true' if $string0_ starts with contents of $string1_,
     * otherwise returns 'false'.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return boolean
     */
    function startsWith($string0_, $string1_)
    {
      return 0===mb_strpos($string0_, $string1_);
    }

    /**
     * Determines whether passed string starts with second passed string
     * ignoring case sensitivity.
     *
     * <p>
     * Returns 'true' if $string0_ starts with contents of $string1_
     * regardless whether passed strings may contain different capitalization.
     * Otherwise returns 'false'.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return boolean
     */
    function startsWithIgnoreCase($string0_, $string1_)
    {
      return 0===mb_stripos($string0_, $string1_);
    }

    /**
     * Determines whether passed string ends with second passed string.
     *
     * <p>
     * Returns 'true' if $string0_ ends with contents of $string1_,
     * otherwise returns 'false'.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return boolean
     */
    function endsWith($string0_, $string1_)
    {
      if(false===($pos=mb_strrpos($string0_, $string1_)))
        return false;

      return mb_strlen($string0_)===$pos+mb_strlen($string1_);
    }

    /**
     * Determines whether passed string ends with second passed string
     * ignoring case sensitivity.
     *
     * <p>
     * Returns 'true' if $string0_ ends with contents of $string1_
     * regardless whether passed strings may contain different capitalization.
     * Otherwise returns 'false'.
     * </p>
     *
     * @param string $string0_
     * @param string $string1_
     *
     * @return boolean
     */
    function endsWithIgnoreCase($string0_, $string1_)
    {
      if(false===($pos=mb_strripos($string0_, $string1_)))
        return false;

      return mb_strlen($string0_)===$pos+mb_strlen($string1_);
    }

    /**
     * @param string $string_
     * @param string $match_
     * @param string $replace_
     * @param integer $offset_
     *
     * @return string
     */
    function replace($string_, $match_, $replace_=null, $offset_=0)
    {
      if(null===$replace_)
        $replace_='';

      if(1>$offset_ || mb_strlen($string_)<=$offset_)
        return str_replace($match_, $replace_, $string_);

      return mb_substr($string_, 0, $offset_)
        .str_replace($match_, $replace_, mb_substr($string_, $offset_));
    }

    /**
     * @param string $string_
     * @param string $match_
     * @param string $replace_
     * @param integer $offset_
     *
     * @return string
     */
    function replaceAll($string_, $match_, $replace_, $offset_=0)
    {
      if(1>$offset_ || mb_strlen($string_)<=$offset_)
        return str_replace($match_, $replace_, $string_);

      return mb_substr($string_, 0, $offset_)
        .str_replace($match_, $replace_, mb_substr($string_, $offset_));
    }

    /**
     * @param string $string_
     * @param integer $length_
     * @param string $append_
     * @param string $truncateAtCharacter_
     *
     * @return string
     */
    function truncate($string_, $length_, $append_=null, $truncateAtCharacter_=null, $style_=LIBSTD_STR_TRUNCATE_END)
    {
      if($length_>=mb_strlen($string_))
        return $string_;

      if(0<($style_&LIBSTD_STR_TRUNCATE_REVERSE))
      {
        $string_=reverse($string_);
        $string=mb_substr($string_, 0, $length_);

        if(null===$truncateAtCharacter_)
          return $append_.reverse($string);

        $truncatePos=mb_strpos($string_, $truncateAtCharacter_, $length_);

        return $append_.reverse($string.mb_substr($string_, $length_, $truncatePos-$length_));
      }

      $string=mb_substr($string_, 0, $length_);
      if(null===$truncateAtCharacter_)
        return $string.$append_;

      $truncatePos=mb_strpos($string_, $truncateAtCharacter_, $length_);

      return $string.mb_substr($string_, $length_, $truncatePos-$length_).$append_;
    }

    /**
     * @param string $string_
     * @param integer $lengthPad_
     * @param string $stringPad_
     * @param integer $direction_
     *
     * @return string
     */
    function pad($string_, $lengthPad_, $stringPad_, $direction_=null)
    {
      if(null===$direction_)
        $direction_=LIBSTD_STR_LEFT|LIBSTD_STR_RIGHT;

      if(0<($direction_&LIBSTD_STR_LEFT))
        $string_=str_pad($string_, $lengthPad_, $stringPad_, STR_PAD_LEFT);
      if(0<($direction_&LIBSTD_STR_RIGHT))
        $string_=str_pad($string_, $lengthPad_, $stringPad_, STR_PAD_RIGHT);

      return $string_;
    }

    /**
     * @param string $string_
     * @param string $stringTrim_
     * @param integer $direction_
     */
    function trim($string_, $stringTrim_=null, $direction_=null)
    {
      if(null===$direction_)
      {
        if(null===$stringTrim_)
          return \trim($string_);

        return \trim($string_, $stringTrim_);
      }

      if(0<($direction_&LIBSTD_STR_LEFT))
      {
        if(null===$stringTrim_)
          $string_=ltrim($string_);
        else
          $string_=ltrim($string_, $stringTrim_);
      }

      if(0<($direction_&LIBSTD_STR_RIGHT))
      {
        if(null===$stringTrim_)
          $string_=ltrim($string_);
        else
          $string_=ltrim($string_, $stringTrim_);
      }

      return $string_;
    }

    /**
     * Reverse string.
     *
     * @param string $string_
     *
     * @return string
     */
    function reverse($string_)
    {
      $characters=split($string_, 1);
      $characters=array_reverse($characters);

      return implode('', $characters);
    }

    /**
     * Check whether string is ASCII compatible.
     *
     * @param string $string_
     *
     * @return bool
     */
    function isAscii($string_)
    {
      return LIBSTD_ENV_CHARSET_ASCII===mb_detect_encoding($string_);
    }

    /**
     * Convert to ASCII.
     *
     * @param string $string_
     *
     * @return string
     */
    function toAscii($string_, $charset_=null)
    {
      if(null===$charset_)
        $charset_=mb_detect_encoding($string_, 'auto', true);

      // FIXME Find a more elegant solution like iconv(ASCII//IGNORE).
      return trim(strtr(mb_convert_encoding($string_, LIBSTD_ENV_CHARSET_ASCII, $charset_), '?', ' '));
    }

    /**
     * Checks whether string is LATIN-1 compatible.
     *
     * @param string $string_
     *
     * @return boolean
     */
    function isLatin1($string_)
    {
      $len=mb_strlen($string_);

      for($i=0; $i<$len; ++$i)
      {
        $ord=ord($string_[$i]);

        // ASCII?
        if($ord>=0&&$ord<=127)
          continue;

          // 2 byte sequence?
        if($ord>=192&&$ord<=223)
        {
          $ord=($ord-192)*64+ord($string_[++$i])-128;

          // LATIN-1?
          if($ord<=0xff)
            continue;
        }

        return false;
      }

      return true;
    }

    /**
     * ASCII-ONLY
     *
     * Checks whether string is in camelCase notation.
     *
     * @param string $string_
     *
     * @return boolean
     */
    function isCamelCase($string_)
    {
      return 1===preg_match('/^[a-z][a-zA-Z0-9]*$/', $string_);
    }

    /**
     * ASCII-ONLY
     *
     * Transform to camelCase notation.
     *
     * @param string $string_ {mul ti ply}
     *
     * @return string {mulTiPly}
     */
    function toCamelCase($string_)
    {
      $string_=(string)$string_;

      $string='';

      $string_=mb_strtolower(trim($string_));
      $len=mb_strlen($string_);
      for($i=0; $i<$len; $i++)
      {
        if(32===($dec=ord($string_[$i])))
          $string.=mb_strtoupper($string_[++$i]);
        else
          $string.=$string_[$i];
      }

      return $string;
    }

    /**
     * ASCII-ONLY
     *
     * Transform to type name notation.
     *
     * @param string $string_ {type name}
     *
     * @return string {ASCII//Type_Name}
     */
    function toTypeName($string_)
    {
      $string_=toAscii($string_);
      $string_=ucwords(strtolower($string_));

      return preg_replace('/\s+/', '_', $string_);
    }

    /**
     * @param string $string_ {propErTy}
     *
     * @return string {PROP ER TY}
     */
    function camelCaseToUppercase($string_)
    {
      return strtoupper(camelCaseToLowercase($string_));
    }

    /**
     * @param string $string_ {propErTy}
     *
     * @return string {prop er ty}
     */
    function camelCaseToLowercase($string_)
    {
      static $stringTable=['A'=>' a', 'B'=>' b', 'C'=>' c', 'D'=>' d',
        'E'=>' e', 'F'=>' f', 'G'=>' g', 'H'=>' h', 'I'=>' i', 'J'=>' j',
        'K'=>' k', 'L'=>' l', 'M'=>' m', 'N'=>' n', 'O'=>' o', 'P'=>' p',
        'Q'=>' q', 'R'=>' r', 'S'=>' s', 'T'=>' t', 'U'=>' u', 'V'=>' v',
        'W'=>' w', 'X'=>' x', 'Y'=>' y', 'Z'=>' z'];

      return strtr(trim($string_), $stringTable);
    }

    /**
     * Converts camelcase names to underscore.
     *
     * @param string $string_ {propErTy}
     *
     * @return string {prop_er_ty}
     */
    function camelCaseToUnderscore($string_)
    {
      static $stringTable=['A'=>'_a', 'B'=>'_b', 'C'=>'_c', 'D'=>'_d',
        'E'=>'_e', 'F'=>'_f', 'G'=>'_g', 'H'=>'_h', 'I'=>'_i', 'J'=>'_j',
        'K'=>'_k', 'L'=>'_l', 'M'=>'_m', 'N'=>'_n', 'O'=>'_o', 'P'=>'_p',
        'Q'=>'_q', 'R'=>'_r', 'S'=>'_s', 'T'=>'_t', 'U'=>'_u', 'V'=>'_v',
        'W'=>'_w', 'X'=>'_x', 'Y'=>'_y', 'Z'=>'_z'];

      return strtr(trim($string_), $stringTable);
    }

    /**
     * Converts underscore names to camelcase.
     *
     * @param string $string_ {prop_er_ty}
     *
     * @return string {propErTy}
     */
    function underscoreToCamelCase($string_)
    {
      $camelcase=ucwords(strtr(trim($string_), '_', ' '));
      $camelcase[0]=lowercase($camelcase[0]);

      return mb_ereg_replace(' ', '', $camelcase);
    }

    /**
     * Converts underscore names to namespaces.
     *
     * @param string $string_ {Namespace_Type_Name}
     *
     * @return string {namespace/type/name}
     */
    function underscoreToNamespace($string_)
    {
      return strtolower(strtr($string_, '_', '/'));
    }

    /**
     * Converts PHP type names to namespaces.
     *
     * @param string $string_ {Namespace\\Type_Name}
     *
     * @return string {namespace/type/name}
     */
    function typeToNamespace($string_)
    {
      return strtolower(strtr($string_, '\\_', '//'));
    }

    /**
     * @param string $string_
     *
     * @return string
     */
    function typeToPath($string_)
    {
      return strtolower(strtr(str_replace('\\', '//', $string_), '_', '/'));
    }

    /**
     * @param string $string_
     *
     * @return string
     */
    function pathToType($string_)
    {
      $chunks=explode('//', $string_);

      $type=array_pop($chunks);
      $type=strtr(ucwords(strtr($type, '/', ' ')), ' ', '_');

      if(1>count($chunks))
        return $type;

      $namespace=strtr(ucwords(implode(' ', $chunks)), ' ', '\\');

      return "$namespace\\$type";
    }

    /**
     * Converts namespace notation to PHP type names.
     *
     * @param string $string_ {namespace/type/name}
     *
     * @return string {Namespace_Type_Name}
     */
    function namespaceToType($string_)
    {
      $string_=strtr($string_, '/', ' ');
      $string_=ucwords($string_);

      return strtr($string_, ' ', '_');
    }

    /**
     * Converts namespace notation to database table names.
     *
     * @param string $string_ {entity/foo/bar}
     *
     * @return string {entity_foo_bar}
     */
    function namespaceToTableName($string_)
    {
      $string_=preg_replace('/[^a-z0-9]/i', '_', $string_);

      return preg_replace('/_+/', '_', strtolower($string_));
    }

    /**
     * Checks for lowercase url friendly string.
     *
     * @param string $string_
     *
     * @return bool
     */
    function isLowercaseUrlIdentifier($string_)
    {
      return 1===preg_match('/^[a-z][a-z0-9_\-]*$/', $string_);
    }

    /**
     * Converts to lowercase url friendly string.
     *
     * @param string $string_
     *
     * @return string
     */
    function toLowercaseUrlIdentifier($string_, $preserveUnicode_=false)
    {
      if($preserveUnicode_)
        $string_=mb_convert_encoding($string_, 'HTML-ENTITIES', libstd_get('charset', 'env'));
      else
        $string_=toAscii($string_);

      $string_=preg_replace('/[^a-z0-9]/i', '-', $string_);
      $string_=preg_replace('/-+/', '-', strtolower($string_));

      if('-'===$string_)
        return null;

      return $string_;
    }

    /**
     * Encodes to base64.
     *
     * @param string $string_
     *
     * @return string
     */
    function encodeBase64($string_)
    {
      return base64_encode($string_);
    }

    /**
     * Decodes from base64.
     *
     * @param string $string_
     *
     * @return string
     */
    function decodeBase64($string_)
    {
      return base64_decode($string_);
    }

    /**
     * Encodes to url-friendly base64.
     *
     * @param string $string_
     *
     * @return string
     */
    function encodeBase64Url($string_)
    {
      return encodeUrl(base64_encode($string_));
    }

    /**
     * Decodes from url-friendly base64.
     *
     * @param string $string_
     *
     * @return string
     */
    function decodeBase64Url($string_)
    {
      return base64_decode(decodeUrl($string_));
    }

    /**
     * Encodes to quoted printable.
     *
     * @param string $string_
     *
     * @return string
     */
    function encodeQuotedPrintable($string_)
    {
      return quoted_printable_encode($string_);
    }

    /**
     * Decodes from quoted printable.
     *
     * @param string $string_
     *
     * @return string
     */
    function decodeQuotedPrintable($string_)
    {
      return quoted_printable_decode($string_);
    }

    /**
     * Checks if given string is encoded by rawurlencode().
     *
     * @param string $string_
     *
     * @return boolean
     */
    function encodedUrl($string_)
    {
      static $m_urlEncoded=['%20', '%21', '%2A', '%27', '%28',
        '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24',
        '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D'];
      static $m_urlDecoded=[' ', '!', '*', "'", "(", ")", ";",
        ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#",
        "[", "]"];

      $count=0;
      str_replace($m_urlEncoded, $m_urlDecoded, $string_, $count);

      return 0<$count;
    }

    /**
     * Encodes to url-friendly string.
     *
     * @param string $string_
     * @param boolean $avoidDuplicateEncoding_
     *                Only encodes if given string is not already encoded.
     *
     * @return string
     */
    function encodeUrl($string_, $avoidDuplicateEncoding_=false)
    {
      if(false===$avoidDuplicateEncoding_ || false===encodedUrl($string_))
        return rawurlencode($string_);

      return $string_;
    }

    /**
     * Decodes back from url-friendly string.
     *
     * @param string $string_
     * @param boolean $avoidDuplicateEncoding_
     *                Only decodes if given string is url encoded.
     *
     * @return string
     */
    function decodeUrl($string_, $avoidDuplicateEncoding_=false)
    {
      if(false===$avoidDuplicateEncoding_ || encodedUrl($string_))
        return rawurldecode($string_);

      return $string_;
    }

    /**
     * @param string $string_
     *
     * @return string
     */
    function toNumber($string_)
    {
      return preg_replace('/[^0-9]/', '', $string_);
    }

    /**
     * @param string $string_
     * @param bool $convertMobileCountryCodeIdentifier_
     *
     * @return string
     */
    function toPhoneNumber($string_, $convertMobileCountryCodeIdentifier_=false)
    {
      $string_=preg_replace('/[^+0-9]/', '', $string_);

      if($convertMobileCountryCodeIdentifier_)
        $string_=str_replace('+', '00', $string_);

      return $string_;
    }

    /**
     * @param string $string_
     * @param string $quote_
     * @param boolean $avoidDuplicateQuotes_
     */
    function quoted($string_, $quote_='"', $avoidDuplicateQuotes_=true)
    {
      if($avoidDuplicateQuotes_)
        $string_=unquoted($string_, '\' "');

      return "$quote_$string_$quote_";
    }

    /**
     * @param string $string_
     * @param string $quotes_
     */
    function unquoted($string_, $quotes_='\' "')
    {
      return ltrim(rtrim($string_, $quotes_), $quotes_);
    }
    //--------------------------------------------------------------------------
?>
