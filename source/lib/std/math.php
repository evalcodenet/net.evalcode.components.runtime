<?php


namespace math;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage math
     *
     * @author evalcode.net
     */


    // ACCESSORS
    /**
     * @param float $float_
     *
     * @return integer
     */
    function hashf($float_)
    {
      return 0x811c9dc5^$float_;
    }

    /**
     * @param float[] $float_
     *
     * @return integer
     */
    function hashfa(array $float_)
    {
      $hash=0;
      foreach($integers_ as $float)
        $hash=(0x811c9dc5*$hash)^$float;

      return $hash;
    }

    /**
     * @param float... $float0_
     *
     * @return integer
     */
    function hashfv($float0_/*, $float1_, $float2_, ..*/)
    {
      $hash=0;
      foreach(func_get_args() as $float)
        $hash=(0x811c9dc5*$hash)^$float;

      return $hash;
    }

    /**
     * @param integer $int_
     *
     * @return integer
     */
    function hashi($int_)
    {
      return 0x811c9dc5^$int_;
    }

    /**
     * @param integer[] $integers_
     *
     * @return integer
     */
    function hashia(array $integers_)
    {
      $hash=0;
      foreach($integers_ as $int)
        $hash=(0x811c9dc5*$hash)^$int;

      return $hash;
    }

    /**
     * @param integer... $int0_
     *
     * @return integer
     */
    function hashiv($int0_/*, $int1_, $int2_, ..*/)
    {
      $hash=0;
      foreach(func_get_args() as $int)
        $hash=(0x811c9dc5*$hash)^$int;

      return $hash;
    }

    /**
     * @param object $object_
     *
     * @return integer
     */
    function hasho($object_)
    {
      $hash=str_replace(
        ['a', 'b', 'c', 'd', 'e', 'f', '0'],
        [11, 12, 13, 14, 15, 16, ''],
        spl_object_hash($object_)
      );

      return $hash;
    }

    /**
     * @param object $object_
     *
     * @return string
     */
    function hasho_md5($object_)
    {
      return md5(spl_object_hash($object_));
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs($string_)
    {
      return hashs_fnv($string_);
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs_ap($string_)
    {
      $hash=0xaaaaaaaa;

      $len=strlen($string_);

      for($i=0; $i<$len; $i++)
      {
        if(0==($i&1))
          $hash^=($hash<<7)^ord($string_[$i])*($hash>>3);
        else
          $hash^=~($hash<<11)^ord($string_[$i])^($hash>>5);
      }

      return $hash;
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs_bkdr($string_)
    {
      $hash=0;

      $len=strlen($string_);

      for($i=0; $i<$len; $i++)
        $hash=(131*$hash)+ord($string_[$i]);

      return $hash;
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs_bp($string_)
    {
      $hash=0;

      $len=strlen($string_);

      for($i=0; $i<$len; $i++)
        $hash=$hash<<7^ord($string_[$i]);

      return $hash;
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs_dek($string_)
    {
      $hash=$len=strlen($string_);

      for($i=0; $i<$len; $i++)
        $hash=(($hash<<5)^($hash>>27))^ord($string_[$i]);

      return $hash;
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs_djb($string_)
    {
      $hash=5381;

      $len=strlen($string_);

      for($i=0; $i<$len; $i++)
        $hash=(($hash<<5)+$hash)+ord($string_[$i]);

      return $hash;
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs_fnv($string_)
    {
      $hash=0;

      $len=strlen($string_);

      for($i=0; $i<$len; $i++)
        $hash=(0x811c9dc5*$hash)^ord($string_[$i]);

      return $hash;
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs_js($string_)
    {
      $hash=1315423911;

      $len=strlen($string_);

      for($i=0; $i<$len; $i++)
        $hash^=(($hash<<5)+ord($string_[$i])+($hash>>2));

      return $hash;
    }

    /**
     * @param string $string_
     *
     * @return string
     */
    function hashs_md5($string_)
    {
      return md5($string_);
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs_pjw($string_)
    {
      $hash=0;
      $test=0;

      $len=strlen($string_);

      for($i=0; $i<$len; $i++)
      {
        $hash=($hash<<4)+ord($string_[$i]);

        if(0!=($test=$hash&1152921504338411520))
          $hash=(($hash^($test>>24))&~1152921504338411520);
      }

      return $hash;
    }

    /**
     * @param string $string_
     *
     * @return integer
     */
    function hashs_sdbm($string_)
    {
      $hash=0;

      $len=strlen($string_);

      $i=0;
      for($i=0; $i<$len; $i++)
        $hash=ord($string_[$i])+($hash<<6)+($hash<<16)-$hash;

      return $hash;
    }

    /**
     * @return string
     */
    function random_sha1_weak()
    {
      return sha1(uniqid(rand(0, 1000), true));
    }

    /**
     * @param float $value_
     * @param integer $precision_
     * @param integer $mode_
     *
     * @return float
     */
    function round($value_, $precision_=2, $mode_=PHP_ROUND_HALF_UP)
    {
      return \round($value_, $precision_, $mode_);
    }

    /**
     * @param float $value_
     * @param string $sign_
     * @param boolean $round_
     * @param integer $precision_
     * @param integer $mode_
     *
     * @return float
     */
    // FIXME i18n based solution.
    function percent($value_, $sign_='%', $round_=true, $precision_=2, $mode_=PHP_ROUND_HALF_UP)
    {
      if($round_)
        return round($value_, $precision_, $mode_).$sign_;

      return sprintf("%-.{$precision_}f%s", $value_, $sign_);
    }
    //--------------------------------------------------------------------------
?>
