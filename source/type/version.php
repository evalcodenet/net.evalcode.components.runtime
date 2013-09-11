<?php


namespace Components;


  /**
   * Version
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   */
  class Version implements Object, Comparable, Cloneable
  {
    // CONSTRUCTION
    public function __construct($major_, $minor_, $revision_)
    {
      $this->m_major=$major_;
      $this->m_minor=$minor_;
      $this->m_revision=$revision_;
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * Retrieves and returns current project version.
     *
     * @return Version
     */
    public static function current()
    {
      // TODO Implement
      return new static(0, 0, 0);
    }

    /**
     * Creates an instance for given version string.
     *
     * @return Version
     */
    public static function parse($versionString_)
    {
      // XXX Examplary Implementation
      $chunks=explode('.', $versionString_);

      $major=isset($chunks[0])?(int)$chunks[0]:0;
      $minor=isset($chunks[1])?(int)$chunks[1]:0;
      $revision=isset($chunks[2])?(int)$chunks[2]:0;

      return new static($major, $minor, $revision);
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    public function getMajor()
    {
      return $this->m_major;
    }

    public function getMinor()
    {
      return $this->m_minor;
    }

    public function getRevision()
    {
      return $this->m_revision;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**     * @see Components\Comparable::compareTo() Components\Comparable::compareTo()
     */
    public function compareTo($object_)
    {
      if($object_ instanceof self)
      {
        if($this->m_major===$object_->m_major)
        {
          if($this->m_minor===$object_->m_minor)
          {
            if($this->m_revision===$object_->m_revision)
              return 0;

            if($this->m_revision<$object_->m_revision)
              return -1;

            return 1;
          }

          if($this->m_minor<$object_->m_minor)
            return -1;

          return 1;
        }

        if($this->m_major<$object_->m_major)
          return -1;

        return 1;
      }

      throw new Runtime_Exception('runtime/version', 'Can not compare to instance of unknown type.');
    }

    /**     * @see Components\Cloneable::__clone() Components\Cloneable::__clone()
     */
    public function __clone()
    {
      return new static($this->m_major, $this->m_minor, $this->m_revision);
    }

    /**     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return integer_hash_m($this->m_major, $this->m_minor, $this->m_revision);
    }

    /**     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
      {
        return $this->m_major===$object_->m_major
          && $this->m_minor===$object_->m_minor
          && $this->m_revision===$object_->m_revision;
      }

      return false;
    }

    /**     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%1$d.%2$d.%3$d',
        $this->getMajor(),
        $this->getMinor(),
        $this->getRevision()
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_major;
    private $m_minor;
    private $m_revision;
    //--------------------------------------------------------------------------
  }
?>
