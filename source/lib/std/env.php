<?php


namespace env;


    /**
     * LIBSTD
     *
     * @package net.evalcode.libstd.php
     * @subpackage env
     *
     * @author evalcode.net
     */


    // PREDEFINED PROPERTIES
    /**
     * @internal
     */
    define('LIBSTD_ENV_CHARSET_ASCII', 'ASCII');
    /**
     * @internal
     */
    define('LIBSTD_ENV_CHARSET_BINARY', 'BINARY');
    /**
     * @internal
     */
    define('LIBSTD_ENV_CHARSET_ISO8859_1', 'ISO-8859-1');
    /**
     * @internal
     */
    define('LIBSTD_ENV_CHARSET_ISO8859_15', 'ISO-8859-15');
    /**
     * @internal
     */
    define('LIBSTD_ENV_CHARSET_UTF8', 'UTF-8');
    /**
     * @internal
     */
    define('LIBSTD_ENV_CHARSET_UTF16', 'UTF-16');
    /**
     * @internal
     */
    define('LIBSTD_ENV_CHARSET_UTF16BE', 'UTF-16BE');
    /**
     * @internal
     */
    define('LIBSTD_ENV_CHARSET_UTF16LE', 'UTF-16LE');


    define('LIBSTD_ENV_USER_AGENT_ENGINE_UNKNOWN', 0);
    define('LIBSTD_ENV_USER_AGENT_ENGINE_MSIE', 1);
    define('LIBSTD_ENV_USER_AGENT_ENGINE_WEBKIT', 2);
    define('LIBSTD_ENV_USER_AGENT_ENGINE_GECKO', 3);

    define('LIBSTD_ENV_USER_AGENT_BROWSER_UNKNOWN', 0);
    define('LIBSTD_ENV_USER_AGENT_BROWSER_MSIE', 1);
    define('LIBSTD_ENV_USER_AGENT_BROWSER_CHROME', 2);
    define('LIBSTD_ENV_USER_AGENT_BROWSER_SAFARI', 3);
    define('LIBSTD_ENV_USER_AGENT_BROWSER_FIREFOX', 4);
    //--------------------------------------------------------------------------


    // PROPERTIES
    libstd_set('charset', LIBSTD_ENV_CHARSET_UTF8, 'env');
    //--------------------------------------------------------------------------


    // ACCESSORS
    /**
     * Default charset.
     *
     * @return string
     */
    function charset()
    {
      return libstd_get('charset', 'env');
    }

    /**
     * Set default charset.
     *
     * @param string $charset_
     */
    function charset_set($charset_)
    {
      libstd_set('charset', $charset_, 'env');
    }

    /**
     * Reset default charset.
     */
    function charset_unset()
    {
      libstd_set('charset', LIBSTD_ENV_CHARSET_UTF8, 'env');
    }

    /**
     * Toggle debug mode.
     *
     * @param boolean $enabled_
     *
     * @return boolean
     */
    function debug($enabled_=null)
    {
      if(null!==$enabled_)
        libstd_set('debug', $enabled_, 'env');

      return (bool)libstd_get('debug', 'env');
    }

    /**
     * @return string
     */
    function hostname()
    {
      if(false===libstd_isset('hostname', 'env'))
      {
        if(isset($_SERVER['HTTP_HOST']))
          return libstd_set('hostname', $_SERVER['HTTP_HOST'], 'env');

        return libstd_set('hostname', gethostname(), 'env');
      }

      return libstd_get('hostname', 'env');
    }

    /**
     * @return integer
     */
    function userAgentEngine()
    {
      if(false===libstd_isset('useragent', 'env'))
        return userAgent()['engine'];

      return libstd_get('useragent', 'env')['engine'];
    }

    /**
     * @return integer
     */
    function userAgentEngineVersion()
    {
      if(false===libstd_isset('useragent', 'env'))
        return userAgent()['engine_version'];

      return libstd_get('useragent', 'env')['engine_version'];
    }

    /**
     * @return integer
     */
    function userAgentBrowser()
    {
      if(false===libstd_isset('useragent', 'env'))
        return userAgent()['browser'];

      return libstd_get('useragent', 'env')['browser'];
    }

    /**
     * @return integer
     */
    function userAgentBrowserVersion()
    {
      if(false===libstd_isset('useragent', 'env'))
        return userAgent()['browser_version'];

      return libstd_get('useragent', 'env')['browser_version'];
    }

    /**
     * @return boolean
     */
    function userAgentIsMobile()
    {
      if(false===libstd_isset('useragent', 'env'))
        return userAgent()['mobile'];

      return libstd_get('useragent', 'env')['mobile'];
    }

    /**
     * @return boolean
     */
    function userAgentIsMsie()
    {
      if(false===libstd_isset('useragent', 'env'))
        return LIBSTD_ENV_USER_AGENT_BROWSER_MSIE===userAgent()['browser'];

      return LIBSTD_ENV_USER_AGENT_BROWSER_MSIE===libstd_get('useragent', 'env')['browser'];
    }

    /**
     * @return scalar[]
     */
    function userAgent()
    {
      static $engines=[
        'ie'=>LIBSTD_ENV_USER_AGENT_ENGINE_MSIE,
        'gecko'=>LIBSTD_ENV_USER_AGENT_ENGINE_GECKO,
        'webkit'=>LIBSTD_ENV_USER_AGENT_ENGINE_WEBKIT
      ];
      static $browsers=[
        'ie'=>LIBSTD_ENV_USER_AGENT_BROWSER_MSIE,
        'chrome'=>LIBSTD_ENV_USER_AGENT_BROWSER_CHROME,
        'firefox'=>LIBSTD_ENV_USER_AGENT_BROWSER_FIREFOX,
        'safari'=>LIBSTD_ENV_USER_AGENT_BROWSER_SAFARI
      ];

      if(false===libstd_isset('useragent', 'env'))
      {
        if(false===function_exists('get_browser'))
          $info=false;
        else
          $info=get_browser(null, true);

        $userAgent=[];

        if(false===$info)
        {
          $userAgent=[
            'engine'=>LIBSTD_ENV_USER_AGENT_ENGINE_UNKNOWN,
            'engine_version'=>0,
            'browser'=>LIBSTD_ENV_USER_AGENT_BROWSER_UNKNOWN,
            'browser_version'=>0,
            'mobile'=>false
          ];
        }
        else
        {
          if(isset($info['browser']))
          {
            $browserName=strtolower($info['browser']);

            if(isset($browsers[$browserName]))
              $userAgent['browser']=$browsers[$browserName];
            else
              $userAgent['browser']=LIBSTD_ENV_USER_AGENT_BROWSER_UNKNOWN;
          }
          else
          {
            $userAgent['browser']=LIBSTD_ENV_USER_AGENT_BROWSER_UNKNOWN;
          }

          if(isset($info['renderingengine_name']))
          {
            $engineName=strtolower($info['renderingengine_name']);

            if(isset($engines[$engineName]))
              $userAgent['engine']=$engines[$engineName];
            else
              $userAgent['engine']=LIBSTD_ENV_USER_AGENT_ENGINE_UNKNOWN;
          }
          else
          {
            $userAgent['engine']=LIBSTD_ENV_USER_AGENT_ENGINE_UNKNOWN;
          }

          if(isset($info['majorver']))
            $userAgent['browser_version']=(int)$info['majorver'];
          else
            $userAgent['browser_version']=0;

          if(isset($info['renderingengine_version']))
            $userAgent['engine_version']=(int)$info['renderingengine_version'];
          else
            $userAgent['engine_version']=0;

          $userAgent['mobile']=isset($info['ismobiledevice']) && (bool)$info['ismobiledevice'];
        }

        libstd_set('useragent', $userAgent);

        return $userAgent;
      }

      return libstd_get('useragent', 'env');
    }
    //--------------------------------------------------------------------------
?>
