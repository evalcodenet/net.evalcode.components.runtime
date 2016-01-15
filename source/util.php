<?php


    /**
     * TODO Refactor to lib/std/dbg.
     */


    // GLOBAL HELPERS & PROPERTIES
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
     * @param mixed.. $arg0_
     */
    function cinfo($arg0_/*, $arg1_..*/)
    {
      \Components\Debug::vinfo(func_get_args());

      if(1===func_num_args())
        return func_get_arg(0);
    }

    /**
     * @param mixed.. $arg0_
     */
    function cwarn($arg0_/*, $arg1_..*/)
    {
      \Components\Debug::vwarn(func_get_args());

      if(1===func_num_args())
        return func_get_arg(0);
    }

    /**
     * @param mixed.. $arg0_
     */
    function cerror($arg0_/*, $arg1_..*/)
    {
      \Components\Debug::verror(func_get_args());

      if(1===func_num_args())
        return func_get_arg(0);
    }

    /**
     * @param mixed.. $arg0_
     */
    function vardump($arg0_/*, $arg1_..*/)
    {
      echo '<pre>';
      call_user_func_array('var_dump', func_get_args());
      echo '</pre>';
    }

    /**
     * @param mixed.. $arg0_
     */
    function dmp($arg0_/*, $arg1_..*/)
    {
      call_user_func_array('vardump', func_get_args());

      die();
    }

    function backtrace()
    {
      echo '<pre>';
      debug_print_backtrace();
      echo '</pre>';
    }

    /**
     * Add an entry to split time table of current profiling session.
     *
     * @param string $description_
     */
    function profile($description_)
    {
      if(null===$GLOBALS['components_debug_profilers_current'])
        throw new \Components\Runtime_Exception_Internal('components/runtime/util', 'No profiling session started.');

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
        throw new \Components\Runtime_Exception_Internal('components/runtime/util', 'No profiling session started.');

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
     * Convert given exception to an array of scalar values.
     *
     * @param \Exception $e_
     * @param bool $includeStackTrace_
     * @param bool $stackTraceAsArray_
     *
     * @return scalar[][]
     */
    function exception_as_array(\Exception $e_, $includeStackTrace_=false, $stackTraceAsArray_=false)
    {
      if($e_ instanceof \Components\Runtime_Exception_Transformable)
        return $e_->toArray($includeStackTrace_, $stackTraceAsArray_);

      $type=Components\Type::of($e_);

      $exceptionAsArray=[
        'id'=>\math\hasho_md5($e_),
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

    /**
     * Convert stack trace of given exception to an array of scalar values.
     *
     * @param \Exception $e_
     *
     * @return scalar[][]
     */
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

    /**
     * Format details as JSON string for given exception.
     *
     * @param \Exception $e_
     * @param bool $includeStackTrace_
     * @param bool $stackTraceAsArray_
     *
     * @return string
     */
    function exception_as_json(\Exception $e_, $includeStackTrace_=false, $stackTraceAsArray_=false)
    {
      if($e_ instanceof \Components\Runtime_Exception_Transformable)
        return $e_->toJson($includeStackTrace_, $stackTraceAsArray_);

      return json_encode(exception_as_array($e_, $includeStackTrace_, $stackTraceAsArray_));
    }

    /**
     * Send header containing details for given exception.
     *
     * @param \Exception $e_
     * @param integer $code_
     */
    function exception_header(\Exception $e_, $code_=500)
    {
      if($cause=$e_->getPrevious())
        exception_header($cause, $code_);

      $firstException=false===isset($GLOBALS['components_runtime_exception_header']);

      if($firstException)
        $GLOBALS['components_runtime_exception_header']=1;
      else
        $GLOBALS['components_runtime_exception_header']++;

      header("Components-Exception-Count: $GLOBALS[components_runtime_exception_header]");

      if($GLOBALS['components_runtime_exception_header']>10)
        return;

      if(false===headers_sent())
      {
        $hash=\math\hasho_md5($e_);

        if(\Components\Runtime::isManagementAccess())
        {
          if($firstException)
          {
            header("Components-Exception-$hash: ".json_encode([
                $hash,
                $e_->getFile(),
                $e_->getLine(),
                exception_as_array($e_, true, false)
              ]), true, $code_
            );
          }
          else
          {
            header("Components-Exception-$hash: ".json_encode([
                $hash,
                $e_->getFile(),
                $e_->getLine(),
                exception_as_array($e_, true, false)
              ])
            );
          }
        }
        else
        {
          if($firstException)
            header("Components-Exception-$hash: $hash", true, $code_);
          else
            header("Components-Exception-$hash: $hash");
        }
      }
    }

    /**
     * Render & print plain text formatted details for given exception.
     *
     * @param \Exception $e_
     * @param bool $includeSource_
     * @param bool $includeStackTrace_
     */
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
         \math\hasho_md5($e_),
          $type->name(),
          $e_->getMessage(),
          $includeSource_?implode(':', [$e_->getFile(), $e_->getLine()]):'',
          $includeStackTrace_?$e_->getTraceAsString():'',
          PHP_EOL
      );

      if($cause=$e_->getPrevious())
        exception_print_cli($cause, $includeSource_, $includeStackTrace_);
    }

    /**
     * Render & print HTML formatted details for given exception.
     *
     * @param \Exception $e_
     * @param bool $includeSource_
     * @param bool $includeStackTrace_
     */
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
           \math\hasho_md5($e_),
            $type->name(),
            $e_->getMessage(),
            $includeSource_?implode(':', [$e_->getFile(), $e_->getLine()]):'',
            $includeStackTrace_?$e_->getTraceAsString():''
        );
      }
      else
      {
        printf('<h1>%1$s</h1><h2>%2$s</h2>',
         \math\hasho_md5($e_),
          $e_->getMessage()
        );

        if($includeStackTrace_)
          echo '<pre>'.$e_->getTraceAsString().'</pre>';
      }

      if($cause=$e_->getPrevious())
        exception_print_html($cause, $includeSource_, $includeStackTrace_);
    }

    /**
     * Log exception.
     *
     * @param \Exception $e_
     */
    function exception_log(\Exception $e_)
    {
      if($e_ instanceof \Components\Runtime_Exception)
      {
        $e_->log();
      }
      else
      {
        $type=get_class($e_);

        \Components\Log::error(strtolower(strtr($type, '\\_', '//')), '[%s] %s%s',
          \math\hasho_md5($e_), $type, $e_
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
      if(isset($GLOBALS['components_runtime_bootstrap_invoked']))
        return;

      $GLOBALS['components_runtime_bootstrap_invoked']=microtime(true);

      if(is_file($file=COMPONENTS_PATH_APP.'/config/environment.php'))
        @include_once $file;
      else
        @include_once dirname(__DIR__).'/config/environment.php';

      \Components\Runtime_Classloader::push(
        new \Components\Classloader_Components(\Components\Environment::pathComponents())
      );

      \Components\Runtime::create();
    }


    define('COMPONENTS_RUNTIME_VERSION_MAJOR', 0);
    define('COMPONENTS_RUNTIME_VERSION_MINOR', 1);
    define('COMPONENTS_RUNTIME_VERSION_BUILD', 0);

    define('COMPONENTS_TIMESTAMP_SIZE', strlen(time()));

    if(false===defined('COMPONENTS_APP'))
    {
      if(false===isset($_SERVER['COMPONENTS_APP']))
      {
        throw new Components\Runtime_Exception_Internal('runtime/util',
          'Missing configuration parameter [COMPONENTS_APP].'
        );
      }

      define('COMPONENTS_APP', $_SERVER['COMPONENTS_APP']);
    }

    if(false===defined('COMPONENTS_PATH_APP'))
    {
      if(isset($_SERVER['COMPONENTS_PATH_APP']))
        define('COMPONENTS_PATH_APP', $_SERVER['COMPONENTS_PATH_APP']);
      else
        define('COMPONENTS_PATH_APP', '/'.COMPONENTS_APP);
    }

    if(false===defined('COMPONENTS_ENV'))
    {
      if(false===isset($_SERVER['COMPONENTS_ENV']))
        $_SERVER['COMPONENTS_ENV']='live';

      define('COMPONENTS_ENV', $_SERVER['COMPONENTS_ENV']);
    }

    if(false===defined('COMPONENTS_APP_CODE'))
      define('COMPONENTS_APP_CODE', strtr(COMPONENTS_APP, '.', '_'));

    if(false===defined('COMPONENTS_INSTANCE_CODE'))
    {
      define('COMPONENTS_INSTANCE_CODE',
        COMPONENTS_ENV.'_'.COMPONENTS_APP_CODE
      );
    }

    if(false===defined('COMPONENTS_CACHE_NAMESPACE'))
    {
      define('COMPONENTS_CACHE_NAMESPACE',
        COMPONENTS_INSTANCE_CODE.'_'.COMPONENTS_RUNTIME_VERSION_BUILD
      );
    }

    $GLOBALS['components_debug_profilers']=[];
    $GLOBALS['components_debug_profilers_current']=null;
?>
