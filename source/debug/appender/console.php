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
    // OVERRIDES/IMPLEMENTS
    /**
     * @see \Components\Debug_Appender::append() append
     */
    public function append($severity_, array $args_, $file_=null, $line_=null)
    {
      $args=array();

      foreach($args_ as $arg)
      {
        if($arg instanceof \Exception)
        {
          $exception=exception_as_array($arg, true, false);

          self::$m_debug[$severity_][]=[object_hash_md5($arg), $arg->getFile(), $arg->getLine(), $exception];
        }
        else
        {
          $args[]=$this->dehydrate($arg);
        }
      }

      if(count($args))
      {
        if(null===$file_ && null===$line_)
        {
          $source=debug_backtrace(false);
          $source=$source[2];

          $line_=0;
          $file_='unknown';
          if(isset($source['file']))
            $file_=$source['file'];
          if(isset($source['line']))
            $line_=$source['line'];
        }

        $header=[];
        foreach($args as $arg)
          $header[$severity_][]=[$file_, $line_, $args];

        if(count($header))
        {
          header('Components-Debug-'.self::$m_debugIdx.':'.json_encode($header));

          self::$m_debugIdx++;
        }

        self::$m_debug[$severity_][]=[$file_, $line_, $args];
      }
    }

    /**
     * @see \Components\Debug_Appender::appendGroup() appendGroup
     */
    public function appendGroup($severity_, $message_, array $lines_)
    {
      self::$m_groups[$severity_][]=[$message_, $lines_];
    }

    /**
     * @see \Components\Debug_Appender::flush() flush
     */
    public function flush()
    {
      if(count(self::$m_debug) || count(self::$m_groups))
      {
        $info=Debug::INFO;
        $warn=Debug::WARN;
        $error=Debug::ERROR;

        $json=json_encode(self::$m_debug);
        self::$m_debug=[];

        $groups=json_encode(self::$m_groups);
        self::$m_groups=[];

        $script=<<<SCRIPT
          <script type="text/javascript">
            var error=$error;
            var warn=$warn;
            var info=$info;

            var severities=[error, warn, info];
            var methods=["error", "warn", "debug"];
            var styles=["color:red", "color:#ffa500", "color:blue"];

            function printException(method_, style_, hash_, file_, line_, namespace_, message_, args_)
            {
              console.groupCollapsed("%c%s", style_, message_);
              console[method_]("[%s] %s\\n\\n%s:%s\\n\\n%s", namespace_, message_, file_, line_, args_);
              console.groupEnd();
            }

            function printDump(method_, style_, file_, line_, args_)
            {
              console.groupCollapsed("%c%s:%s", style_, file_, line_);

              for(var i=0; i<args_.length; i++)
                console[method_]("%O", args_[i]);

              console.groupEnd();
            }

            function printGroup(message_, group_, style_)
            {
              console.groupCollapsed("%c%s", style_, message_);

              for(var idx in severities)
              {
                if(group_[severities[idx]])
                {
                  var items=group_[severities[idx]];
                  var count=items.length;

                  for(var i=0; i<count; i++)
                    console[methods[idx]]("%O", items[i]);
                }
              }

              console.groupEnd();
            }

            function print(items_, method_, style_)
            {
              var count=items_.length;
              var i=0;

              for(var item in items_)
              {
                if(++i>count)
                  break;

                if(items_[item][3])
                  printException(method_, style_, items_[item][0], items_[item][1], items_[item][2], items_[item][3]["namespace"], items_[item][3]["message"], items_[item][3]["stack"]);
                else
                  printDump(method_, style_, items_[item][0], items_[item][1], items_[item][2]);
              }
            };

            var debug=$json;
            var groups=$groups;

            for(var idx in severities)
            {
              if(debug[severities[idx]] && 0<debug[severities[idx]].length)
                print(debug[severities[idx]], methods[idx], styles[idx]);

              if(groups[severities[idx]])
              {
                for(var i=0; i<groups[severities[idx]].length; i++)
                  printGroup(groups[severities[idx]][i][0], groups[severities[idx]][i][1], styles[idx]);
              }
            }
          </script>
SCRIPT;

        echo $script;
      }
    }

    /**
     * @see \Components\Debug_Appender::clear() clear
     */
    public function clear()
    {
      self::$m_debug=[];
      self::$m_exceptions=[];
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
      return object_hash($this);
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
    private static $m_debug=[];
    private static $m_groups=[];
    private static $m_debugIdx=0;
    private static $m_exceptions=[];
    //--------------------------------------------------------------------------
  }
?>
