

  if("undefined"==typeof(std))
  {
    var libstd_declare=function()
    {
      /**
       * libstd
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      std=function(module_, callback_)
      {
        if(!std[module_])
        {
          std.info("std", "Instantiating libstd_"+module_);

          var type=window["libstd_"+module_];

          std[module_]=new type();
        }

        if(callback_)
          setTimeout(function() { callback_(std[module_]); }, 0);

        return std[module_];
      };


      // CONSTANTS
      std.DEBUG=function()
      {
        var tagDebug=jQuery("meta[name='libstd.debug']");

        if(1>tagDebug.length)
          return -1;

        return parseInt(tagDebug.attr("content"));
      }();


      // INTERNAL CONSTANTS
      std.__LOADER=null;
      std.__SCHEDULER=null;


      // STATIC ACCESSORS
      std.include=function(uri_, callback_)
      {
        if(null==std.__LOADER)
        {
          std.__LOADER=new std.Loader();

          std.schedule("std/loader", std.__LOADER);
        }

        std.__LOADER.add(uri_, callback_);
      };

      std.typeForName=function(name_)
      {
        var type=(window || this);

        var path=name_.split("/");
        path[path.length-1]=path[path.length-1].charAt(0).toUpperCase()+path[path.length-1].slice(1);

        for(var i=0; i<path.length; i++)
        {
          type=type[path[i]];

          if(!type)
            break;
        }

        return type;
      };

      std.run=function(runnable_)
      {
        if(!runnable_.run)
          runnable_=new std.Runnable(runnable_);

        setTimeout(function() {
          runnable_.run();
        }, 0);

        return runnable_;
      };

      std.timestamp=function()
      {
        return Date.now()/1000|0;
      };
      //------------------------------------------------------------------------


      //------------------------------------------------------------------------
      // MODULES
      //------------------------------------------------------------------------
      /**
       * libstd LOG
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      libstd_log=function()
      {

      };


      // PREDEFINED PROPERTIES
      libstd_log.COLOR_LOG="color:#000";
      libstd_log.COLOR_INFO="color:#006";
      libstd_log.COLOR_WARN="color:#ff6800";
      libstd_log.COLOR_ERROR="color:#600";
      libstd_log.COLOR_DUMP="color:#060";


      // STATIC ACCESSORS
      libstd_log.append=function(method_, color_, namespace_, message_, arg_)
      {
        if("undefined"==typeof(arg_))
        {
          console[method_]("%c[%s] %s", color_, namespace_, message_);
        }
        else
        {
          if(arg_.type && "std/exception"==arg_.type())
          {
            console[method_]("%c[%s] %s\n\n%s:%s\n\n%s",
              color_,
              namespace_?namespace_:arg_.namespace,
              message_?message_:arg_.message,
              arg_.file,
              arg_.line,
              arg_.stack
            );
          }
          else if(arg_.stack)
          {
            console[method_]("%c[%s] %s\n\n%s:%s\n\n%s",
              color_,
              namespace_?namespace_:arg_.name,
              message_?message_:arg_.message,
              "unknown",
              0,
              arg_.stack
            );
          }
          else
          {
            if(namespace_ || message_)
              console[method_]("%c[%s] %s%O", color_, namespace_, message_, arg_);
            else
              console[method_]("%c", color_, arg_);
          }
        }
      }

      libstd_log.appenda=function(method_, color_, namespace_, message_, args_, expand_)
      {
        libstd_log.groupBegin(color_, namespace_, message_, expand_);

        if("function"==typeof(args_))
        {
          args_();
        }
        else
        {
          jQuery.each(args_, function(key_, item_) {
            libstd_log.append(method_, color_, null, null, item_);
          });
        }

        libstd_log.groupEnd();
      }

      libstd_log.groupBegin=function(color_, namespace_, message_, expand_)
      {
        if(console.groupCollapsed && !expand_)
          console.groupCollapsed("%c[%s] %s", color_, namespace_, message_);
        else if(console.group)
          console.group("%c[%s] %s", color_, namespace_, message_);
      }

      libstd_log.groupEnd=function()
      {
        if(console.groupEnd)
          console.groupEnd();
      }


      libstd_log.error=function(namespace_, message_, arg_)
      {
        libstd_log.append("error", libstd_log.COLOR_ERROR, namespace_, message_, arg_);
      };

      libstd_log.errora=function(namespace_, message_, args_, color_)
      {
        if(!color_)
          color_=libstd_log.COLOR_ERROR;

        libstd_log.appenda("error", color_, namespace_, message_, args_);
      };

      libstd_log.warn=function(namespace_, message_, arg_)
      {
        libstd_log.append("warn", libstd_log.COLOR_WARN, namespace_, message_, arg_);
      };

      libstd_log.warna=function(namespace_, message_, args_, color_)
      {
        if(!color_)
          color_=libstd_log.COLOR_WARN;

        libstd_log.appenda("warn", color_, namespace_, message_, args_);
      };

      libstd_log.info=function(namespace_, message_, arg_)
      {
        libstd_log.append(console.debug?"debug":"log", libstd_log.COLOR_INFO, namespace_, message_, arg_);
      };

      libstd_log.infoa=function(namespace_, message_, args_, color_)
      {
        if(!color_)
          color_=libstd_log.COLOR_INFO;

        libstd_log.appenda(console.debug?"debug":"log", color_, namespace_, message_, args_);
      };

      libstd_log.dump=function(args_, message_)
      {
        if("object"!=typeof(args_))
          args_=[args_];

        if(!message_)
          message_="";

        libstd_log.appenda(console.debug?"debug":"log", libstd_log.COLOR_DUMP, "std/dump", message_, args_, true);
      };

      libstd_log.assert=function(namespace_, message_, assertion_)
      {
        console.assert(assertion_, "["+namespace_+"] "+message_);
      };

      libstd_log.log=function(namespace_, message_, arg_)
      {
        libstd_log.append("log", null, namespace_, message_, arg_);
      };

      libstd_log.loga=function(namespace_, message_, args_, color_)
      {
        if(!color_)
          color_=libstd_log.COLOR_LOG;

        libstd_log.appenda("log", color_, namespace_, message_, args_);
      };


      // ALIASES
      std.assert=function(namespace_, message_, assertion_) {};

      std.profile={
        begin: function() {},
        end: function() {}
      };

      std.log=function(namespace_, message_, arg_) {};
      std.loga=function(namespace_, message_, args_, color_) {};

      std.info=function(namespace_, message_, arg_) {};
      std.infoa=function(namespace_, message_, args_, color_) {};

      std.warn=function(namespace_, message_, arg_) {};
      std.warna=function(namespace_, message_, args_, color_) {};

      std.error=function(namespace_, message_, arg_) {};
      std.errora=function(namespace_, message_, args_, color_) {};

      std.dump=function(args_) {};


      if("undefined"!=typeof(console))
      {
        if(0<std.DEBUG)
        {
          std.error=libstd_log.error;
          std.errora=libstd_log.errora;
        }

        if(1<std.DEBUG)
        {
          std.warn=libstd_log.warn;
          std.warna=libstd_log.warna;
        }

        if(2<std.DEBUG)
        {
          std.info=libstd_log.info;
          std.infoa=libstd_log.infoa;

          std.dump=libstd_log.dump;

          if(console.assert)
            std.assert=libstd_log.assert;

          if(console.profile)
          {
            std.profile={
              begin: console.profile,
              end: console.profileEnd
            };
          }
        }

        std.log=libstd_log.log;
        std.loga=libstd_log.loga;
      }
      //------------------------------------------------------------------------


      /**
       * libstd SCHEDULER
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      libstd_scheduler=function()
      {
        // PROPERTIES
        this.tasks={};
      };


      // ACCESSORS/MUTATORS
      libstd_scheduler.prototype.run=function()
      {
        jQuery.each(this.tasks, function(name_, runnable_) {
          std.run(runnable_);
        });

        setTimeout(function() {std.__SCHEDULER.run();}, 50);
      };

      libstd_scheduler.prototype.schedule=function(name_, closure_)
      {
        std.info("std/scheduler", "Schedule task ["+name_+"].");

        if(closure_.run)
          this.tasks[name_]=closure_;
        else
          this.tasks[name_]=new std.Runnable(closure_);
      };

      libstd_scheduler.prototype.unschedule=function(name_)
      {
        std.info("std/scheduler", "Remove scheduled task ["+name_+"].");

        var tasks={};

        for(var name in this.tasks)
        {
          if(name!=name_)
            tasks[name]=this.tasks[name];
        }

        this.tasks=tasks;
      };


      // INSTANTIATION
      std.__SCHEDULER=new libstd_scheduler();


      // ALIASES
      std.schedule=function(name_, closure_) {std.__SCHEDULER.schedule(name_, closure_);};
      std.unschedule=function(name_) {std.__SCHEDULER.unschedule(name_);};

      setTimeout(function() {std.__SCHEDULER.run();}, 0);
      //------------------------------------------------------------------------


      /**
       * libstd ENV
       *
       * @package net.evalcode.libstd.js
       * @subpackage env
       *
       * @author evalcode.net
       */
      libstd_env=function()
      {
        // PREDEFINED PROPERTIES
        this.USER_AGENT_ENGINE={
          MSIE: 1,
          WEBKIT: 2,
          GECKO: 3
        };
        this.USER_AGENT_BROWSER={
          MSIE: 1,
          CHROME: 2,
          SAFARI: 3,
          FIREFOX: 4
        };
        this.USER_AGENT_ENGINE_NAME={
          1: "msie",
          2: "webkit",
          3: "gecko"
        };
        this.USER_AGENT_BROWSER_NAME={
          1: "msie",
          2: "chrome",
          3: "safari",
          4: "firefox"
        };


        // PROPERTIES
        this.userAgentEngine=null;
        this.userAgentEngineVersion=0;
        this.userAgentBrowser=null;
        this.userAgentBrowserVersion=0;
        this.userAgentMobile=false;

        this.secure="https:"==document.location.protocol;
      };


      // ACCESSORS/MUTATORS
      libstd_env.prototype.init=function()
      {
        // FIXME Less code & higher flexibility (= one good regex).
        var msie=navigator.userAgent.match(/msie\s+([\d.]+)/i);

        if(null==msie)
        {
          var webkit=navigator.userAgent.match(/applewebkit[\/\s]+([\d.]+)/i);

          if(null==webkit)
          {
            this.userAgentEngine=this.USER_AGENT_ENGINE.GECKO;
            this.userAgentEngineVersion=parseInt(navigator.appVersion.match(/[\d.]+/)[0]);
            this.userAgentBrowser=this.USER_AGENT_BROWSER.UNKNOWN;
          }
          else
          {
            this.userAgentEngine=this.USER_AGENT_ENGINE.WEBKIT;
            this.userAgentEngineVersion=parseInt(webkit[1]);

            var chrome=navigator.userAgent.match(/chrome[\/\s]+([\d.]+)/i);

            if(null==chrome)
            {
              var safari=navigator.userAgent.match(/safari[\/\s]+([\d.]+)/i);

              if(null==safari)
              {
                this.userAgentBrowser=this.USER_AGENT_BROWSER.UNKNOWN;
              }
              else
              {
                this.userAgentBrowser=this.USER_AGENT_BROWSER.SAFARI;
                this.userAgentBrowserVersion=parseInt(safari[1]);
              }
            }
            else
            {
              this.userAgentBrowser=this.USER_AGENT_BROWSER.CHROME;
              this.userAgentBrowserVersion=parseInt(chrome[1]);
            }
          }
        }
        else
        {
          this.userAgentEngine=this.USER_AGENT_ENGINE.MSIE;
          this.userAgentEngineVersion=parseInt(msie[1]);
          this.userAgentBrowser=this.USER_AGENT_BROWSER.MSIE;
        }

        this.userAgentMobile=navigator.userAgent.match(/mobile/i)?true:false;

        // FIXME IE.
        jQuery(document.body).addClass(
          this.USER_AGENT_ENGINE_NAME[this.userAgentEngine]+" "
          +this.USER_AGENT_ENGINE_NAME[this.userAgentEngine]+"-"+this.userAgentEngineVersion+" "
          +this.USER_AGENT_BROWSER_NAME[this.userAgentBrowser]+"-"+this.userAgentBrowserVersion
          +(this.userAgentMobile?" mobile":"")
        );

        std.info("std/env", "Resolved user agent [engine: "+this.USER_AGENT_ENGINE_NAME[this.userAgentEngine]
          +", engine-version: "+this.userAgentEngineVersion
          +", browser: "+this.USER_AGENT_BROWSER_NAME[this.userAgentBrowser]
          +", browser-version: "+this.userAgentBrowserVersion
          +", mobile: "+this.userAgentMobile+"]."
        );
      };


      std("env", function(env) {
        env.init();
      });
      //------------------------------------------------------------------------


      /**
       * libstd DOM
       *
       * @package net.evalcode.libstd.js
       * @subpackage dom
       *
       * @author evalcode.net
       */
      libstd_dom=function()
      {

      };


      // PREDEFINED PROPERTIES
      libstd_dom.PATTERN_TAG_SCRIPT_GLOBAL=new RegExp("(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)", "img");
      libstd_dom.PATTERN_TAG_SCRIPT_LOCAL=new RegExp(libstd_dom.PATTERN_TAG_SCRIPT_GLOBAL.source, "im");

      libstd_dom.PATTERN_TAG_STYLE_GLOBAL=new RegExp("(?:<style.*?>)((\n|\r|.)*?)(?:<\/style>)", "img");
      libstd_dom.PATTERN_TAG_STYLE_LOCAL=new RegExp(libstd_dom.PATTERN_TAG_STYLE_GLOBAL.source, "im");


      // ACCESSORS/MUTATORS
      libstd_dom.prototype.init=function()
      {
        jQuery(window).load(function() {
          jQuery("[libstd-dom]").each(function() {
            var element=jQuery(this);
            var attribute=element.attr("libstd-dom");
            var obj=jQuery.parseJSON(attribute);
            jQuery.each(obj, function(method_, args_) {
              jQuery.each(args_, function(property_, selector_) {
                std.dom[method_][property_](selector_);
              });
            });
          });
        });
      };

      libstd_dom.prototype.equalHeight=function(selector_)
      {
        if("object"!=typeof(selector_))
          selector_=[selector_];

        jQuery.each(selector_, function(key_, value_) {
          var height=0;
          jQuery(value_).each(function() {
            height=Math.max(height, jQuery(this).height());
          });
          jQuery(value_).each(function() {
            jQuery(this).height(height);
          });
        });
      };

      libstd_dom.prototype.equalWidth=function(selector_)
      {
        if("object"!=typeof(selector_))
          selector_=[selector_];

        jQuery.each(selector_, function(key_, value_) {
          var width=0;
          jQuery(value_).each(function() {
            width=Math.max(width, jQuery(this).width());
          });
          jQuery(value_).each(function() {
            width(this).width(width);
          });
        });
      };

      libstd_dom.prototype.extractTagsByPattern=function(html_, patternGlobal_, patternLocal_)
      {
        var results=[];

        var code=html_.match(patternGlobal_) || [];

        jQuery.each(code, function(key_, item_) {
          results.push(
            (item_.toString().match(patternLocal_) || ['', ''])[1]
              .replace(/\<\!\-\-/, '')
              .replace(/\-\-\>/, '')
          );
        });

        return results;
      };

      libstd_dom.prototype.extractScriptTags=function(html_)
      {
        return std.dom.extract.tagsByPattern(html_,
          libstd_dom.PATTERN_TAG_SCRIPT_GLOBAL, libstd_dom.PATTERN_TAG_SCRIPT_LOCAL
        );
      };

      libstd_dom.prototype.extractStyleTags=function(html_)
      {
        return std.dom.extract.tagsByPattern(html_,
          libstd_dom.PATTERN_TAG_STYLE_GLOBAL, libstd_dom.PATTERN_TAG_STYLE_LOCAL
        );
      };

      libstd_dom.prototype.stripScriptTags=function(html_)
      {
        return html_.replace(libstd_dom.PATTERN_TAG_SCRIPT_GLOBAL, "");
      };

      libstd_dom.prototype.stripStyleTags=function(html_)
      {
        return html_.replace(libstd_dom.PATTERN_TAG_STYLE_GLOBAL, "");
      };


      // INSTANTIATION
      std.dom=new libstd_dom();


      // ALIASES
      std.dom.equal={
        width: std.dom.equalWidth,
        height: std.dom.equalHeight
      };

      std.dom.extract={
        tagsByPattern: std.dom.extractTagsByPattern,
        scripts: std.dom.extractScriptTags,
        styles: std.dom.extractStyleTags
      };

      std.dom.strip={
        scripts: std.dom.stripScriptTags,
        styles: std.dom.stripStyleTags
      };

      std("dom", function(dom) {
        dom.init();
      });
      //------------------------------------------------------------------------


      /**
       * libstd cache
       *
       * @package net.evalcode.libstd.js
       * @subpackage cache
       *
       * @author evalcode.net
       */
      libstd_cache=function()
      {
        if("undefined"==typeof(window.sessionStorage))
          this.cache=new std.Cache.Backend.Cookie("std/cache/default");
        else
          this.cache=new std.Cache.Backend.SessionStorage("std/cache/default");
      };


      // ACCESSORS/MUTATORS
      libstd_cache.prototype.has=function(key_)
      {
        return this.cache.has(key_);
      };

      libstd_cache.prototype.has_t=function(key_)
      {
        return this.cache.has_t(key_);
      };

      libstd_cache.prototype.get=function(key_)
      {
        return this.cache.get(key_);
      };

      libstd_cache.prototype.get_t=function(key_)
      {
        return this.cache.get_t(key_);
      };

      libstd_cache.prototype.set=function(key_, value_)
      {
        return this.cache.set(key_, value_);
      };

      libstd_cache.prototype.set_t=function(key_, value_, ttl_)
      {
        return this.cache.set_t(key_, value_, ttl_);
      };

      libstd_cache.prototype.add=function(key_, value_)
      {
        return this.cache.add(key_, value_);
      };

      libstd_cache.prototype.add_t=function(key_, value_, ttl_)
      {
        return this.cache.add_t(key_, value_, ttl_);
      };

      libstd_cache.prototype.remove=function(key_)
      {
        return this.cache.remove(key_);
      };

      libstd_cache.prototype.clear=function()
      {
        return this.cache.clear();
      };
      //------------------------------------------------------------------------


      /**
       * libstd COMPONENTS
       *
       * @package net.evalcode.libstd.js
       * @subpackage components
       *
       * @author evalcode.net
       */
      libstd_components=function()
      {

      };


      // PREDEFINED PROPERTIES
      libstd_components.SEVERITY={
        1: "error",
        2: "warn",
        4: "info"
      };
      libstd_components.DEFAULT_STYLE={
        1: libstd_log.COLOR_ERROR,
        2: libstd_log.COLOR_WARN,
        4: libstd_log.COLOR_INFO
      };
      libstd_components.STYLE={
        1: libstd_log.COLOR_DUMP
      };
      libstd_components.STYLE_ARGS="color:#909;font-size:smaller";
      libstd_components.STYLE_LOCATION="color:#553;font-weight:bold;font-size:smaller;";
      libstd_components.__cache=[];


      // ACCESSORS/MUTATORS
      libstd_components.dump=function(dump_, level_)
      {
        if("undefined"==typeof(console))
          return;

        var groupIdx=dump_[0].length;

        for(; groupIdx>0; groupIdx--)
        {
          var items=dump_[0][groupIdx-1];
          var group=dump_[1][groupIdx-1];

          if(group)
          {
            libstd_log.groupBegin(
              group[1] && libstd_components.STYLE[group[1]]?libstd_components.STYLE[group[1]]:libstd_components.DEFAULT_STYLE[group[0]],
              "components/debug",
              group[2]
            );
          }

          jQuery.each(items, function(key_, item_) {
            libstd_components.__cache.push(JSON.stringify(item_));
            libstd_components.__dump(item_);
          });

          if(null!=group)
            libstd_log.groupEnd();
        }
      };

      libstd_components.dumpHeader=function(header_)
      {
        var json=header_.substring(header_.indexOf(": ")+1);

        libstd_components.__cache.push(json);
        libstd_components.__dump(jQuery.parseJSON(json));
      };

      libstd_components.__dump=function(item_)
      {
        if(item_["id"])
        {
          std.error("components/debug", item_["id"]+" "+item_["message"], new std.Exception(
            item_["namespace"],
            item_["id"]+" "+item_["message"],
            item_["file"],
            item_["line"],
            item_["stack"]
          ));
        }
        else
        {
          var method=libstd_components.SEVERITY[item_[0]];

          if("undefined"==typeof(console[method]))
            method="log";

          var msg=item_[4].shift();
          var arg=libstd_components.__extract(item_[4]);
          var loc=(item_[2]?item_[2]:"")+(item_[3]?"["+item_[3]+"]":"");

          if(arg)
          {
            console[method](
              "%c[components/debug] %s\n%c%s %O\n%c%s",
              item_[1] && libstd_components.STYLE[item_[1]]?libstd_components.STYLE[item_[1]]:libstd_components.DEFAULT_STYLE[item_[0]],
              msg,
              libstd_components.STYLE_ARGS,
              JSON.stringify(arg),
              arg,
              libstd_components.STYLE_LOCATION,
              loc
            );
          }
          else if("string"==typeof(msg))
          {
            console[method](
              "%c[components/debug] %s\n%c%s",
              item_[1] && libstd_components.STYLE[item_[1]]?libstd_components.STYLE[item_[1]]:libstd_components.DEFAULT_STYLE[item_[0]],
              msg,
              libstd_components.STYLE_LOCATION,
              loc
            );
          }
          else
          {
            console[method](
              "%c[components/debug] %O %s\n%c%s",
              item_[1] && libstd_components.STYLE[item_[1]]?libstd_components.STYLE[item_[1]]:libstd_components.DEFAULT_STYLE[item_[0]],
              msg,
              JSON.stringify(msg),
              libstd_components.STYLE_LOCATION,
              loc
            );
          }
        }
      };

      libstd_components.__extract=function(arg_)
      {
        if(!arg_)
          return "";

        if("object"==typeof(arg_))
        {
          if(1>arg_.length)
            return "";

          if(2>arg_.length)
            return libstd_components.__extract(arg_.shift());
        }

        return arg_;
      };


      window.grep=function(pattern_)
      {
        var count=0;

        jQuery.each(libstd_components.__cache, function(key_, json_) {
          if(-1<json_.toLowerCase().indexOf(pattern_.toLowerCase()))
          {
            if("undefined"==typeof(console))
              alert(json_);
            else
              libstd_components.__dump(jQuery.parseJSON(json_)); 

            count++;
          }
        });

        return count;
      };
      //------------------------------------------------------------------------


      //------------------------------------------------------------------------
      // TYPES
      //------------------------------------------------------------------------
      /**
       * libstd Object
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      std.Object=function()
      {

      };


      // ACCESSORS/MUTATORS
      std.Object.prototype.type=function()
      {
        throw new Error("Abstract method.");
      };

      std.Object.prototype.log=function(message_, arg_)
      {
        std.log(this.type(), message_, arg_);
      };

      std.Object.prototype.info=function(message_, arg_)
      {
        std.info(this.type(), message_, arg_);
      };

      std.Object.prototype.warn=function(message_, arg_)
      {
        std.warn(this.type(), message_, arg_);
      };

      std.Object.prototype.error=function(message_, arg_)
      {
        std.error(this.type(), message_, arg_);
      };
      //------------------------------------------------------------------------


      /**
       * libstd Cache
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      std.Cache=function(namespace_)
      {
        std.Object.call(this);

        if("undefined"==typeof(window.sessionStorage))
          this.backend=new std.Cache.Backend.Cookie(namespace_);
        else
          this.backend=new std.Cache.Backend.SessionStorage(namespace_);
      };

      std.Cache.Backend={};

      std.Cache.prototype=new std.Object();
      std.Cache.prototype.constructor=std.Cache;


      // OVERRIDES/IMPLEMENTS
      std.Cache.prototype.type=function()
      {
        return "std/cache";
      };


      // ACCESSORS/MUTATORS
      std.Cache.prototype.has=function(key_)
      {
        return this.backend.has(key_);
      };

      std.Cache.prototype.has_t=function(key_)
      {
        return this.backend.has_t(key_);
      };

      std.Cache.prototype.get=function(key_)
      {
        return this.backend.get(key_);
      };

      std.Cache.prototype.get_t=function(key_)
      {
        return this.backend.get_t(key_);
      };

      std.Cache.prototype.set=function(key_, value_)
      {
        return this.backend.set(key_, value_);
      };

      std.Cache.prototype.set_t=function(key_, value_, ttl_)
      {
        return this.backend.set_t(key_, value_, ttl_);
      };

      std.Cache.prototype.add=function(key_, value_)
      {
        return this.backend.add(key_, value_);
      };

      std.Cache.prototype.add_t=function(key_, value_, ttl_)
      {
        return this.backend.add_t(key_, value_, ttl_);
      };

      std.Cache.prototype.remove=function(key_)
      {
        return this.backend.remove(key_);
      };

      std.Cache.prototype.clear=function()
      {
        return this.backend.clear();
      };

      std.Cache.prototype.key=function(key_)
      {
        return this.namespace+":"+key_;
      };
      //------------------------------------------------------------------------


      /**
       * libstd Cache.Backend.SessionStorage
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      std.Cache.Backend.SessionStorage=function(namespace_)
      {
        std.Object.call(this, namespace_);

        this.namespace=namespace_;
        this.storage=window.sessionStorage;
      };

      std.Cache.Backend.SessionStorage.prototype=new std.Cache();
      std.Cache.Backend.SessionStorage.prototype.constructor=std.Cache.Backend.SessionStorage;


      // OVERRIDES/IMPLEMENTS
      std.Cache.Backend.SessionStorage.prototype.type=function()
      {
        return "std/cache/backend/session-storage";
      };


      // ACCESSORS/MUTATORS
      std.Cache.Backend.SessionStorage.prototype.has=function(key_)
      {
        return "undefined"!=typeof(this.storage[this.key(key_)]);
      };

      std.Cache.Backend.SessionStorage.prototype.has_t=function(key_)
      {
        key_=this.key(key_);

        if("undefined"==typeof(this.storage[key_]))
          return false;

        var entry=JSON.parse(this.storage[key_]);

        if(0<entry.ttl && std.timestamp()>(entry.ttl+entry.time))
        {
          this.storage.removeItem(key_);

          return false;
        }

        return true;
      };

      std.Cache.Backend.SessionStorage.prototype.get=function(key_)
      {
        key_=this.key(key_);

        if("undefined"==typeof(this.storage[key_]))
          return false;

        return JSON.parse(this.storage[key_]);
      };

      std.Cache.Backend.SessionStorage.prototype.get_t=function(key_)
      {
        key_=this.key(key_);

        if("undefined"==typeof(this.storage[key_]))
          return false;

        var entry=JSON.parse(this.storage[key_]);

        if(0<entry.ttl && std.timestamp()>(entry.ttl+entry.time))
        {
          this.storage.removeItem(key_);

          return false;
        }

        return entry.value;
      };

      std.Cache.Backend.SessionStorage.prototype.set=function(key_, value_)
      {
        this.storage.setItem(this.key(key_), JSON.stringify(value_));

        return true;
      };

      std.Cache.Backend.SessionStorage.prototype.set_t=function(key_, value_, ttl_)
      {
        key_=this.key(key_);

        if("undefined"==typeof(ttl_))
          ttl_=0;

        this.storage.setItem(key_, JSON.stringify({
          value: value_,
          time: std.timestamp(),
          ttl: ttl_
        }));

        return true;
      };

      std.Cache.Backend.SessionStorage.prototype.add=function(key_, value_)
      {
        key_=this.key(key_);

        if("undefined"!=typeof(this.storage[key_]))
          return false;

        this.storage.setItem(key_, JSON.stringify(value_));

        return true;
      };

      std.Cache.Backend.SessionStorage.prototype.add_t=function(key_, value_, ttl_)
      {
        key_=this.key(key_);

        if("undefined"!=typeof(this.storage[key_]))
        {
          var entry=JSON.parse(this.storage[key_]);

          if(1>entry.ttl || std.timestamp()<(entry.ttl+entry.time))
            return false;
        }

        if("undefined"==typeof(ttl_))
          ttl_=0;

        this.storage.setItem(key_, JSON.stringify({
          value: value_,
          time: std.timestamp(),
          ttl: ttl_
        }));

        return true;
      };

      std.Cache.Backend.SessionStorage.prototype.remove=function(key_)
      {
        this.storage.removeItem(this.key(key_));

        return true;
      };

      std.Cache.Backend.SessionStorage.prototype.clear=function()
      {
        var s=this;

        jQuery.each(this.storage, function(key_, value_) {
          if(key_.startsWith(s.namespace))
          {
            s.storage.removeItem(key_);
          }
        });

        return true;
      };
      //------------------------------------------------------------------------


      /**
       * libstd Cache.Backend.Cookie
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      std.Cache.Backend.Cookie=function(namespace_)
      {
        std.Object.call(this, namespace_);

        this.namespace=namespace_;
      };

      std.Cache.Backend.Cookie.prototype=new std.Cache();
      std.Cache.Backend.Cookie.prototype.constructor=std.Cache.Backend.Cookie;


      // OVERRIDES/IMPLEMENTS
      std.Cache.Backend.Cookie.prototype.type=function()
      {
        return "std/cache/backend/cookie";
      };


      // ACCESSORS/MUTATORS
      std.Cache.Backend.Cookie.prototype.has=function(key_)
      {
        return false;
      };

      std.Cache.Backend.Cookie.prototype.has_t=function(key_)
      {
        return false;
      };

      std.Cache.Backend.Cookie.prototype.get=function(key_)
      {
        return null;
      };

      std.Cache.Backend.Cookie.prototype.get_t=function(key_)
      {
        return null;
      };

      std.Cache.Backend.Cookie.prototype.set=function(key_, value_)
      {
        return false;
      };

      std.Cache.Backend.Cookie.prototype.set_t=function(key_, value_, ttl_)
      {
        return false;
      };

      std.Cache.Backend.Cookie.prototype.add=function(key_, value_)
      {
        return false;
      };

      std.Cache.Backend.Cookie.prototype.add_t=function(key_, value_, ttl_)
      {
        return false;
      };

      std.Cache.Backend.Cookie.prototype.remove=function(key_)
      {
        return true;
      };

      std.Cache.Backend.Cookie.prototype.clear=function()
      {
        return true;
      };
      //------------------------------------------------------------------------


      /**
       * libstd Exception
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      std.Exception=function(namespace_, message_, file_, line_, stack_)
      {
        std.Object.call(this);

        if(!file_)
          file_="unknown";
        if(!line_)
          line_=0;
        if(!stack_)
          stack_=[];


        // PROPERTIES
        this.namespace=namespace_;
        this.message=message_;
        this.file=file_;
        this.line=line_;
        this.stack=stack_;
      };

      std.Exception.prototype=new std.Object();
      std.Exception.prototype.constructor=std.Exception;


      // OVERRIDES/IMPLEMENTS
      std.Exception.prototype.type=function()
      {
        return "std/exception";
      };
      //------------------------------------------------------------------------


      /**
       * libstd Runnable
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      // CONSTRUCTION
      std.Runnable=function(closure_)
      {
        std.Object.call(this);

        this.closure=closure_;
        this.result=null;
      };

      std.Runnable.prototype=new std.Object();
      std.Runnable.prototype.constructor=std.Runnable;


      // ACCESSORS/MUTATORS
      std.Runnable.prototype.run=function()
      {
        var c=this.closure;

        this.result=c();
      };

      std.Runnable.prototype.result=function()
      {
        return this.result;
      };


      // OVERRIDES/IMPLEMENTS
      std.Runnable.prototype.type=function()
      {
        return "std/runnable";
      };
      //------------------------------------------------------------------------


      /**
       * libstd Loader
       *
       * @package net.evalcode.libstd.js
       *
       * @author evalcode.net
       */
      // CONSTRUCTION
      std.Loader=function()
      {
        std.Runnable.call(this);

        this.queue=[];
        this.waiting={};
        this.done=[];
      };

      std.Loader.prototype=new std.Runnable();
      std.Loader.prototype.constructor=std.Loader;


      // ACCESSORS/MUTATORS
      std.Loader.prototype.add=function(uri_, callback_)
      {
        this.queue.push({
          uri: uri_,
          callback: callback_
        });
      };


      // OVERRIDES/IMPLEMENTS
      std.Loader.prototype.type=function()
      {
        return "std/loader";
      };

      std.Loader.prototype.run=function()
      {
        var loader=this;
        var next=this.queue.pop();

        if(next)
        {
          var uri=next.uri;

          if(!this.waiting[uri])
          {
            this.waiting[uri]=[];

            setTimeout(function() {
              jQuery.getScript(uri, function() {
                loader.done.push(uri);
              });
            }, 0);
          }

          this.waiting[uri].push(next.callback);
        }

        jQuery.each(this.waiting, function(uri_, callbacks_) {
          if(-1<loader.done.indexOf(uri_))
          {
            while(callback=callbacks_.pop())
              callback();
          }
        });
      };
      //--------------------------------------------------------------------------
    };


    var libstd_init=function() {
      if("undefined"==typeof(jQuery))
        setTimeout(libstd_init, 10);
      else
        libstd_declare();
    }();
  }
