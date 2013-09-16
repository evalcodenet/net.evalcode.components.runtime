<?php


  if(false===defined('COMPONENTS_INSTANCE_NAMESPACE'))
    define('COMPONENTS_INSTANCE_NAMESPACE', md5(__DIR__));
  if(false===defined('COMPONENTS_LAST_UPDATE'))
    define('COMPONENTS_LAST_UPDATE', filemtime(dirname(__DIR__)));
  if(false===defined('COMPONENTS_CACHE_NAMESPACE'))
    define('COMPONENTS_CACHE_NAMESPACE', md5(COMPONENTS_INSTANCE_NAMESPACE.COMPONENTS_LAST_UPDATE));

  if(false===defined('COMPONENTS_RUNTIME_VERSION_MAJOR'))
  {
    define('COMPONENTS_RUNTIME_VERSION_MAJOR', 0);
    define('COMPONENTS_RUNTIME_VERSION_MINOR', 1);
    define('COMPONENTS_RUNTIME_VERSION_REVISION', COMPONENTS_LAST_UPDATE);
  }

  define('COMPONENTS_TIMESTAMP_SIZE', strlen(time()));

  $GLOBALS['components_debug_profilers']=array();
  $GLOBALS['components_debug_profilers_current']=null;


  /**
   * @param mixed.. $arg0_
   */
  function dump($arg0_/*, $arg1_..*/)
  {
    \Components\Debug::_dump(func_get_args());

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
      throw new Runtime_Exception('components/runtime/util', 'No profiling session started.');

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
      throw new Runtime_Exception('components/runtime/util', 'No profiling session started.');

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

    $entries=array();
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
    return (int)str_replace(
      array('a', 'b', 'c', 'd', 'e', 'f'),
      array('11', '12', '13', '14', '15', '16'),
      spl_object_hash($object_)
    );
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

  function exception_as_json(\Exception $e_)
  {
    if($e_ instanceof \Components\Runtime_Exception
      || $e_ instanceof \Components\Runtime_ErrorException)
      return $e_->toJson();

    $type=get_class($e_);

    return json_encode(array(
      'type'=>$type,
      'code'=>$e_->getCode(),
      'namespace'=>strtolower(strtr($type, '\\_', '//')),
      'message'=>$e_->getMessage(),
      'stack'=>$e_->getTraceAsString()
    ));
  }

  function exception_header(\Exception $e_)
  {
    if(headers_sent())
      return;

    if($e_ instanceof \Components\Runtime_Exception
      || $e_ instanceof \Components\Runtime_ErrorException)
    {
      $e_->sendHeader();
    }
    else
    {
      header('HTTP/1.1 500 Internal Server Error', true, 500);

      $hash=exception_log_hash($e_);
      header("Components-Exception: $hash");
      if(\Components\Runtime::isManagementAccess())
        header("$hash: ".exception_as_json($e_));
    }
  }

  function exception_log(\Exception $e_)
  {
    if($e_ instanceof \Components\Runtime_Exception
      || $e_ instanceof \Components\Runtime_ErrorException)
      $e_->log();
    else
      \Components\Log::error(strtolower(strtr(get_class($e_), '\\_', '//')), '[%s] %s', exception_log_hash($e_), $e_);
  }

  function exception_log_hash(\Exception $exception_)
  {
    return md5(object_hash($exception_));
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
