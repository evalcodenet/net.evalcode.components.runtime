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


  function string_hash($string_)
  {
    $hash=0;

    if(false===is_string($string_))
      $string_=(string)$string_;

    $len=strlen($string_);
    for($i=0; $i<$len; $i++)
    {
      // FIXME (CSH) Stay inside 32bit ...
      $hash=31*$hash+ord($string_[$i]);
    }

    return $hash;
  }

  function integer_hash($int0_/*, $int1_, $int2_, ..*/)
  {
    if(false===is_array($args=func_get_arg(0)))
      $args=func_get_args();

    $hash=0;
    foreach($args as $arg)
    {
      // FIXME (CSH) Stay inside 32bit ...
      $hash=31*$hash+$arg;
    }

    return $hash;
  }

  function float_hash($float0_/*, $float1_, $float2_, ..*/)
  {
    if(false===is_array($args=func_get_arg(0)))
      $args=func_get_args();

    $hash=0;
    foreach($args as $arg)
      $hash=31*$hash+$arg;

    return $hash;
  }

  function object_hash($object_)
  {
    return spl_object_hash($object_);
  }

  function dump()
  {
    call_user_func_array(array('Components\\Debug', 'dump'), func_get_args());
  }

  /**
   * Add an entry to split time table of current profiling session.
   *
   * @param string $description_
   */
  function profile($description_)
  {
    if(null===$GLOBALS['components_debug_profilers_current'])
      throw new Runtime_Exception('util', 'No profiling session started.');

    $GLOBALS['components_debug_profilers_current']->splitTime($description_);
  }

  /**
   * Start a profiling session.
   */
  function profile_begin()
  {
    $GLOBALS['components_debug_profilers_current']=new Components\Debug_Profiler();
    Components\Debug_Profiler::push($GLOBALS['components_debug_profilers_current']);
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
      throw new Runtime_Exception('util', 'No profiling session started.');

    $profiler=$GLOBALS['components_debug_profilers_current']->result();
    Components\Debug_Profiler::pop($profiler);

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
?>
