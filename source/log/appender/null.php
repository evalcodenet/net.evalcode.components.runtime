<?php


namespace Components;


  /**
   * Log_Appender_Null
   *
   * @package net.evalcode.components
   * @subpackage log.appender
   *
   * @author evalcode.net
   */
  class Log_Appender_Null extends Log_Appender_Abstract
  {
    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::append()
     */
    public function append($level_, array $args_=array())
    {
      // Do nothing ...
    }
    //--------------------------------------------------------------------------
  }
?>
