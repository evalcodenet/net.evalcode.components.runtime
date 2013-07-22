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
    // OVERRIDES/IMPLEMENTS
    /**
     * (non-PHPdoc)
     * @see \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{initialized: %s}',
        __CLASS__,
        $this->hashCode(),
        Boolean::valueOf($this->m_initialized)
      );
    }
    //--------------------------------------------------------------------------
  }
?>
