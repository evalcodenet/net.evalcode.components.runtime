<?php


namespace Components;


  /**
   * Debug_Appender_Vardump
   *
   * @package net.evalcode.components.debug
   * @subpackage appender
   *
   * @author evalcode.net
   */
  class Debug_Appender_Vardump extends Debug_Appender_Abstract
  {
    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Debug_Appender::append() append
     */
    public function append($severity_, array $args_,
      $style_=Debug::STYLE_PLAIN, $sourceFile_=null, $sourceLine_=null)
    {
      echo '<pre style="text-align:left;background:white;color:black;">';

      foreach($args_ as $arg)
      {
        if($arg instanceof \Exception)
          exception_print_html($arg, true, true);
        else
          print_r($arg);
      }

      echo '</pre>';
    }

    /**
     * @see \Components\Debug_Appender::groupBegin() groupBegin
     */
    public function groupBegin($severity_, $message_,
      $sourceFile_=null, $sourceLine_=null, $style_=Debug::STYLE_PLAIN)
    {
      // TODO Implement ...
    }

    /**
     * @see \Components\Debug_Appender::groupEnd() groupEnd
     */
    public function groupEnd()
    {
      // TODO Implement ...
    }

    /**
     * @see \Components\Debug_Appender::flush() flush
     */
    public function flush()
    {
      // TODO Implement ...
    }

    /**
     * @see \Components\Debug_Appender::clear() clear
     */
    public function clear()
    {
      // Do nothing ...
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if(null===$object_)
        return false;

      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------
  }
?>
