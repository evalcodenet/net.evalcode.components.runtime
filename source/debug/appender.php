<?php


namespace Components;


  /**
   * Debug_Appender
   *
   * @api
   * @package net.evalcode.components.debug
   *
   * @author evalcode.net
   */
  interface Debug_Appender extends Object
  {
    // ACCESSORS/MUTATORS
    /**
     * @param integer $severity_
     * @param mixed[] $args_
     * @param string $sourceFile_
     * @param integer $sourceLine_
     * @param integer $style_
     *
     * @return void
     */
    function append($severity_, array $args_,
      $sourceFile_=null, $sourceLine_=null, $style_=Debug::STYLE_PLAIN);

    /**
     * @param integer $severity_
     * @param string $message_
     * @param string $sourceFile_
     * @param integer $sourceLine_
     * @param integer $style_
     *
     * @return void
     */
    function groupBegin($severity_, $message_,
      $sourceFile_=null, $sourceLine_=null, $style_=Debug::STYLE_PLAIN);

    /**
     * @return void
     */
    function groupEnd();

    function clear();
    function flush();
    //--------------------------------------------------------------------------
  }
?>
