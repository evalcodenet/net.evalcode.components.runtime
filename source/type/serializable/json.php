<?php


namespace Components;


  /**
   * Serializable_Json
   *
   * @api
   * @package net.evalcode.components.type
   * @subpackage serializable
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
     * @return \Components\Serializable_Json
     */
    function unserializeJson($json_);
    //--------------------------------------------------------------------------
  }
?>
