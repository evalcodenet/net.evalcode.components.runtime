<?php


namespace Components;


  /**
   * Exception_Flat
   *
   * @package net.evalcode.components
   * @subpackage type.exception
   *
   * @author evalcode.net
   */
  class Exception_Flat
  {
    // PROPERTIES
    /**
     * @var string Class name of represented exception.
     */
    public $type;

    /**
     * @var integer Code of represented exception.
     */
    public $code;
    /**
     * @var string Messge of represented exception.
     */
    public $message;
    /**
     * @var string Textual stack trace of represented exception.
     */
    public $traceAsString;
    /**
     * @var boolean Indicates if represented exception is of type Error.
     */
    public $isErrorException=false;

    /**
     * @var string Source file where represented exception has been thrown.
     */
    public $file;
    /**
     * @var integer Source line where represented exception has been thrown.
     */
    public $line;
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * Creates instance from given exception.
     *
     * @param \Exception $exception_
     *
     * @return \Components\Exception_Flat
     */
    public static function create(\Exception $exception_)
    {
      $exception=static::createEmpty();

      $exception->type=get_class($exception_);

      $exception->code=$exception_->getCode();
      $exception->message=$exception_->getMessage();
      $exception->traceAsString=$exception_->getTraceAsString();

      $exception->file=$exception_->getFile();
      $exception->line=$exception_->getLine();

      if($exception_ instanceof \ErrorException)
        $exception->isErrorException=true;

      return $exception;
    }

    /**
     * Creates empty instance.
     *
     * @return \Components\Exception_Flat
     */
    public static function createEmpty()
    {
      return new static();
    }
    //--------------------------------------------------------------------------
  }
?>
