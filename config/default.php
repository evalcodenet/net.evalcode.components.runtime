<?php


namespace Components;


  Log::push(new Log_Appender_Syslog(COMPONENTS_APP_CODE));

  if(Environment::isDev())
    Runtime::addManagementIp(Runtime::getClientAddress());


  if(isset($_REQUEST['debug']) || Environment::isDev())
  {
    if(isset($_REQUEST['debug']))
      $debug=(int)$_REQUEST['debug'];
    else
      $debug=Debug::INFO;

    if(0<$debug)
      Debug::activate();

    Debug::verbosity($debug);

    Debug::appender(Debug::INFO, new Debug_Appender_Console());
    Debug::enable(Debug::MARKUP);
  }
?>
