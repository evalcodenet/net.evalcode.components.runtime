<?php


namespace Components;


  /**
   * Cache_Backend_Local
   *
   * @api
   * @package net.evalcode.components.cache
   * @subpackage backend
   *
   * @author evalcode.net
   */
  class Cache_Backend_Local implements Cache_Backend
  {
    // CONSTRUCTION
    public function __construct(array &$cache_=[])
    {
      $this->m_cache=&$cache_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * @see \Components\Cache_Backend::exists() exists
     */
    public function exists($key_)
    {
      if(false===array_key_exists($key_, $this->m_cache))
        return false;

      if(0<$this->m_cache[$key_]['ttl'] && time()>$this->m_cache[$key_]['time']+$this->m_cache[$key_]['ttl'])
      {
        $this->remove($key_);

        return false;
      }

      return true;
    }

    /**
     * @see \Components\Cache_Backend::get() get
     */
    public function get($key_)
    {
      if($this->exists($key_))
        return $this->m_cache[$key_]['value'];

      return false;
    }

    /**
     * @see \Components\Cache_Backend::set() set
     */
    public function set($key_, $value_, $ttl_=86400)
    {
      $this->m_cache[$key_]=['value'=>$value_, 'time'=>time(), 'ttl'=>$ttl_];

      return true;
    }

    /**
     * @see \Components\Cache_Backend::remove() remove
     */
    public function remove($key_)
    {
      if(array_key_exists($key_, $this->m_cache))
      {
        unset($this->m_cache[$key_]);

        return true;
      }

      return false;
    }

    /**
     * @see \Components\Cache_Backend::dump() dump
     */
    public function dump($filename_)
    {
      if(false===file_put_contents($filename_, serialize($this->m_cache)))
        return false;

      return true;
    }

    /**
     * @see \Components\Cache_Backend::load() load
     */
    public function load($filename_)
    {
      if(is_array($data=@unserialize(file_get_contents($filename_))))
      {
        $this->m_cache=$data;

        return true;
      }

      return false;
    }

    /**
     * @see \Components\Cache_Backend::clear() clear
     */
    public function clear($prefix_=null)
    {
      if(null===$prefix_)
      {
        $this->m_cache=[];
      }
      else
      {
        foreach($this->m_cache as $key=>$value)
        {
          if(0===strpos($key, $prefix_))
            unset($this->m_cache[$key_]);
        }
      }
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var array
     */
    private $m_cache=[];
    //--------------------------------------------------------------------------
  }
?>
