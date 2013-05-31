<?php


namespace Components;


  /**
   * Memory_Shared_Shmop
   *
   * @package net.evalcode.components
   * @subpackage memory.shared
   *
   * @author evalcode.net
   */
  class Memory_Shared_Shmop implements Object
  {
    // CONSTRUCTION
    public function __construct($id_)
    {
      $this->m_id=$id_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param integer $id_
     *
     * @return \Components\Memory_Shared_Shmop
     *
     * @throws \Components\Runtime_Exception
     */
    public static function forId($id__)
    {
      if(false===static::isSupported())
      {
        throw new Runtime_Exception('memory/shared/shmop',
          'Shared memory with \'shmop\' is not supported. Compile PHP with \'--enable-shmop\'.'
        );
      }

      if(false===isset(self::$m_segments[$id_]))
        self::$m_segments[$id_]=new static($id_);

      return self::$m_segments[$id_];
    }

    /**
     * @return boolean
     */
    public static function isSupported()
    {
      if(null===self::$m_isSupported)
        self::$m_isSupported=function_exists('shmop_open');

      return self::$m_isSupported;
    }
    //--------------------------------------------------------------------------



    // ACCESSORS
    public function getId()
    {
      return $this->m_id;
    }

    public function getSegmentId()
    {
      return $this->m_segmentId;
    }

    public function open()
    {
      if(false!==$this->m_segmentId)
        return $this->m_segmentId;

      $segmentId=shmop_open($this->m_id);

      if(false===$segmentId)
        return false;

      return $this->m_segmentId=$segmentId;
    }

    public function close()
    {
      if(false===$this->m_segmentId)
        return false;

      // Ensure correct object state for the case shmop_close fails in FATAL.
      $segmentId=$this->m_segmentId;
      $this->m_segmentId=false;

      shmop_close($segmentId);

      return true;
    }

    public function clear()
    {
      if(false===$this->m_segmentId)
      {
        throw new Runtime_Exception('memory/shared/shmop',
          'Can not clear a closed shared memory segment.'
        );
      }

      $this->m_offset=0;
      $this->m_capacity=0;

      return shmop_delete($this->m_segmentId);
    }

    public function read($start_, $count_)
    {
      if(false===$this->m_segmentId)
      {
        throw new Runtime_Exception('memory/shared/shmop',
          'Can not read from a closed shared memory segment.'
        );
      }

      if(null===$start_)
        $start_=$this->m_offset;
      else
        $start_=(int)$start_;

      if(null===$count_)
        $count_=$this->getCapacity()-$start_;
      else
        $count_=(int)$count_;

      if($this->getCapacity()<($start_+$count_))
      {
        throw new Runtime_Exception('memory/shared/shmop', sprintf(
          'Requested range of bytes exceeds current size of shared memory segment '.
          '[size: %1$d, requested-bytes: %2$d-%3$d].',
            $this->getCapacity(), $start_, $count_
        ));
      }

      return shmop_read($this->m_segmentId, $start_, $count_);
    }

    public function write($data_, $offset_=null)
    {
      if(false===$this->m_segmentId)
      {
        throw new Runtime_Exception('memory/shared/shmop',
          'Can not write to a closed shared memory segment.'
        );
      }

      if(null===$offset_)
        $offset_=$this->m_offset;

      $bytesWritten=shmop_write($this->m_segmentId, $data_, $offset_);

      if(false===$wrote)
        return false;

      $this->m_offset+=$bytesWritten;
      $this->m_capacity+=$bytesWritten;

      return $bytesWritten;
    }

    public function getCapacity()
    {
      if(null===$this->m_capacity)
      {
        if(false===$this->m_segmentId)
        {
          throw new Runtime_Exception('memory/shared/shmop',
            'Can not get size of a closed shared memory segment.'
          );
        }

        $size=shmop_size($this->m_segmentId);

        if(false===$size)
          return false;

        $this->m_capacity=$size;
      }

      return $this->m_capacity;
    }

    public function getOffset()
    {
      return $this->m_offset;
    }

    public function seekTo($offset_)
    {
      $this->m_offset=(int)$offset_;
    }

    public function seekToBeginning()
    {
      $this->m_offset=0;
    }

    public function seekToEnd()
    {
      $this->m_offset=$this->getCapacity();
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
        return $this->m_id===$object_->m_id;

      return false;
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::hashCode()
     */
    public function hashCode()
    {
      return integer_hash($this->m_id);
    }

    /**
     * (non-PHPdoc)
     * @see Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{id: %s, capacity: %d, offset: %d}',
        __CLASS__,
        $this->hashCode(),
        $this->m_id,
        $this->m_capacity,
        $this->m_offset
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_segments=array();
    private static $m_isSupported;

    private $m_segmentId=false;
    private $m_capacity=0;
    private $m_offset=0;
    private $m_id;
    //--------------------------------------------------------------------------
  }
?>
