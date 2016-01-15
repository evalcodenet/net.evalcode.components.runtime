<?php


namespace Components;


  /**
   * Log_Appender_Null
   *
   * @api
   * @package net.evalcode.components.log
   * @subpackage appender
   *
   * @author evalcode.net
   */
  class Log_Appender_Null extends Log_Appender_Abstract
  {
    // CONSTRUCTION
    public function __construct($name_='null', $level_=null)
    {
      parent::__construct($name_, $level_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Log_Appender::append() append
     */
    public function append($level_, array $args_=[])
    {
      // Do nothing ...
    }
    //--------------------------------------------------------------------------
  }
?>
