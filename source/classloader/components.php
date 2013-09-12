<?php


namespace Components;


  /**
   * Classloader_Components
   *
   * <p>
   *   Default classloader for components runtime.
   * </p>
   *
   * @api
   * @package net.evalcode.components.classloader
   *
   * @author evalcode.net
   */
  class Classloader_Components extends Classloader_Abstract
  {
    // CONSTRUCTION
    public function __construct($path_, $patternInclude_=Classloader_Abstract::PATTERN_INCLUDE_DEFAULT)
    {
      parent::__construct(null, $path_, $patternInclude_);
    }
    //--------------------------------------------------------------------------


    // OVERRIDES
    /**
     * @see \Components\Classloader::initialize() \Components\Classloader::initialize()
     */
    public function initialize()
    {
      $iterator=new \RegexIterator(new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator($this->m_path)),
        '/.manifest$/',
        \RecursiveRegexIterator::MATCH
      );

      foreach($iterator as $entry)
      {
        try
        {
          $manifest=Manifest::forComponent(basename($entry->getPath()));
        }
        catch(Runtime_Exception $e)
        {
          continue;
        }

        $this->addClasspath(
          $manifest->getNamespace(Manifest::SOURCE_TYPE_MAIN),
          $manifest->getClasspath(Manifest::SOURCE_TYPE_MAIN),
          $this->m_patternInclude
        );
      }
    }
    //--------------------------------------------------------------------------
  }
?>
