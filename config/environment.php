<?php


  \Components\Environment::push(\Components\Environment::create()
    ->setPathWeb(COMPONENTS_PATH_APP.'/web')
    ->setPathResource(COMPONENTS_PATH_APP.'/web/media')
    // TODO Automatically resolve relative path of pathResource to pathWeb.
    ->setUriResource('/media')
  );
?>
