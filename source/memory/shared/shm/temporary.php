<?php


namespace Components;


  /**
   * Memory_Shared_Shm_Temporary
   *
   * @package net.evalcode.components
   * @subpackage memory.shared.shm
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
        throw new Runtime_Exception('memory/shared/shm',
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
     * (non-PHPdoc)
     * @see Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_file===$object_->m_file;

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return string_hash($this->m_file);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
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
     * (non-PHPdoc)
     * @see Components\Cloneable::__clone()
     */
    public function __clone()
    {
      throw new Runtime_Exception('memory/shared/shm',
        'Cloning a temporary shared memory segments is not supported.'
      );
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable_Php::serialize()
     */
    public function serialize()
    {
      throw new Runtime_Exception('memory/shared/shm',
        'Serializing a temporary shared memory segment is not supported.'
      );
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable_Php::unserialize()
     */
    public function unserialize($segmentId_)
    {
      throw new Runtime_Exception('memory/shared/shm',
        'Serializing a temporary shared memory segment is not supported.'
      );
    }

    /**
     * (non-PHPdoc)
     * @see Components\Serializable::serialVersionUid()
     */
    public function serialVersionUid()
    {
      return 1;
    }

    public function __sleep()
    {
      throw new Runtime_Exception('memory/shared/shm',
        'Serializing a temporary shared memory segment is not supported.'
      );
    }

    public function __wakeup()
    {
      throw new Runtime_Exception('memory/shared/shm',
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
