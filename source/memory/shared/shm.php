<?php


namespace Components;


  /**
   * Memory_Shared_Shm
   *
   * @api
   * @package net.evalcode.components.memory
   * @subpackage shared
   *
   * @author evalcode.net
   */
  class Memory_Shared_Shm implements Object, Serializable_Php
  {
    // CONSTRUCTION
    public function __construct($segmentId_)
    {
      $this->m_segmentId=$segmentId_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param integer $segmentId_
     *
     * @return \Components\Memory_Shared_Shm
     *
     * @throws \Components\Runtime_Exception
     */
    public static function forSegment($segmentId_)
    {
      if(false===static::isSupported())
      {
        throw new Runtime_Exception('memory/shared/shm',
          'Shared memory with \'shm\' is not supported. Compile PHP with \'--enable-sysvshm\'.'
        );
      }

      if(false===isset(self::$m_segments[$segmentId_]))
        self::$m_segments[$segmentId_]=new static($segmentId_);

      return self::$m_segments[$segmentId_];
    }

    /**
     * @param integer $segmentId_
     */
    public static function removeSegment($segmentId_)
    {
      if(isset(self::$m_segments[$segmentId_]))
        unset(self::$m_segments[$segmentId_]);
    }

    /**
     * @return boolean
     */
    public static function isSupported()
    {
      if(null===self::$m_isSupported)
        self::$m_isSupported=function_exists('shm_attach');

      return self::$m_isSupported;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @return integer
     */
    public function getSegmentId()
    {
      return $this->m_segmentId;
    }

    /**
     * @param integer $key_
     *
     * @return mixed
     */
    public function get($key_)
    {
      if(false===shm_has_var($this->m_segment, $key_))
        return null;

      return shm_get_var($this->m_segment, $key_);
    }

    /**
     * @param integer $key_
     * @param mixed $value_
     *
     * @throws \Components\Runtime_Exception
     */
    public function set($key_, $value_)
    {
      if(false===shm_put_var($this->m_segment, $key_, $value_))
      {
        throw new Runtime_Exception('memory/shared/shm', sprintf(
          'Unable to store to shared memory segment [%1$s]. '.
          'Try to increase \'sysvshm.init_mem\'.',
            $this->m_segmentId
        ));
      }
    }

    /**
     * @param integer $key_
     */
    public function exists($key_)
    {
      return shm_has_var($this->m_segment, $key_);
    }

    /**
     * @param integer $key_
     *
     * @return boolean
     */
    public function remove($key_)
    {
      if(shm_has_var($this->m_segment, $key_))
        return shm_remove_var($this->m_segment, $key_);

      return false;
    }

    public function attach()
    {
      if(null===$this->m_segment)
      {
        $this->m_segment=shm_attach($this->m_segmentId);

        if(false===$this->m_segment)
        {
          $this->m_segment=null;

          throw new Runtime_Exception('memory/shared/shm', sprintf(
            'Unable to attach shared memory segment [%1$s].', $this->m_segmentId
          ));
        }
      }

      return $this->m_segment;
    }

    public function detach()
    {
      if(null!==$this->m_segment)
      {
        shm_detach($this->m_segment);

        $this->m_segment=null;
      }
    }

    public function clear()
    {
      shm_remove($this->m_segment);

      $this->detach();
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    public function __isset($key_)
    {
      return $this->exists($key_);
    }

    public function __unset($key_)
    {
      return $this->remove($key_);
    }

    public function __get($key_)
    {
      return $this->get($key_);
    }

    public function __set($key_, $value_)
    {
      return $this->set($key_, $value_);
    }

    /**
     * @see \Components\Object::equals() \Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_segmentId===$object_->m_segmentId;

      return false;
    }

    /**
     * @see \Components\Object::hashCode() \Components\Object::hashCode()
     */
    public function hashCode()
    {
      return integer_hash($this->m_segmentId);
    }

    /**
     * @see \Components\Object::__toString() \Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%1$s#%2$s{segmentId: %3$d}',
        __CLASS__,
        $this->hashCode(),
        $this->m_segmentId
      );
    }

    /**
     * @see \Components\Serializable_Php::__sleep() \Components\Serializable_Php::__sleep()
     */
    public function __sleep()
    {
      $this->detach();

      return array('m_segmentId');
    }

    /**
     * @see \Components\Serializable_Php::__wakeup() \Components\Serializable_Php::__wakeup()
     */
    public function __wakeup()
    {
      // Segment will be attached lazily on first concrete access.
    }

    /**
     * @see \Components\Serializable::serialVersionUid() \Components\Serializable::serialVersionUid()
     */
    public function serialVersionUid()
    {
      return 1;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_segments=array();
    private static $m_isSupported;

    private $m_segment;
    private $m_segmentId;
    //-----


    // DESTRUCTION
    public function __destruct()
    {
      $this->detach();
    }
    //--------------------------------------------------------------------------
  }
?>
