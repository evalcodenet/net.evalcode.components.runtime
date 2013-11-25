<?php


namespace Components;


  /**
   * Debug_Appender_Abstract
   *
   * @api
   * @package net.evalcode.components.debug
   * @subpackage appender
   *
   * @author evalcode.net
   */
  abstract class Debug_Appender_Abstract implements Debug_Appender
  {
    // STATIC ACCESSORS
    /**
     * @return Components\Type
     */
    public static function type()
    {
      return Type::forName(get_called_class());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @param mixed $arg_
     */
    protected function dehydrate($arg_)
    {
      if(is_scalar($arg_))
        return $arg_;

      if(is_array($arg_))
      {
        foreach($arg_ as &$value)
          $value=$this->dehydrate($value);

        return $arg_;
      }

      if($arg_ instanceof \Exception)
        return exception_as_array($arg_, true, false);

      // TODO Dehydrate objects  ..
      return print_r($arg_, true);
    }
    //--------------------------------------------------------------------------
  }
?>
