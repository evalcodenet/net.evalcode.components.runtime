<?php


namespace Components;


  /**
   * Log_Appender_File
   *
   * @api
   * @package net.evalcode.components.log
   * @subpackage appender
   *
   * @author evalcode.net
   */
  class Log_Appender_File extends Log_Appender_Abstract
  {
    // CONSTRUCTION
    public function __construct($name_, $file_, $level_=Log_Appender_Abstract::DEFAULT_LEVEL)
    {
      parent::__construct($name_, $level_);

      $this->m_file=$file_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Log_Appender::append() \Components\Log_Appender::append()
     */
    public function append($level_, array $args_=[])
    {
      @file_put_contents(
        $this->m_file,
        $this->format($level_, $args_),
        FILE_APPEND
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string
     */
    protected $m_file;
    //--------------------------------------------------------------------------
  }
?>
