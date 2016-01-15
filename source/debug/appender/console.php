<?php


namespace Components;


  /**
   * Debug_Appender_Console
   *
   * @package net.evalcode.components.debug
   * @subpackage appender
   *
   * @author evalcode.net
   */
  class Debug_Appender_Console extends Debug_Appender_Abstract
  {
    // PREDEFINED PROPERTIES
    public static $appendDebugHeaders=true;
    //--------------------------------------------------------------------------


    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Debug_Appender::append() append
     */
    public function append($severity_, array $args_,
      $sourceFile_=null, $sourceLine_=null, $style_=Debug::STYLE_PLAIN)
    {
      $args=[];

      foreach($args_ as $arg)
      {
        if($arg instanceof \Exception)
          $this->m_dumps[$this->m_groupIdx][]=array_merge([$severity_, $style_], exception_as_array($arg, true, false));
        else
          $args[]=$this->dehydrate($arg);
      }

      if(count($args))
      {
        if(self::$appendDebugHeaders)
        {
          header('Components-Debug-'.self::$m_headerIdx++.':'.json_encode(
            [$severity_, $style_, $sourceFile_, $sourceLine_, $args]
          ));
        }

        $this->m_dumps[$this->m_groupIdx][]=[$severity_, $style_, $sourceFile_, $sourceLine_, $args];
      }
    }

    /**
     * @see \Components\Debug_Appender::groupBegin() groupBegin
     */
    public function groupBegin($severity_, $message_,
      $sourceFile_=null, $sourceLine_=null, $style_=Debug::STYLE_PLAIN)
    {
      $this->m_groups[++$this->m_groupIdx]=[
        $severity_, $style_, $message_, $sourceFile_, $sourceLine_
      ];
    }

    /**
     * @see \Components\Debug_Appender::groupEnd() groupEnd
     */
    public function groupEnd()
    {
      $this->m_groupIdx--;
    }

    /**
     * @see \Components\Debug_Appender::flush() flush
     */
    public function flush()
    {
      if(count($this->m_dumps))
      {
        $json=json_encode([$this->m_dumps, $this->m_groups]);

        $this->clear();

        if(false===self::$appendDebugHeaders)
        {
          $jquery=Environment::uriComponentsResource('ui/js/jquery/jquery-1.11.2.min.js');
          $libstd=Environment::uriComponentsResourceLibstd();

          $verbosity=Debug::verbosity();

          echo <<<SCRIPT
          <script type="text/javascript">
            var __lcl=false;
            var __lcd=function()
            {
              if("undefined"==typeof(libstd_components))
              {
                if(__lcl) return setTimeout(__lcd, 10);
                if("undefined"!=typeof(parent.libstd_components)) libstd_components=parent.libstd_components;
                else {
                  __lcl=true; document.writeln('<meta name="libstd.debug" content="$verbosity"/>');
                  if("undefined"==typeof(jQuery)) document.writeln('<script type="text/javascript" src="$jquery"><\/script>');
                  document.writeln('<script type="text/javascript" src="$libstd"><\/script>');
                  return setTimeout(__lcd, 10);
                }
              }

              libstd_components.dump($json);
            };
            __lcd();
          </script>
SCRIPT;
        }
      }
    }

    /**
     * @see \Components\Debug_Appender::clear() clear
     */
    public function clear()
    {
      $this->m_dumps=[];
      $this->m_groups=[];
      $this->m_groupIdx=0;
    }

    /**
     * @see \Components\Object::equals() equals
     */
    public function equals($object_)
    {
      if(null===$object_)
        return false;

      if($object_ instanceof self)
        return $this->hashCode()===$object_->hashCode();

      return false;
    }

    /**
     * @see \Components\Object::hashCode() hashCode
     */
    public function hashCode()
    {
      return \math\hasho($this);
    }

    /**
     * @see \Components\Object::__toString() __toString
     */
    public function __toString()
    {
      return sprintf('%s@%s', __CLASS__, $this->hashCode());
    }
    //--------------------------------------------------------------------------


    // IMPLEMENTATION
    /**
     * @var integer
     */
    private static $m_headerIdx=0;

    /**
     * @var scalar[]
     */
    private $m_dumps=[];
    /**
     * @var scalar[]
     */
    private $m_groups=[];
    /**
     * @var integer
     */
    private $m_groupIdx=0;
    //--------------------------------------------------------------------------
  }
?>
