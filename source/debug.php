<?php


namespace Components;


  /**
   * Debug
   *
   * @package net.evalcode.components
   * @subpackage debug
   *
   * @author evalcode.net
   *
   * TODO Finish ...
   */
  class Debug
  {
    // PREDEFINED PROPERTIES
    const APPEND_TO_HEADERS='APPEND_TO_HEADERS';
    const APPEND_TO_BODY='APPEND_TO_BODY';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    public static function active()
    {
      return self::$m_active;
    }

    public static function activate($toggle_=true)
    {
      self::$m_active=true===$toggle_;

      self::notifyListeners();
    }

    public static function enable($flag_/*, $flag1_, ...*/)
    {
      foreach(func_get_args() as $arg)
        self::$m_flags[$arg]=true;

      self::notifyListeners();
    }

    public static function disable($flag_/*, $flag1_, ...*/)
    {
      foreach(func_get_args() as $arg)
        self::$m_flags[$arg]=null;

      self::notifyListeners();
    }

    public static function enabled($flag_)
    {
      return isset(self::$m_flags[$flag_]);
    }

    public static function areEnabled($flag_/*, $flag1_, ...*/)
    {
      $i=0;
      foreach(func_get_args() as $arg)
      {
        if(false===isset(self::$m_flags[$arg]))
          $i++;
      }

      return 0===$i;
    }

    public static function addFlagListener(\Closure $callbleListener_)
    {
      self::$m_listeners[]=$callbleListener_;

      // Initialize ...
      $callbleListener_(self::$m_active, self::$m_flags);
    }

    public static function verbosity($verbosity_=null)
    {
      if(null===$verbosity_)
        return self::$m_verbosity;

      return self::$m_verbosity=$verbosity_;
    }

    public static function appendToHeaders()
    {
      return isset(self::$m_flags[self::APPEND_TO_HEADERS]);
    }

    public static function appendToBody()
    {
      return isset(self::$m_flags[self::APPEND_TO_BODY]);
    }

    public static function dump()
    {
      ob_start();
      foreach(func_get_args() as $arg)
        var_export($arg);

      self::$m_dump[]=ob_get_clean();
    }

    public static function fetch()
    {
      $dump='';
      foreach(self::$m_dump as $chunk)
        $dump.=$chunk;

      return $dump;
    }

    public static function fetchHtml()
    {
      // TODO Implement
      $dump='<pre>';
      foreach(self::$m_dump as $chunk)
        $dump.=$chunk;

      return "$dump</pre>";
    }

    public static function fetchPlain()
    {
      // TODO Implement
      $dump='';
      foreach(self::$m_dump as $arg)
        $dump.=$arg;

      return $dump;
    }

    public static function fetchJson()
    {
      // TODO Implement
      return json_encode(self::$m_dump);
    }

    public static function fetchXml()
    {
      // TODO Implement
      $dump='<debug>';
      foreach(self::$m_dump as $chunk)
        $dump.=$chunk;

      return "$dump</debug>";
    }

    public static function flush()
    {
      echo self::fetch();
      self::clear();
    }

    public static function flushPlain()
    {
      echo self::fetchPlain();
      self::clear();
    }

    public static function flushHtml()
    {
      echo self::fetchHtml();
      self::clear();
    }

    public static function flushJson()
    {
      echo self::fetchJson();
      self::clear();
    }

    public static function flushXml()
    {
      echo self::fetchXml();
      self::clear();
    }

    public static function clear()
    {
      self::$m_dump=array();
    }

    public static function isEmpty()
    {
      return 1>count(self::$m_dump);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_flags=array();
    private static $m_listeners=array();
    private static $m_dump=array();
    private static $m_active=false;
    private static $m_verbosity=1;
    //-----


    // HELPERS
    private static function appendArray($array_, $level_=0)
    {
      $dump=array();
      foreach($array_ as $key=>$value)
      {
        $dump[]=array(
          'key'=>self::appendScalar($key),
          'value'=>is_array($value)?self::appendArray($value, ++$level_):self::appendScalar($value),
          'level'=>$level_
        );
      }

      return $dump;
    }

    private static function appendObject($object_, $level_=0, array &$dump_=array())
    {
      $obj=new \ReflectionObject($object_);

      $dump=array();
      if(1<self::$m_verbosity)
        $dump['id']=object_hash($object_);
      $dump=array_merge($dump, self::appendType(get_class($object_)));
      $dump['type']='object';

      return $dump;
    }

    private static function appendType($type_)
    {
      $type=new \ReflectionClass($type_);

      $dump=array(
        'type'=>'class',
        'class'=>self::appendScalar($type->name),
        'internal'=>self::appendScalar($type->isInternal())
      );

      if(1<self::$m_verbosity)
      {
        if($type->isInternal())
        {
          $dump['source']=array(
            'extension'=>self::appendExtension($type->getExtension())
          );
        }
        else
        {
          $dump['source']=array(
            'file'=>self::appendScalar($type->getFileName()),
            'line_start'=>self::appendScalar($type->getStartLine()),
            'line_end'=>self::appendScalar($type->getEndLine())
          );
        }
      }

      return $dump;
    }

    private static function appendExtension(\ReflectionExtension $extension_)
    {
      $ini=array();
      foreach($extension_->getINIEntries() as $name=>$value)
        $ini[$name]=$value;

      $dependencies=array();
      foreach($extension_->getDependencies() as $dependency)
        $dependencies[]=$dependency;

      return array(
        'name'=>self::appendScalar($extension_->name),
        'ini'=>self::appendArray($ini),
        'dependencies'=>$dependencies
      );
    }

    private static function appendScalar($mixed_)
    {
      if(5>self::$m_verbosity)
        return $mixed_;

      if(6>self::$m_verbosity)
        return sprintf('%1$s{%2$s}', gettype($mixed_), $mixed_);

      if(is_null($mixed_))
        return array('type'=>'null', 'numeric'=>'false', 'size'=>'0', 'value'=>'null');

      return array('type'=>gettype($mixed_), 'numeric'=>is_numeric($mixed_), 'size'=>mb_strlen($mixed_), 'value'=>$mixed_);
    }

    private static function notifyListeners()
    {
      foreach(self::$m_listeners as $listener)
        $listener(self::$m_active, self::$m_flags);
    }
    //--------------------------------------------------------------------------
  }
?>
