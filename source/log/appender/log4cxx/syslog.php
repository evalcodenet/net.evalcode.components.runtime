<?php


namespace Components;


  /**
   * Log_Appender_Log4cxx_File
   *
   * @package net.evalcode.components
   * @subpackage log.appender.log4cxx
   *
   * @author evalcode.net
   */
  class Log_Appender_Log4cxx_Syslog extends Log_Appender_Log4cxx
  {
    // OVERRIDES
    public function __toString()
    {
      return sprintf('%1$s#%2$s{initialized: %3$s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_initialized?'true':'false'
      );
    }
    //--------------------------------------------------------------------------
  }
?>
