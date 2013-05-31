<?php


namespace Components;


  /**
   * Log_Appender_File
   *
   * @package net.evalcode.components
   * @subpackage log.appender
   *
   * @author evalcode.net
   */
  class Log_Appender_File extends Log_Appender_Abstract
  {
    // CONSTRUCTION
    public function __construct($name_, $file_, $level_=Log_Appender_Abstract::DEFAULT_LEVEL,
      $pattern_=Log_Appender_Abstract::DEFAULT_PATTERN)
    {
      parent::__construct($name_, $level_, $pattern_);

      $this->m_file=$file_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * (non-PHPdoc)
     * @see Components\Log_Appender::append()
     */
    public function append($level_, array $args_=array())
    {
      @file_put_contents(
        $this->m_file,
        $this->format($level_, $args_),
        FILE_APPEND
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    protected $m_file;
    //--------------------------------------------------------------------------
  }
?>
