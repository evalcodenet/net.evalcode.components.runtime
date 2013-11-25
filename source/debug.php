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
    const ERROR=1;
    const WARN=2;
    const INFO=4;

    const MARKUP='MARKUP';
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * Determine whether debugging is active.
     *
     * @return boolean
     */
    public static function active()
    {
      return self::$m_active;
    }

    /**
     * Activate debugging.
     */
    public static function activate()
    {
      self::$m_active=true;

      self::notifyListeners();
    }

    /**
     * Deactivate debugging.
     */
    public static function deactivate()
    {
      self::$m_active=false;

      self::notifyListeners();
    }

    /**
     * Check whether given debug flags are set.
     *
     * @param integer... $flags_
     */
    public static function enabled($flag_/*, $flag1_, ...*/)
    {
      if(0===func_num_args())
        return isset(self::$m_flags[$flag_]);

      $i=0;
      foreach(func_get_args() as $arg)
      {
        if(false===isset(self::$m_flags[$arg]))
          $i++;
      }

      return 0===$i;
    }

    /**
     * Enable debug flags.
     *
     * @param integer... $flags_
     */
    public static function enable($flag_/*, $flag1_, ...*/)
    {
      foreach(func_get_args() as $arg)
        self::$m_flags[$arg]=true;

      self::notifyListeners();
    }

    /**
     * Disnable debug flags.
     *
     * @param integer... $flags_
     */
    public static function disable($flag0_/*, $flag1_, ...*/)
    {
      foreach(func_get_args() as $arg)
        self::$m_flags[$arg]=null;

      self::notifyListeners();
    }

    /**
     * @param \Components\Debug_Appender $appender_
     *
     * @return \Components\Debug_Appender
     */
    public static function appender($verbosity_=self::INFO, Debug_Appender $appender_=null)
    {
      if(null!==$appender_)
        self::$m_appender[$verbosity_]=$appender_;

      if(self::$m_active && self::$m_verbosity>=$verbosity_)
      {
        while(self::INFO>=$verbosity_)
        {
          if(isset(self::$m_appender[$verbosity_]))
            return self::$m_appender[$verbosity_];

          $verbosity_*=2;
        }
      }

      if(null===self::$m_appenderNull)
        self::$m_appenderNull=new Debug_Appender_Null();

      return self::$m_appenderNull;
    }

    /**
     * @param string $type_
     *
     * @return \Components\Debug_Appender
     */
    public static function appenderForType(Type $type_)
    {
      foreach(self::$m_appender as $appender)
      {
        if($type_->isTypeOf($appender))
          return $appender;
      }

      return null;
    }

    /**
     * @param integer $verbosity_
     *
     * @return integer
     */
    public static function verbosity($verbosity_=null)
    {
      if(null!==$verbosity_)
        self::$m_verbosity=$verbosity_;

      return self::$m_verbosity;
    }

    /**
     * @param \Closure $listener_
     */
    public static function addFlagListener(\Closure $listener_)
    {
      self::$m_listeners[]=$listener_;

      // Initialize ...
      $listener_(self::$m_active, self::$m_flags);
    }

    /**
     * @param mixed... $args_
     */
    public static function info($arg0_/*, $arg1_... */)
    {
      static::appender(self::INFO)->append(self::INFO, func_get_args());
    }

    /**
     * @param mixed[] $args_
     */
    public static function vinfo(array $args_)
    {
      static::appender(self::INFO)->append(self::INFO, $args_);
    }

    /**
     * @param mixed... $args_
     */
    public static function warn($arg0_/*, $arg1_... */)
    {
      static::appender(self::WARN)->append(self::WARN, func_get_args());
    }

    /**
     * @param mixed[] $args_
     */
    public static function vwarn(array $args_)
    {
      static::appender(self::WARN)->append(self::WARN, $args_);
    }

    /**
     * @param mixed... $args_
     */
    public static function error($arg0_/*, $arg1_... */)
    {
      static::appender(self::ERROR)->append(self::ERROR, func_get_args());
    }

    /**
     * @param mixed[] $args_
     */
    public static function verror(array $args_)
    {
      static::appender(self::ERROR)->append(self::ERROR, $args_);
    }

    public static function flush()
    {
      foreach(self::$m_appender as $appender)
        $appender->flush();
    }

    public static function clear()
    {
      foreach(self::$m_appender as $appender)
        $appender->clear();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var boolean
     */
    private static $m_active=false;
    /**
     * @var integer
     */
    private static $m_verbosity=self::INFO;
    /**
     * @var \Closure[]
     */
    private static $m_listeners=[];
    private static $m_flags=[];
    /**
     * @var \Components\Debug_Appender[]
     */
    private static $m_appender=[];
    /**
     * @var \Components\Debug_Appender
     */
    private static $m_appenderNull;
    //-----


    // HELPERS
    private static function notifyListeners()
    {
      foreach(self::$m_listeners as $listener)
        $listener(self::$m_active, self::$m_flags);
    }
    //--------------------------------------------------------------------------
  }
?>
