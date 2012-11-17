<?php


  /**
   * Tmo_Magento_ClassLoader
   *
   * @package net.evalcode.components
   * @subpackage magento
   *
   * @author evalcode.net
   */
  class Tmo_Magento_ClassLoader implements Tmo_ClassLoader
  {
    // PREDEFINED PROPERTIES
    const PATTERN_INCLUDE_DEFAULT='/.php$/';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    private function __construct(array $paths_, $patternInclude_)
    {
      $this->m_paths=$paths_;
      $this->m_patternInclude=$patternInclude_;
      $this->m_namespace='tmo-magento-classloader-'.md5(implode('', $paths_));

      $this->m_hashCode=spl_object_hash($this);
    }
    //--------------------------------------------------------------------------


    // STATIC ACCESSORS
    /**
     * @param string $path_
     * @param string $patternInclude_
     *
     * @return Tmo_Magento_ClassLoader
     */
    public static function register(array $paths_, $patternInclude_=self::PATTERN_INCLUDE_DEFAULT)
    {
      $instance=new self($paths_, $patternInclude_);
      self::$m_instances[$instance->hashCode()]=$instance;

      spl_autoload_register(array($instance, 'load'));

      return $instance;
    }

    /**
     * @param string $instanceId_
     */
    public static function unregister(Tmo_Magento_ClassLoader $classloader_)
    {
      if(isset(self::$m_instances[$classloader_->hashCode()]))
      {
        spl_autoload_unregister(array($classloader_, 'load'));

        self::$m_instances[$classloader_->hashCode()]=null;
      }
    }
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    public function load($clazz_)
    {
      if(false===isset($this->m_classpaths[$clazz_]))
        @file_put_contents('/var/www/dev/com.mo-commerce/var/log/system.log', "Missing Type: $type [{$file->getBasename()}, {$this->m_classpaths[$type]}, {$filePath}]\r\n", FILE_APPEND);

      return @require_once $this->m_classpaths[$clazz_];
    }

    public function initialize($force_=false)
    {
      if($this->m_initialized && false===$force_)
        return;

      $apc=@ini_get('apc.enabled');

      if(false===$force_)
      {
        if($apc && is_array($this->m_classpaths=apc_fetch($this->m_namespace)))
          return $this->m_initialized=true;
        else if(false===$apc
          && @is_file($cacheFile=sys_get_temp_dir()."/{$this->m_namespace}.json")
          && is_array($this->m_classpaths=@json_decode(file_get_contents(sys_get_temp_dir()."/{$this->m_namespace}.json"))))
          return $this->m_initialized=true;
      }

      $this->m_classpaths=array();
      foreach($this->m_paths as $path)
      {
        $iterator=new \RegexIterator(new \RecursiveIteratorIterator(
          new \RecursiveDirectoryIterator($path)), $this->m_patternInclude, \RecursiveRegexIterator::MATCH
        );

        foreach($iterator as $file)
        {
          foreach($this->collectTypes($filePath=$file->getRealpath()) as $type)
          {
            if(isset($this->m_classpaths[$type]))
            {
              if(false!==strpos($file->getBasename(), '.', strpos($file->getBasename(), '.')))
                @file_put_contents('/var/www/dev/com.mo-commerce/var/log/system.log', "$type [{$file->getBasename()}, {$this->m_classpaths[$type]}, {$filePath}]\r\n", FILE_APPEND);
            }

            $this->m_classpaths[$type]=$filePath;
          }
        }
      }

      if($apc)
        apc_store($this->m_namespace, $this->m_classpaths, 0);
      else
        @file_put_contents($cacheFile, json_encode($this->m_classpaths));

      $this->m_initialized=true;
    }

    public function hashCode()
    {
      return $this->m_hashCode;
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private static $m_instances=array();

    private $m_classpaths=array();
    private $m_paths=array();
    private $m_initialized=false;
    private $m_hashCode;
    private $m_namespace;
    private $m_patternInclude;
    //-----


    private function collectTypes($path_)
    {
      $source=@file_get_contents($path_);

      if(false===$source)
        return array();

      $matches=array();
      preg_match_all('/\n\s*(?:(?:abstract|final)+\s+)*(?:class|interface|trait)\s*(\w+)\s/', $source, $matches);

      if(isset($matches[1]))
        return $matches[1];

      return array();
    }
    //--------------------------------------------------------------------------
  }
?>
