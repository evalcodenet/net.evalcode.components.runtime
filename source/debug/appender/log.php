<?php


namespace Components;


  /**
   * Debug_Appender_Log
   *
   * @package net.evalcode.components.debug
   * @subpackage appender
   *
   * @author evalcode.net
   */
  class Debug_Appender_Log extends Debug_Appender_Abstract
  {
    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Debug_Appender::append() append
     */
    public function append($severity_, array $args_)
    {
      Log::current()->append(self::$m_severity[$severity_],
        array('debug', print_r($this->dehydrate($args_), true))
      );
    }

    /**
     * @see \Components\Debug_Appender::appendGroup() appendGroup
     */
    public function appendGroup($severity_, $message_, array $lines_)
    {
      Log::current()->append(
        self::$m_severity[$severity_], array('debug', $message_)
      );

      foreach($lines_ as $severity=>$messages)
      {
        foreach($messages as $message)
        {
          Log::current()->append(
            self::$m_severity[$severity], array('debug', $message)
          );
        }
      }
    }

    /**
     * @see \Components\Debug_Appender::flush() flush
     */
    public function flush()
    {
      // Do nothing ...
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
      return object_hash($this);
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_severity=[
      Debug::INFO=>Log::INFO,
      Debug::WARN=>Log::WARN,
      Debug::ERROR=>Log::ERROR
    ];
    //--------------------------------------------------------------------------
  }
?>
