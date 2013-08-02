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
      static::_dump(func_get_args());
    }

    public static function dumpException(\Exception $exception_)
    {
      $exception=$exception_;

      $path=array();
      while($exception)
      {
        $trace=array();
        foreach($exception->getTrace() as $element)
        {
          $traceElement=array();
          if(isset($element['file']))
            $traceElement['file']=$element['file'];
          if(isset($element['line']))
            $traceElement['line']=$element['line'];
          if(isset($element['class']))
            $traceElement['class']=$element['class'];
          if(isset($element['function']))
            $traceElement['function']=$element['function'];

          $trace[]=$traceElement;
        }

        $path[]=array(
          'file'=>$exception->getFile(),
          'line'=>$exception->getLine(),
          'message'=>$exception->getMessage(),
          'trace'=>$trace
        );

        $exception=$exception->getPrevious();
      }

      if(count(($path)))
        self::$m_exceptions[]=$path;
    }

    public static function fetchHtml()
    {
      $html='';

      if(0<count(self::$m_dump))
      {
        $html.='<h2 style="color:#000;background:none;font:bold 15pt/15pt mono;padding:0;margin:40px 0 0;">Components-Debug</h2>';

        foreach(self::$m_dump as $dump)
        {
          $html.='<pre style="display:block;color:#000;background:none;margin:20px 0 0;font:normal 8pt/10pt mono;">';

          $source=$dump[0];

          $location='';
          if(isset($source['file']))
            $location[]="$source[file]::";
          if(isset($source['line']))
            $location[]=$source['line'];

          if(1>count($location))
            $location[]='unknown';

          $sourceInfo=array(
            '['.\Components\String::truncate(implode($location), 90, '..', null, \Components\String::TRUNCATE_REVERSE).']'
          );

          if(isset($source['class']))
            $sourceInfo[]=$source['class'];
          if(isset($source['type']))
            $sourceInfo[]=$source['type'];
          if(isset($source['function']))
            $sourceInfo[]=$source['function'];

          $html.=print_r(implode(' ', $sourceInfo).'(): ', true);

          foreach($dump[1] as $arg)
            $html.=var_export($arg, true);

          $html.='</pre>';
        }
      }

      if(0<count(self::$m_exceptions))
      {
        foreach(self::$m_exceptions as $exceptions)
        {
          $html.='<pre style="display:block;color:#000;background:none;">';
          $html.='<h2 style="color:#000;background:none;font:bold 15pt/15pt mono;padding:0;margin:40px 0 0;">Components-Exception</h2>';

          if($exception=reset($exceptions))
          {
            foreach($exceptions as $exception)
            {
              $location=array();
              if(isset($exception['file']))
                $location[]="$exception[file]::";

              if(isset($exception['line']))
                $location[]=$exception['line'];
              else
                $location[]=0;

              $html.='<pre style="display:block;color:#000;background:none;margin:20px 0 0;font:normal 8pt/10pt mono;">';
              $html.='<h3 style="color:#000;background:none;font:bold 10pt/10pt mono;padding:0;margin:5px 0;">'.$exception['message'].'</h3>';
              $html.='<h4 style="color:#000;background:none;font:bold 8pt/8pt mono;padding:0;margin:0 0 20px 0;opacity:0.5;">'.implode($location).'</h4>';
              $html.=print_r($exception['trace'], true);
              $html.='</pre>';
            }
          }

          $html.='</pre>';
        }
      }

      if(trim($html))
        return "<pre style=\"display:block;position:relative;float:none;clear:both;z-index:99999;zoom:1;color:#000;background:#fff;text-align:left;padding:10px 20px;margin:0;border:0 none;width:auto;height:auto;font:normal normal 10pt/12pt mono;\">$html</pre>";

      return '';
    }

    public static function fetchPlain()
    {
      $plain='';

      if(0<count(self::$m_dump))
      {
        $plain.="=== Components-Debug ===\n\n";
        foreach(self::$m_dump as $dump)
          $plain.=print_r($dump, true);

        $plain.="\n\n";
      }

      if(0<count(self::$m_exceptions))
      {
        $plain.="=== Components-Exceptions ===\n\n";
        foreach(self::$m_exceptions as $exceptions)
        {
          if(0<count($exceptions))
          {
            $plain.="-- Components-Exception --\n\n";
            $plain.=print_r($exceptions, true);
            $plain.="\n\n";
          }
        }

        $plain.="\n\n";
      }

      return $plain;
    }

    public static function fetchJson()
    {
      // TODO Implement
      return json_encode(array('dumps'=>self::$m_dump, 'exceptions'=>self::$m_exceptions));
    }

    public static function fetchXml()
    {
      // TODO Implement
      $xml='<debug>';

      if(0<count(self::$m_dump))
      {
        $xml.='<dumps>';
        foreach(self::$m_dump as $entry)
        {
          $xml.='<dump>';

          $xml.='<source>';
          $source=$entry[0];
          if(isset($source['file']))
            $xml.="<file>$source[file]</file>";
          if(isset($source['line']))
            $xml.="<line>$source[line]</line>";
          if(isset($source['class']))
            $xml.="<class>$source[class]</class>";
          if(isset($source['type']))
            $xml.="<type>$source[type]</type>";
          if(isset($source['function']))
            $xml.="<function>$source[function]</function>";
          $xml.='</source>';

          $xml.='<content>';
          foreach($entry[1] as $arg)
          {
            // TODO XML
            $xml.=json_encode($arg);
          }
          $xml.='</content>';

          $xml.='</dump>';
        }
        $xml.='</dumps>';
      }


      if(0<count(self::$m_exceptions))
      {
        $xml.='<exceptions>';

        foreach(self::$m_exceptions as $exceptions)
        {
          if(0<count($exceptions))
          {
            $xml.='<exception>';
            $xml.=json_encode($exceptions);
            $xml.='</exception>';
          }
        }

        $xml.='</exceptions>';
      }

      return "$dump</debug>";
    }

    public static function flushHeaders()
    {
      if(0<count(self::$m_dump) || 0<count(self::$m_exceptions))
      {
        if(static::appendToHeaders())
        {
          if(0<count(self::$m_dump))
            header('Components-Debug: '.json_encode(self::$m_dump));
          if(0<count(self::$m_exceptions))
            header('Components-Exceptions: '.json_encode(self::$m_exceptions));
        }
      }
    }

    public static function clear()
    {
      self::$m_dump=array();
      self::$m_exceptions=array();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_mimeTypeHandlers=array(
      'text/html'=>'fetchHtml',
      'application/json'=>'fetchJson',
      'application/xml'=>'fetchXml'
    );

    private static $m_flags=array();
    private static $m_listeners=array();
    private static $m_dump=array();
    private static $m_exceptions=array();
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

    /*package*/ static function _dump(array $args_)
    {
      $source=debug_backtrace(false);
      $source=$source[2];
      $source['args']=0;

      self::$m_dump[]=array($source, $args_);
    }
    //--------------------------------------------------------------------------
  }
?>
