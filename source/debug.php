<?php


namespace Components;


  /**
   * Debug
   *
   * @api
   * @package net.evalcode.components.debug
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

    public static function appendToBodyEnabled()
    {
      return isset(self::$m_flags[self::APPEND_TO_BODY]);
    }

    public static function appendToHeadersEnabled()
    {
      return isset(self::$m_flags[self::APPEND_TO_HEADERS]);
    }

    public static function appendToBody()
    {
      if(self::$m_active && isset(self::$m_flags[self::APPEND_TO_BODY])
        && false===Environment::isLive() && Runtime::isManagementAccess())
      {
        if(0<count(self::$m_dump))
        {
          $debug=[];

          foreach(self::$m_dump as $dump)
          {
            $source=$dump[0];

            $line=0;
            $file='unknown';

            if(isset($source['file']))
              $file=$source['file'];
            if(isset($source['line']))
              $line=$source['line'];

            foreach($dump[1] as $arg)
              $debug[$file][$line][]=var_export($arg, true);
          }

          $json=json_encode($debug);

          echo <<<JS
            <script type="text/javascript">
              var cdbg=$json;
              if("undefined"!=typeof(console))
              {
                for(var dsf in cdbg)
                {
                  for(var dsl in cdbg[dsf])
                  {
                    console.debug("[components/debug] "+dsf+":"+dsl);
                    for(var ds in cdbg[dsf][dsl])
                      console.debug("[components/debug]   "+cdbg[dsf][dsl][ds]);
                  }
                }
              }
              else
              {
                document.write("<pre class=\"components_debug\"><ul>");

                for(var dsf in cdbg)
                {
                  document.write("<li>"+dsf+"<ul>");
                  for(var dsl in cdbg[dsf])
                  {
                    document.write("<li>"+dsl+"<ul>");
                    for(var ds in cdbg[dsf][dsl])
                      document.write("<li>"+cdbg[dsf][dsl][ds]+"</li>");
                    document.write("</ul></li>");
                  }
                  document.write("</ul></li>");
                }

                document.write("</ul></pre>");
              }
            </script>
JS;
        }

        // TODO Use fetchHtml

        $html='';
        if(0<count(self::$m_exceptions))
        {
          foreach(self::$m_exceptions as $exceptions)
          {
            $html.='<pre style="display:block;color:#000;background:#fff;">';
            $html.='<h2 style="color:#fff;background:#000;font:bold 15pt/20pt mono;padding:0;margin:40px 0 20px 0;">Components-Debug Exception</h2>';

            if($exception=reset($exceptions))
            {
              foreach($exceptions as $exception)
              {
                $html.='<pre style="display:block;color:#000;background:#fff;margin:10px 0 50px 0;font:normal 8pt/10pt mono;">';

                $location=[];
                if(isset($exception['file']))
                  $location[]=$exception['file'];
                if(isset($exception['line']))
                  $location[]=$exception['line'];

                $html.='<h4 style="color:#000;background:#fff;font:bold 8pt/11pt mono;padding:0;margin:0;">['.implode('::', $location).']</h4>';
                $html.='<h3 style="color:#000;background:#fff;font:bold 15pt/25pt mono;padding:0;margin:0;">'.$exception['message'].'</h3>';

                $stack=[];
                foreach($exception['trace'] as $stackTraceElement)
                {
                  $method=[];
                  if(isset($stackTraceElement['class']))
                    $method[]=$stackTraceElement['class'];
                  if(isset($stackTraceElement['function']))
                    $method[]=$stackTraceElement['function'];

                  if(false===isset($stackTraceElement['file']))
                    $stackTraceElement['file']='internal';
                  if(false===isset($stackTraceElement['line']))
                    $stackTraceElement['line']=0;

                  $stack[$stackTraceElement['file']][$stackTraceElement['line']]=implode('::', $method).'()';
                }

                foreach($stack as $file=>$lines)
                {
                  $html.="\n$file\n";
                  foreach($lines as $line=>$method)
                    $html.=sprintf("[%5d] %s\n", $line, $method);
                }

                $html.='</pre>';
              }
            }

            $html.='</pre>';
          }
        }

        if(trim($html))
          echo "<pre style=\"display:block;position:relative;float:none;clear:both;z-index:99999;zoom:1;color:#000;background:#fff;text-align:left;padding:10px 20px;margin:0;border:0 none;width:auto;height:auto;font:normal normal 10pt/12pt mono;\">$html</pre>";

      }
    }

    public static function appendToHeaders()
    {
      if(self::$m_active && isset(self::$m_flags[self::APPEND_TO_HEADERS]) && Runtime::isManagementAccess())
      {
        if(0<count(self::$m_dump))
        {
          $header=[];
          foreach(self::$m_dump as $dump)
          {
            $source=$dump[0];

            $line=0;
            $file='unknown';
            if(isset($source['file']))
              $file=$source['file'];
            if(isset($source['line']))
              $line=$source['line'];

            foreach($dump[1] as $arg)
              $header[$file][$line][]=var_export($arg, true);
          }

          header('Components-Debug: '.json_encode($header));
        }

        if(0<count(self::$m_exceptions))
          header('Components-Debug-Exceptions: '.json_encode(self::$m_exceptions));
      }
    }

    public static function dump()
    {
      static::_dump(func_get_args());
    }

    public static function dumpException(\Exception $exception_)
    {
      $exception=$exception_;

      $path=[];
      while($exception)
      {
        $trace=[];
        foreach($exception->getTrace() as $element)
        {
          $traceElement=[];
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
        $debug=[];
        foreach(self::$m_dump as $dump)
        {
          $source=$dump[0];

          $line=0;
          $file='unknown';

          if(isset($source['file']))
            $file=$source['file'];
          if(isset($source['line']))
            $line=$source['line'];

          foreach($dump[1] as $arg)
            $debug[$file][$line][]=var_export($arg, true);
        }

        $html.='<h2 style="color:#fff;background:#000;font:bold 15pt/20pt mono;padding:0;margin:40px 0 0;">Components-Debug</h2>';
        $html.='<pre style="display:block;color:#000;background:#fff;margin:10px 0 0;font:normal 8pt/11pt mono;">';

        foreach($debug as $file=>$lines)
        {
          $html.="<span style=\"display:block;color:#000;background:#fff;\">$file</span>";

          foreach($lines as $line=>$statements)
          {
            $html.="<div><span style=\"color:#000;background:#fff;margin:10px 0 0;\">$line:</span>";
            foreach($statements as $statement)
              $html.="<div>$statement</div>";
            $html.="</div>";
          }
        }

        $html.='</pre>';
      }

      if(0<count(self::$m_exceptions))
      {
        foreach(self::$m_exceptions as $exceptions)
        {
          $html.='<pre style="display:block;color:#000;background:#fff;">';
          $html.='<h2 style="color:#fff;background:#000;font:bold 15pt/20pt mono;padding:0;margin:40px 0 20px 0;">Components-Debug Exception</h2>';

          if($exception=reset($exceptions))
          {
            foreach($exceptions as $exception)
            {
              $html.='<pre style="display:block;color:#000;background:#fff;margin:10px 0 0;font:normal 8pt/10pt mono;">';

              $location=[];
              if(isset($exception['file']))
                $location[]=$exception['file'];
              if(isset($exception['line']))
                $location[]=$exception['line'];

              $html.='<h4 style="color:#000;background:#fff;font:bold 8pt/11pt mono;padding:0;margin:0;">['.implode('::', $location).']</h4>';
              $html.='<h3 style="color:#000;background:#fff;font:bold 15pt/25pt mono;padding:0;margin:0;">'.$exception['message'].'</h3>';

              $stack=[];
              foreach($exception['trace'] as $stackTraceElement)
              {
                $method=[];
                if(isset($stackTraceElement['class']))
                  $method[]=$stackTraceElement['class'];
                if(isset($stackTraceElement['function']))
                  $method[]=$stackTraceElement['function'];

                if(false===isset($stackTraceElement['file']))
                  $stackTraceElement['file']='internal';
                if(false===isset($stackTraceElement['line']))
                  $stackTraceElement['line']=0;

                $stack[$stackTraceElement['file']][$stackTraceElement['line']]=implode('::', $method).'()';
              }

              foreach($stack as $file=>$lines)
              {
                $html.="\n$file\n";
                foreach($lines as $line=>$method)
                  $html.=sprintf("[%5d] %s\n", $line, $method);
              }

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
        $plain.="=== Components-Debug Exceptions ===\n\n";
        foreach(self::$m_exceptions as $exceptions)
        {
          if(0<count($exceptions))
          {
            $plain.="-- Exception --\n\n";
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

    public static function clear()
    {
      self::$m_dump=[];
      self::$m_exceptions=[];
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_mimeTypeHandlers=array(
      'text/html'=>'fetchHtml',
      'application/json'=>'fetchJson',
      'application/xml'=>'fetchXml'
    );

    private static $m_flags=[];
    private static $m_listeners=[];
    private static $m_dump=[];
    private static $m_exceptions=[];
    private static $m_active=false;
    private static $m_verbosity=1;
    //-----


    // HELPERS
    private static function appendArray($array_, $level_=0)
    {
      $dump=[];
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

    private static function appendObject($object_, $level_=0, array &$dump_=[])
    {
      $obj=new \ReflectionObject($object_);

      $dump=[];
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
      $ini=[];
      foreach($extension_->getINIEntries() as $name=>$value)
        $ini[$name]=$value;

      $dependencies=[];
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
      $source=$source[1];
      $source['args']=0;

      /**
       * TODO [CSH] Append headers instantly if enabled.
       *
       * Debug output is lost if an unhandled exception or fatal error occurs.
       */
      self::$m_dump[]=array($source, $args_);
    }
    //--------------------------------------------------------------------------
  }
?>
