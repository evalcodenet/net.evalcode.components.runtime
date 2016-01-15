<?php


namespace io;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage io
     *
     * @author evalcode.net
     */


    // PREDEFINED PROPERTIES
    define('LIBSTD_IO_BYTES_KB', 1024);
    define('LIBSTD_IO_BYTES_MB', 1048576);
    define('LIBSTD_IO_BYTES_GB', 1073741824);
    define('LIBSTD_IO_BYTES_TB', 1099511627776);
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    function bytesToKb($bytes_, $round_=2)
    {
      return round($bytes_/LIBSTD_IO_BYTES_KB, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    function bytesToMb($bytes_, $round_=2)
    {
      return round($bytes_/LIBSTD_IO_BYTES_MB, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    function bytesToGb($bytes_, $round_=2)
    {
      return round($bytes_/LIBSTD_IO_BYTES_GB, $round_);
    }

    /**
     * @param integer $bytes_
     * @param integer $round_
     *
     * @return float
     */
    function bytesToTb($bytes_, $round_=2)
    {
      return round($bytes_/LIBSTD_IO_BYTES_TB, $round_);
    }

    /**
     * @param string $filename_
     *
     * @return string
     */
    function fileExtension($filename_)
    {
      return pathinfo($filename_, PATHINFO_EXTENSION);
    }

    /**
     * @param string $filepath_
     * @param boolean $stripFileExtension_
     *
     * @return string
     */
    function fileName($filepath_, $stripFileExtension_=false)
    {
      if($stripFileExtension_)
        return str_replace('.'.pathinfo($filepath_, PATHINFO_EXTENSION), '', basename($filepath_));

      return basename($filepath_);
    }

    /**
     * @param string $parent_
     * @param Closure $closure_
     * @param boolean $skipHidden_
     *
     * @param @internal Closure $__glob
     */
    function pathApply($parent_, \Closure $closure_, $skipHidden_=true,
      $__glob=null)
    {
      if(null===$__glob)
      {
        if($skipHidden_)
          $__glob=function($path_) {return glob("$path_/*", GLOB_NOSORT);};
        else
          $__glob=function($path_) {return glob("$path_/{.*,*}", GLOB_NOSORT|GLOB_BRACE);};
      }

      foreach($__glob($parent_) as $path)
      {
        if(false===$skipHidden_ && '.'===substr($path, strlen($path)-1))
          continue;

        $closure_($path);

        if(is_dir($path))
          pathApply($path, $closure_, $skipHidden_, $__glob);
      }
    }

    /**
     * @param string $parent_
     * @param string $filterRegex_
     * @param Closure $closure_
     * @param boolean $skipHidden_
     *
     * @param @internal Closure $__glob
     */
    function pathApplyFiltered($parent_, $filterRegex_, \Closure $closure_, $skipHidden_=true,
      $__glob=null)
    {
      if(null===$__glob)
      {
        if($skipHidden_)
          $__glob=function($path_) {return glob("$path_/*", GLOB_NOSORT);};
        else
          $__glob=function($path_) {return glob("$path_/{.*,*}", GLOB_NOSORT|GLOB_BRACE);};
      }

      foreach($__glob($parent_) as $path)
      {
        if(false===$skipHidden_ && '.'===substr($path, strlen($path)-1))
          continue;

        if(preg_match($filterRegex_, $path))
          $closure_($path);

        if(is_dir($path))
          pathApplyFiltered($path, $filterRegex_, $closure_, $skipHidden_, $__glob);
      }
    }

    /**
     * @param string $parent_
     * @param boolean $skipHidden_
     * @param string[] $paths_
     *
     * @param @internal Closure $__glob
     *
     * @return string[]
     */
    function pathList($parent_, $skipHidden_=true, array &$paths_=[],
      $__glob=null)
    {
      if(null===$__glob)
      {
        if($skipHidden_)
          $__glob=function($path_) {return glob("$path_/*", GLOB_NOSORT);};
        else
          $__glob=function($path_) {return glob("$path_/{.*,*}", GLOB_NOSORT|GLOB_BRACE);};
      }

      foreach($__glob($parent_) as $path)
      {
        if(false===$skipHidden_ && '.'===substr($path, strlen($path)-1))
          continue;

        $paths_[]=$path;

        if(is_dir($path))
          pathList($path, $skipHidden_, $paths_, $__glob);
      }

      return $paths_;
    }

    /**
     * @param string $parent_
     * @param string $filterRegex_
     * @param boolean $skipHidden_
     * @param string[] $paths_
     *
     * @param @internal Closure $__glob
     *
     * @return string[]
     */
    function pathListFiltered($parent_, $filterRegex_, $skipHidden_=true, array &$paths_=[],
      $__glob=null)
    {
      if(null===$__glob)
      {
        if($skipHidden_)
          $__glob=function($path_) {return glob("$path_/*", GLOB_NOSORT);};
        else
          $__glob=function($path_) {return glob("$path_/{.*,*}", GLOB_NOSORT|GLOB_BRACE);};
      }

      foreach($__glob($parent_) as $path)
      {
        if(false===$skipHidden_ && '.'===substr($path, strlen($path)-1))
          continue;

        if(preg_match($filterRegex_, $path))
          $paths_[]=$path;

        if(is_dir($path))
          pathListFiltered($path, $filterRegex_, $skipHidden_, $paths_, $__glob);
      }

      return $paths_;
    }
    //--------------------------------------------------------------------------
?>
