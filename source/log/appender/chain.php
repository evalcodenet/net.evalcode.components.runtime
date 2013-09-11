<?php


namespace Components;


  /**
   * Log_Appender_Chain
   *
   * @package net.evalcode.components
   * @subpackage log.appender
   *
   * @author evalcode.net
   */
  class Log_Appender_Chain extends Log_Appender_Abstract
  {
    // STATIC ACCESSORS
    /**
     * Create chain of given appenders.
     *
     * @param \Components\Log_Appender... $appender0_
     *
     * @return \Components\Log_Appender_Chain
     */
    public static function of($name_, $level_=Log::INFO, Log_Appender $appender0_/*, Log_Appender $appender1_.. */)
    {
      $args=func_get_args();

      $instance=new static(array_shift($args), array_shift($args));
      $instance->m_appenders=$args;

      return $instance;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**     * @see Components\Log_Appender::append() Components\Log_Appender::append()
     */
    public function append($level_, array $args_=array())
    {
      foreach($this->m_appenders as $appender)
        $appender->append($level_, $args_);
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_appenders=array();
    //--------------------------------------------------------------------------
  }
?>
