<?php


namespace Components;


  /**
   * Log_Appender_Chain
   *
   * @api
   * @package net.evalcode.components.log
   * @subpackage appender
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
    public static function of($name_, $level_, Log_Appender $appender0_/*, Log_Appender $appender1_.. */)
    {
      $args=func_get_args();

      $instance=new static(array_shift($args), array_shift($args));
      $instance->m_appenders=$args;

      return $instance;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS/MUTATORS
    /**
     * @param Log_Appender $appender_
     *
     * @return \Components\Log_Appender_Chain
     */
    public function add(Log_Appender $appender_)
    {
      $this->m_appenders[]=$appender_;

      return $this;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Log_Appender::append() append
     */
    public function append($level_, array $args_=[])
    {
      foreach($this->m_appenders as $appender)
        $appender->append($level_, $args_);
    }

    /**
     * @see \Components\Log_Appender_Abstract::initialize() initialize
     */
    public function initialize()
    {
      foreach($this->m_appenders as $appender)
        $appender->initialize();
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var \Components\Log_Appender[]
     */
    private $m_appenders=[];
    //--------------------------------------------------------------------------
  }
?>
