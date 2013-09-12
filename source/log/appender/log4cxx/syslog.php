<?php


namespace Components;


  /**
   * Log_Appender_Log4cxx_File
   *
   * @api
   * @package net.evalcode.components.log
   * @subpackage appender.log4cxx
   *
   * @author evalcode.net
   */
  class Log_Appender_Log4cxx_Syslog extends Log_Appender_Log4cxx
  {
    // OVERRIDES
    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
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
