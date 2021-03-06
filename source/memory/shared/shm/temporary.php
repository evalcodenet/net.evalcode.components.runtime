<?php


namespace Components;


  /**
   * Memory_Shared_Shm_Temporary
   *
   * @package net.evalcode.components.memory
   * @subpackage shared.shm
   *
   * @author evalcode.net
   */
  class Memory_Shared_Shm_Temporary extends Memory_Shared_Shm
  {
    // CONSTRUCTION
    public function __construct($segmentId_, $file_)
    {
      parent::__construct($segmentId_);

      $this->m_file=$file_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $tmpFile_
     *
     * @return \Components\Memory_Shared_Shm_Temporary
     */
    public static function create($file_=null)
    {
      if(false===static::isSupported())
      {
        throw new Exception_NotSupported('memory/shared/shm',
          'Shared memory with \'shm\' is not supported. Compile PHP with \'--enable-sysvshm\'.'
        );
      }

      // FIXME Resolve dependency to i/o component.
      if(null===$file_)
        $file_=Io::tmpFileName('shm');

      return new static(ftok($file_, 'a'), $file_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_file===$object_->m_file;

      return false;
    }

    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return \math\hashs($this->m_file);
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%1$s#%2$s{file: %4$s, attached: %5$s, index: %5$s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_segmentId,
        $this->m_file,
        true===$this->m_attached?'true':'false',
        $this->m_index
      );
    }

    /**
     * @see \Components\Cloneable::__clone() \Components\Cloneable::__clone()
     */
    public function __clone()
    {
      throw new Exception_NotSupported('memory/shared/shm',
        'Cloning a temporary shared memory segments is not supported.'
      );
    }

    /**
     * @see \Components\Serializable_Php::serialize() \Components\Serializable_Php::serialize()
     */
    public function serialize()
    {
      throw new Exception_NotSupported('memory/shared/shm',
        'Serializing a temporary shared memory segment is not supported.'
      );
    }

    /**
     * @see \Components\Serializable_Php::unserialize() \Components\Serializable_Php::unserialize()
     */
    public function unserialize($segmentId_)
    {
      throw new Exception_NotSupported('memory/shared/shm',
        'Serializing a temporary shared memory segment is not supported.'
      );
    }

    /**
     * @see \Components\Serializable::serialVersionUid() \Components\Serializable::serialVersionUid()
     */
    public function serialVersionUid()
    {
      return 1;
    }

    public function __sleep()
    {
      throw new Exception_NotSupported('memory/shared/shm',
        'Serializing a temporary shared memory segment is not supported.'
      );
    }

    public function __wakeup()
    {
      throw new Exception_NotSupported('memory/shared/shm',
        'Serializing a temporary shared memory segment is not supported.'
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var string
     */
    private $m_file;
    //-----


    // DESTRUCTION
    public function __destruct()
    {
      parent::__destruct();

      if(null!==$this->m_file && is_file($this->m_file))
        @unlink($this->m_file);
    }
    //--------------------------------------------------------------------------
  }
?>
