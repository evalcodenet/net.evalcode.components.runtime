<?php


namespace Components;


  /**
   * Log
   *
   * @api
   * @package net.evalcode.components.log
   *
   * @author evalcode.net
   */
  class Log
  {
    // PREDEFINED PROPERTIES
    const FATAL=1;
    const ERROR=2;
    const WARN=3;
    const INFO=4;
    const DEBUG=5;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * Current log appender.
     *
     * @return \Components\Log_Appender
     */
    public static function current()
    {
      return self::$m_current;
    }

    /**
     * Current log level.
     *
     * @return integer
     */
    public static function currentLevel()
    {
      return self::$m_currentLevel;
    }

    /**
     * @param integer $level_
     *
     * @return boolean
     */
    public static function isLevelActive($level_)
    {
      return $level_<=self::$m_currentLevel;
    }

    /**
     * Return named appender.
     *
     * @param string $name_
     *
     * @return \Components\Log_Appender
     *
     * @throws \Components\Exception_IllegalArgument If no instance found for given name.
     */
    public static function get($name_)
    {
      foreach(self::$m_stack as $instance)
      {
        if($name_===$instance->name)
          return $instance;
      }

      throw new Exception_IllegalArgument('components/log', sprintf(
        'No instance found for given name [name: %1$s].', $name_
      ));
    }

    /**
     * Push given appender onto the stack.
     *
     * @param \Components\Log_Appender
     *
     * @return \Components\Log_Appender
     */
    public static function push(Log_Appender $appender_)
    {
      $appender_->initialize();

      if(null!==self::$m_current)
        array_push(self::$m_stack, self::$m_current);

      self::$m_current=$appender_;
      self::$m_currentLevel=$appender_->level;
      self::$m_count++;

      return $appender_;
    }

    /**
     * Pop current appender off the stack.
     *
     * @return \Components\Log_Appender
     */
    public static function pop()
    {
      $current=self::$m_current;

      if(0<self::$m_count)
      {
        self::$m_current=array_pop(self::$m_stack);
        self::$m_currentLevel=self::$m_current->level;
        self::$m_count--;
      }

      return $current;
    }

    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    public static function debug($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      if(self::DEBUG<=self::$m_currentLevel)
        self::$m_current->append(self::DEBUG, func_get_args());
    }

    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    public static function info($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      if(self::INFO<=self::$m_currentLevel)
        self::$m_current->append(self::INFO, func_get_args());
    }

    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    public static function warn($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      if(self::WARN<=self::$m_currentLevel)
        self::$m_current->append(self::WARN, func_get_args());
    }

    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    public static function error($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      self::$m_current->append(self::ERROR, func_get_args());
    }

    /**
     * @param string $namespace_
     * @param string $message_
     * @param mixed.. $args..
     */
    public static function fatal($namespace_, $message_/*, $arg0_, $arg1_, ..*/)
    {
      self::$m_current->append(self::FATAL, func_get_args());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Log_Appender[]
     */
    private static $m_stack=[];
    /**
     * @var integer
     */
    private static $m_count=0;
    /**
     * @var \Components\Log_Appender
     */
    private static $m_current;
    /**
     * @var integer
     */
    private static $m_currentLevel;
    //--------------------------------------------------------------------------
  }
?>
