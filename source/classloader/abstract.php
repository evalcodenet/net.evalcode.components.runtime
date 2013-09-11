<?php


namespace Components;


  /**
   * Classloader_Abstract
   *
   * @package net.evalcode.components
   * @subpackage runtime
   *
   * @author evalcode.net
   */
  class Classloader_Abstract implements Classloader
  {
    // PREDEFINED PROPERTIES
    const PATTERN_INCLUDE_DEFAULT='/.php$/';
    //--------------------------------------------------------------------------


    // CONSTRUCTION
    public function __construct($namespace_, $path_, $patternInclude_=self::PATTERN_INCLUDE_DEFAULT)
    {
      $this->m_path=$path_;
      $this->m_patternInclude=$patternInclude_;
      $this->m_namespace=$namespace_;
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**     * @see Classloader::getClasspaths() Classloader::getClasspaths()
     */
    public function getClasspaths()
    {
      return $this->m_classpaths;
    }

    /**     * @see Classloader::loadClass() Classloader::loadClass()
     */
    public function loadClass($clazz_)
    {
      if(false===isset($this->m_classpaths[$clazz_]))
        return false;

      require_once $this->m_classpaths[$clazz_];

      return true;
    }

    /**     * @see Classloader::initialize() Classloader::initialize()
     */
    public function initialize()
    {
      $this->addClasspath($this->m_namespace, $this->m_path, $this->m_patternInclude);
    }

    /**     * @see Components\Object::hashCode() Components\Object::hashCode()
     */
    public function hashCode()
    {
      return object_hash($this);
    }

    /**     * @see Components\Object::equals() Components\Object::equals()
     */
    public function equals($object_)
    {
      if($object_ instanceof self)
        return $this->m_path===$object_->m_path;

      return false;
    }

    /**     * @see Components\Object::__toString() Components\Object::__toString()
     */
    public function __toString()
    {
      return sprintf('%s@%s{path: %s, initialized: %s}',
        __CLASS__,
        $this->hashCode(),
        $this->m_path,
        $this->m_initialized
      );
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    private $m_classpaths=array();
    private $m_namespace;

    protected $m_path;
    protected $m_patternInclude;
    //-----


    protected function addClasspath($namespace_, $sourcePath_, $patternInclude_)
    {
      $iterator=new \RegexIterator(new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($sourcePath_)),
        $patternInclude_,
        \RecursiveRegexIterator::MATCH
      );

      foreach($iterator as $entry)
      {
        foreach($this->parseTypeDefinitions($entry) as $type)
          $this->m_classpaths[$namespace_?$namespace_.'\\'.$type:$type]=$entry->getPathname();
      }
    }

    protected function parseTypeDefinitions($path_)
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
