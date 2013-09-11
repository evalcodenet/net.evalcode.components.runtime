<?php


namespace Components;


  /**
   * Debug_Profiler
   *
   * Method invocation profiling.
   *
   * Supports profiling in a forked child process for more accurate results.
   *
   * @package net.evalcode.components
   * @subpackage debug
   *
   * @author evalcode.net
   */
  class Debug_Profiler implements Object
  {
    // PREDEFINED PROPERTIES
    const FACTOR_BYTES_MEGABYTES=1048576;
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct()
    {
      $this->m_memoryConsumptionBefore=memory_get_usage(true);
      $this->m_timeStart=$this->m_lastSplitTimeEntry=microtime(true);
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @return \Components\Debug_Profiler
     */
    public static function profileCall(array $callable_, array $args_=array())
    {
      if(false===is_callable($callable_))
        throw new Runtime_Exception('debug/profiler', 'Valid callback expected.');

      $sessionId=static::start();

      try
      {
        $returnValue=call_user_func_array($callable_, $args_);

        $profiler=static::stop($sessionId);
        $profiler->m_returnValue=$returnValue;
      }
      catch(\Exception $e)
      {
        $profiler=static::stop($sessionId);
        $profiler->m_exception=Exception_Flat::create($e);
      }

      return $profiler;
    }

    /**
     * <p> Only use if you require exact peak memory usage etc.
     * for certain invocations.
     *
     * <p> This method may fail if shared memory is getting unavailable due to
     * size of (in-)directly passed object(s).
     *
     * <p> Invocations that directly rely on reflections may not work properly.
     *
     * <p> Forked profiling requries PHP compiled with shared memory support
     * [--enable-sysvshm] and enabled extension [pcntl].
     *
     * @return \Components\Debug_Profiler
     */
    public static function profileCallForked(array $callable_, array $args_=array())
    {
      if(false===static::isForkedProfilingSupported() || false===Memory_Shared_Shm::isSupported())
      {
        throw new Runtime_Exception('debug/profiler',
          'Forked profiling is not supported on this platform\'s configuration.'
        );
      }

      if(false===is_callable($callable_))
        throw new Runtime_Exception('debug/profiler', 'Valid callback expected.');

      $shm=Memory_Shared_Shm_Temporary::create();
      $shm->attach();

      self::$m_profileForkedArgs=$args_;
      self::$m_profileForkedCallable=$callable_;
      self::$m_profileForkedSegmentId=$shm->getSegmentId();

      $pid=pcntl_fork();

      if(-1==$pid)
      {
        throw new Runtime_Exception('debug/profiler',
          'Unable to fork child process. Forked profiling failed.'
        );
      }

      if($pid)
      {
        $pid=pcntl_wait($status);
      }
      else
      {
        ob_start();

        $sessionId=static::start();

        try
        {
          $returnValue=call_user_func_array(
            self::$m_profileForkedCallable, self::$m_profileForkedArgs
          );

          $profiler=static::stop($sessionId);
          $profiler->m_returnValue=$returnValue;
        }
        catch(\ErrorException $e)
        {
          $profiler=static::stop($sessionId);
          $profiler->m_exception=Exception_Flat::create($e);
        }
        catch(\Exception $e)
        {
          $profiler=static::stop($sessionId);
          $profiler->m_exception=Exception_Flat::create($e);
        }

        self::$m_profileForkedArgs=null;
        self::$m_profileForkedCallable=null;

        $segment=Memory_Shared_Shm::forSegment(self::$m_profileForkedSegmentId);
        $segment->attach();

        $segment->set(1, ob_get_clean());
        $segment->set(2, $profiler->result()->m_exception);
        $segment->set(3, $profiler->result()->m_memoryConsumptionAfter);
        $segment->set(4, $profiler->result()->m_memoryConsumptionBefore);
        $segment->set(5, $profiler->result()->m_memoryConsumptionPeak);
        $segment->set(6, $profiler->result()->m_posixSystemTime);
        $segment->set(7, $profiler->result()->m_posixUserTime);
        $segment->set(8, $profiler->result()->m_returnValue);
        $segment->set(9, $profiler->result()->m_timeStart);
        $segment->set(10, $profiler->result()->m_timeStop);
        $segment->set(11, $profiler->result()->m_splitTimeTable);

        exit(0);
      }

      echo $shm->get(1);

      $profiler=new static();
      $profiler->m_profiling=false;
      $profiler->m_exception=$shm->get(2);
      $profiler->m_memoryConsumptionAfter=$shm->get(3);
      $profiler->m_memoryConsumptionBefore=$shm->get(4);
      $profiler->m_memoryConsumptionPeak=$shm->get(5);
      $profiler->m_posixSystemTime=$shm->get(6);
      $profiler->m_posixUserTime=$shm->get(7);
      $profiler->m_returnValue=$shm->get(8);
      $profiler->m_timeStart=$shm->get(9);
      $profiler->m_timeStop=$shm->get(10);
      $profiler->m_splitTimeTable=$shm->get(11);

      $shm->clear();

      return $profiler;
    }

    /**
     * Start profiling session.
     *
     * Returns id for started profiling session.
     *
     * @param \Components\Debug_Profiler $profiler_
     *
     * @return integer
     */
    public static function start()
    {
      return static::push(new static());
    }

    /**
     * Stop profiling session.
     *
     * <p> Id is provided by Debug_Profiler::start() and required here
     * to prevent mistakes when encapsulating profiling sessions.
     *
     * <p> Whatever opens a session should also take care for closing it.
     *
     * @param integer $profilingSessionId_
     *
     * @return \Components\Debug_Profiler
     */
    public static function stop($profilingSessionId_)
    {
      if($profilingSessionId_!==count(self::$m_instances))
      {
        throw new Runtime_Exception('debug/profiler', sprintf(
          'Profiling session id mismatch. Inner profiling sessions must be stopped first. [%1$d].', $profilingSessionId_
        ));
      }

      return array_pop(self::$m_instances)->result();
    }

    /**
     * Add entry to split-time table of current profiling session.
     *
     * @param string $description_
     *
     * @throws \Components\Runtime_Exception
     */
    public static function split($description_)
    {
      if(null===($instance=end(self::$m_instances)))
        throw new Runtime_Exception('debug/profiler', 'No profiling session started.');

      $instance->splitTime($description_);
    }

    /**
     * @param \Components\Debug_Profiler $profiler_
     *
     * @return integer
     *
     * @internal
     */
    public static function push(Debug_Profiler $instance_)
    {
      return $instance_->m_profilingSessionId=
        array_push(self::$m_instances, $instance_);
    }

    /**
     * @return \Components\Debug_Profiler
     *
     * @internal
     */
    public static function pop()
    {
      if($instance=array_pop(self::$m_instances))
        $instance->m_profilingSessionId=null;

      return $instance;
    }

    /**
     * @return boolean
     */
    public static function isForkedProfilingSupported()
    {
      if(null===self::$m_isForkedProfilingSupported)
        self::$m_isForkedProfilingSupported=function_exists('pcntl_fork') && Memory_Shared_Shm::isSupported();

      return self::$m_isForkedProfilingSupported;
    }

    public static function disableForkedProfilingSupport()
    {
      self::$m_isForkedProfilingSupported=false;
    }

    /**
     * @return boolean
     */
    public static function isPosixSupported()
    {
      if(null===self::$m_isPosixSupported)
        self::$m_isPosixSupported=function_exists('posix_times');

      return self::$m_isPosixSupported;
    }

    public static function disablePosixSupport()
    {
      self::$m_isPosixSupported=false;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function splitTime($description_)
    {
      $time=microtime(true);

      array_push($this->m_splitTimeTable, array(
        $time-$this->m_lastSplitTimeEntry, $description_
      ));

      $this->m_lastSplitTimeEntry=$time;
    }

    public function splitTimeTable()
    {
      return $this->m_splitTimeTable;
    }

    public function profilingSessionId()
    {
      return $this->m_profilingSessionId;
    }

    public function processingTime()
    {
      return $this->result()->m_timeStop-$this->result()->m_timeStart;
    }

    public function processingTimeAsString($precision_=5)
    {
      return sprintf('%08.8s', sprintf('%-02.'.$precision_.'f',
        round($this->processingTime(), $precision_)
      ));
    }

    public function memoryConsumptionBefore()
    {
      return $this->result()->m_memoryConsumptionBefore;
    }

    public function memoryConsumptionAfter()
    {
      return $this->result()->m_memoryConsumptionAfter;
    }

    public function memoryConsumptionIncrease()
    {
      return $this->result()->m_memoryConsumptionAfter-
        $this->result()->m_memoryConsumptionBefore;
    }

    public function memoryConsumptionPeak()
    {
      return $this->result()->m_memoryConsumptionPeak;
    }

    public function memoryConsumptionAsString()
    {
      return sprintf('%-5.5s %-5.5s',
        static::formatSize($this->memoryConsumptionIncrease()),
        static::formatSize($this->memoryConsumptionPeak())
      );
    }

    public function posixUserTime()
    {
      return $this->result()->m_posixUserTime;
    }

    public function posixSystemTime()
    {
      return $this->result()->m_posixSystemTime;
    }

    public function posixTimesAsString()
    {
      if(false===static::isPosixSupported())
        return 'posix -----------/----------- user/sys';

      return sprintf('posix %011d/%011d user/sys',
        $this->posixUserTime(), $this->posixSystemTime()
      );
    }

    /**
     * @return \Components\Exception_Flat
     */
    public function exception()
    {
      return $this->result()->m_exception;
    }

    /**
     * @return mixed return value of profiled call.
     */
    public function returnValue()
    {
      return $this->m_returnValue;
    }

    /**
     * @return \Components\Debug_Profiler
     */
    public function result()
    {
      if(false===$this->m_profiling)
        return $this;

      $this->m_timeStop=microtime(true);
      $this->m_memoryConsumptionAfter=memory_get_usage(true);
      $this->m_memoryConsumptionPeak=memory_get_peak_usage(true);

      if(static::isPosixSupported())
      {
        $times=posix_times();

        $this->m_posixUserTime=$times['utime'];
        $this->m_posixSystemTime=$times['stime'];
      }

      $this->m_profiling=false;

      return $this;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf("%s@%s{profilingSessionId: %s, active: %s}",
        __CLASS__,
        $this->hashCode(),
        $this->m_profilingSessionId,
        true===$this->m_profiling?'true':'false'
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected static $m_profileForkedArgs;
    protected static $m_profileForkedCallable;
    protected static $m_profileForkedSegmentId;

    private static $m_instances=array();
    private static $m_isForkedProfilingSupported;
    private static $m_isPosixSupported;

    protected $m_splitTimeTable=array();
    protected $m_profiling=true;
    protected $m_lastSplitTimeEntry=0;
    protected $m_memoryConsumptionAfter=0;
    protected $m_memoryConsumptionBefore=0;
    protected $m_memoryConsumptionPeak=0;
    protected $m_posixSystemTime=0;
    protected $m_posixUserTime=0;
    protected $m_timeStart=0;
    protected $m_timeStop=0;
    protected $m_profilingSessionId;
    protected $m_exception;
    protected $m_returnValue;
    //-----


    // HELPERS
    protected static function formatSize($size_)
    {
      return sprintf('%02.4s', sprintf('%2f',
        round($size_/self::FACTOR_BYTES_MEGABYTES, 3)
      ));
    }
    //--------------------------------------------------------------------------
  }
?>
