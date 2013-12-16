<?php


  if(false===defined('COMPONENTS_INSTANCE_NAMESPACE'))
    define('COMPONENTS_INSTANCE_NAMESPACE', substr(md5(__DIR__), 0, 6));
  if(false===defined('COMPONENTS_LAST_UPDATE'))
    define('COMPONENTS_LAST_UPDATE', 0);
  if(false===defined('COMPONENTS_CACHE_NAMESPACE'))
    define('COMPONENTS_CACHE_NAMESPACE', COMPONENTS_INSTANCE_NAMESPACE.'_'.COMPONENTS_LAST_UPDATE);

  if(false===defined('COMPONENTS_RUNTIME_VERSION_MAJOR'))
  {
    define('COMPONENTS_RUNTIME_VERSION_MAJOR', 0);
    define('COMPONENTS_RUNTIME_VERSION_MINOR', 1);
    define('COMPONENTS_RUNTIME_VERSION_REVISION', COMPONENTS_LAST_UPDATE);
  }

  define('COMPONENTS_TIMESTAMP_SIZE', strlen(time()));

  $GLOBALS['components_debug_profilers']=[];
  $GLOBALS['components_debug_profilers_current']=null;


  /**
   * @param mixed.. $arg0_
   */
  function dump($arg0_/*, $arg1_..*/)
  {
    \Components\Debug::vinfo(func_get_args());

    if(1===func_num_args())
      return func_get_arg(0);
  }

  /**
   * @return string
   */
  function hostname()
  {
    if(false===isset($GLOBALS['components_hostname']))
    {
      if(isset($_SERVER['HTTP_HOST']))
        $GLOBALS['components_hostname']=$_SERVER['HTTP_HOST'];

      $GLOBALS['components_hostname']=gethostname();
    }

    return $GLOBALS['components_hostname'];
  }

  /**
   * Add an entry to split time table of current profiling session.
   *
   * @param string $description_
   */
  function profile($description_)
  {
    if(null===$GLOBALS['components_debug_profilers_current'])
      throw new \Components\Runtime_Exception('components/runtime/util', 'No profiling session started.');

    $GLOBALS['components_debug_profilers_current']->splitTime($description_);
  }

  /**
     * Start a profiling session.
   */
  function profile_begin()
  {
    $GLOBALS['components_debug_profilers_current']=new \Components\Debug_Profiler();
    \Components\Debug_Profiler::push($GLOBALS['components_debug_profilers_current']);
    array_push($GLOBALS['components_debug_profilers'], $GLOBALS['components_debug_profilers_current']);
  }

  /**
     * Stop current profiling session.
   *
   * @return \Components\Debug_Profiler
   *
   * @throws \Components\Runtime_Exception If no profiling session is started.
   */
  function profile_end()
  {
    if(null===$GLOBALS['components_debug_profilers_current'])
      throw new \Components\Runtime_Exception('components/runtime/util', 'No profiling session started.');

    $profiler=$GLOBALS['components_debug_profilers_current']->result();
    \Components\Debug_Profiler::pop($profiler);

    if(false===($GLOBALS['components_debug_profilers_current']=array_pop($GLOBALS['components_debug_profilers'])))
      $GLOBALS['components_debug_profilers_current']=null;

    return $profiler;
  }

  /**
   * Stop current profiling session and dump split time table.
   *
   * @return \Components\Debug_Profiler
   *
   * @throws \Components\Runtime_Exception If no profiling session is started.
   */
  function profile_end_dump()
  {
    $profiler=profile_end();

    $entries=[];
    $total=0;
    foreach($profiler->splitTimeTable() as $entry)
    {
      $entries[]=sprintf('%s: %.5fs', $entry[1], $entry[0]);
      $total+=$entry[0];
    }

    $entries[]=sprintf('TOTAL: %.5fs', $total);

    dump($entries);

    return $profiler;
  }

  /**
   * @param integer $int_
   *
   * @return integer
   */
  function integer_hash($int_)
  {
    return 0x811c9dc5^$int_;
  }

  /**
   * @param integer.. $int0_
   *
   * @return integer
   */
  function integer_hash_m($int0_/*, $int1_, $int2_, ..*/)
  {
    $hash=0;
    foreach(func_get_args() as $int)
      $hash=(0x811c9dc5*$hash)^$int;

    return $hash;
  }

  /**
   * @param integer[] $integers_
   * @return integer
   */
  function integer_hash_a(array $integers_)
  {
    $hash=0;
    foreach($integers_ as $int)
      $hash=(0x811c9dc5*$hash)^$int;

    return $hash;
  }

  /**
   * @param float $float_
   *
   * @return integer
   */
  function float_hash($float_)
  {
    return 0x811c9dc5^$float_;
  }

  /**
   * @param float.. $float0_
   *
   * @return integer
   */
  function float_hash_m($float0_/*, $float1_, $float2_, ..*/)
  {
    $hash=0;
    foreach(func_get_args() as $float)
      $hash=(0x811c9dc5*$hash)^$float;

    return $hash;
  }

  /**
   * @param float[] $float_
   *
   * @return integer
   */
  function float_hash_a(array $float_)
  {
    $hash=0;
    foreach($integers_ as $float)
      $hash=(0x811c9dc5*$hash)^$float;

    return $hash;
  }

  /**
   * @param mixed $object_
   *
   * @return integer
   */
  function object_hash($object_)
  {
    $hash=str_replace(
      ['a', 'b', 'c', 'd', 'e', 'f', '0'],
      ['11', '12', '13', '14', '15', '16', ''],
      spl_object_hash($object_)
    );

    return $hash;
  }

  /**
   * @param mixed $object_
   *
   * @return string
   */
  function object_hash_md5($object_)
  {
    return md5(spl_object_hash($object_));
  }

  /**
   * @param string $string_
   *
   * @return integer
   */
  function string_hash($string_)
  {
    return string_hash_fnv($string_);
  }

  /**
   * @param string $string_
   *
   * @return integer
   */
  function string_hash_ap($string_)
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
  function string_hash_bkdr($string_)
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
  function string_hash_bp($string_)
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
  function string_hash_dek($string_)
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
  function string_hash_djb($string_)
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
  function string_hash_fnv($string_)
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
  function string_hash_js($string_)
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
   * @return integer
   */
  function string_hash_pjw($string_)
  {
    $hash=0;
    $test=0;

    $len=strlen($string_);

    for($i=0; $i<$len; $i++)
    {
      $hash=($hash<<4)+ord($string_[$i]);

      if(0!=($test=$hash&1152921504338411520))
        $hash=(($hash^($test>>24)) & ~1152921504338411520);
    }

    return $hash;
  }

  /**
   * @param string $string_
   *
   * @return integer
   */
  function string_hash_sdbm($string_)
  {
    $hash=0;

    $len=strlen($string_);

    $i=0;
    for($i=0; $i<$len; $i++)
      $hash=ord($string_[$i])+($hash<<6)+($hash<<16)-$hash;

    return $hash;
  }

  function exception_as_array(\Exception $e_, $includeStackTrace_=false, $stackTraceAsArray_=false)
  {
    if($e_ instanceof \Components\Runtime_Exception
      || $e_ instanceof \Components\Runtime_ErrorException)
      return $e_->toArray($includeStackTrace_, $stackTraceAsArray_);

    $type=Components\Type::of($e_);

    $exceptionAsArray=[
      'type'=>$type->name(),
      'code'=>$e_->getCode(),
      'namespace'=>$type->ns()->name(),
      'message'=>$e_->getMessage(),
      'file'=>$e_->getFile(),
      'line'=>$e_->getLine()
    ];

    if($includeStackTrace_ && $stackTraceAsArray_)
      $exceptionAsArray['stack']=exception_stacktrace_as_array($e_);
    else if($includeStackTrace_)
      $exceptionAsArray['stack']=$e_->getTraceAsString();

    return $exceptionAsArray;
  }

  function exception_stacktrace_as_array(\Exception $e_)
  {
    $trace=[];

    foreach($e_->getTrace() as $element)
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

    return $trace;
  }

  function exception_as_json(\Exception $e_, $includeStackTrace_=false, $stackTraceAsArray_=false)
  {
    if($e_ instanceof \Components\Runtime_Exception
      || $e_ instanceof \Components\Runtime_ErrorException)
      return $e_->toJson($includeStackTrace_, $stackTraceAsArray_);

    return json_encode(exception_as_array($e_, $includeStackTrace_, $stackTraceAsArray_));
  }

  function exception_header(\Exception $e_, $includeStackTrace_=false, $stackTraceAsArray_=false)
  {
    if(false===headers_sent() && \Components\Runtime::isManagementAccess())
    {
      $hash=object_hash_md5($e_);
      header("Components-Exception-$hash: ".json_encode([
        $hash,
        $e_->getFile(),
        $e_->getLine(),
        exception_as_array($e_, $includeStackTrace_, $stackTraceAsArray_)
      ]), true, 500);
    }
  }

  function exception_print_cli(\Exception $e_, $includeSource_=false, $includeStackTrace_=false)
  {
    $type=Components\Type::of($e_);

    printf('
      [%1$s] %2$s in %4$s
      %6$s
      %3$s
      %6$s
      %5$s
      %6$s',
        object_hash_md5($e_),
        $type->name(),
        $e_->getMessage(),
        $includeSource_?implode(':', [$e_->getFile(), $e_->getLine()]):'',
        $includeStackTrace_?$e_->getTraceAsString():'',
        PHP_EOL
    );
  }

  function exception_print_html(\Exception $e_, $includeSource_=false, $includeStackTrace_=false)
  {
    if($includeSource_)
    {
      $type=Components\Type::of($e_);

      printf('
        <h1 style="color:black;background:white;font:17px/20px mono;text-align:left;margin:0;padding:0;">[%1$s] %2$s</h1>
        <h2 style="color:black;background:white;font:15px/17px mono;text-align:left;margin:0;padding:0;">%3$s</h2>
        <h3 style="color:black;background:white;font:13px/15px mono;text-align:left;margin:0;padding:0;">%4$s</h3>
        <pre style="color:black;background:white;font:11px/13px mono;text-align:left;margin:0;padding:0;">%5$s</pre>',
          object_hash_md5($e_),
          $type->name(),
          $e_->getMessage(),
          $includeSource_?implode(':', [$e_->getFile(), $e_->getLine()]):'',
          $includeStackTrace_?$e_->getTraceAsString():''
      );
    }
    else
    {
      printf('<h1>%1$s</h1><h2>%2$s</h2>',
        object_hash_md5($e_),
        $e_->getMessage()
      );

      if($includeStackTrace_)
        echo '<pre>'.$e_->getTraceAsString().'</pre>';
    }
  }

  function exception_log(\Exception $e_)
  {
    if($e_ instanceof \Components\Runtime_Exception
      || $e_ instanceof \Components\Runtime_ErrorException)
    {
      $e_->log();
    }
    else
    {
      $type=get_class($e_);

      \Components\Log::error(strtolower(strtr($type, '\\_', '//')), '[%s] %s%s',
        object_hash_md5($e_), $type, $e_
      );
    }
  }


  /**
   * Bootstrap Components Runtime
   *
   * @author evalcode.net
   */
  function runtime_bootstrap()
  {
    if(defined(__METHOD__))
      return;

    define(__METHOD__, true);

    if(false===defined('COMPONENTS_ENVIRONMENT_CONFIG'))
      define('COMPONENTS_ENVIRONMENT_CONFIG', dirname(dirname(__DIR__)).'/app/config/environment.php');

    include_once COMPONENTS_ENVIRONMENT_CONFIG;

    \Components\Runtime_Classloader::push(
      new \Components\Classloader_Components(\Components\Environment::pathComponents())
    );

    \Components\Runtime::create();
  }
?>
