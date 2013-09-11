<?php


namespace Components;


  /**
   * Cache_Backend_Local
   *
   * @package net.evalcode.components
   * @subpackage runtime.cache.backend
   *
   * @author evalcode.net
   */
  class Cache_Backend_Local implements Cache_Backend
  {
    // CONSTRUCTION
    public function __construct(array &$cache_=array())
    {
      $this->m_cache=&$cache_;
    }
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**     * @see Components\Cache_Backend::exists() Components\Cache_Backend::exists()
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

    /**     * @see Components\Cache_Backend::get() Components\Cache_Backend::get()
     */
    public function get($key_)
    {
      if($this->exists($key_))
        return $this->m_cache[$key_]['value'];

      return false;
    }

    /**     * @see Components\Cache_Backend::set() Components\Cache_Backend::set()
     */
    public function set($key_, $value_, $ttl_=0)
    {
      $this->m_cache[$key_]=array('value'=>$value_, 'time'=>time(), 'ttl'=>$ttl_);

      return true;
    }

    /**     * @see Components\Cache_Backend::remove() Components\Cache_Backend::remove()
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

    /**     * @see Components\Cache_Backend::dump() Components\Cache_Backend::dump()
     */
    public function dump($filename_)
    {
      if(false===file_put_contents($filename_, serialize($this->m_cache)))
        return false;

      return true;
    }

    /**     * @see Components\Cache_Backend::load() Components\Cache_Backend::load()
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

    /**     * @see Components\Cache_Backend::clear() Components\Cache_Backend::clear()
     */
    public function clear($prefix_=null)
    {
      if(null===$prefix_)
      {
        $this->m_cache=array();
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
     * @var array|mixed
     */
    private $m_cache=array();
    //--------------------------------------------------------------------------
  }
?>
