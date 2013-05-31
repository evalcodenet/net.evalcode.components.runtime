<?php


namespace Components;


  /**
   * Serializable_Json
   *
   * @package net.evalcode.components
   * @subpackage type.serializable
   *
   * @author evalcode.net
   */
  interface Serializable_Json extends Serializable
  {
    // ACCESSORS
    /**
     * @return string
     */
    function serializeJson();

    /**
     * @param string $json_
     *
     * @return Serializable_Json
     */
    function unserializeJson($json_);
    //--------------------------------------------------------------------------
  }
?>
