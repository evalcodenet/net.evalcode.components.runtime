<?php


namespace Components;


  /**
   * Serializable_Xml
   *
   * @api
   * @package net.evalcode.components.type
   * @subpackage serializable
   *
   * @author evalcode.net
   */
  interface Serializable_Xml extends Serializable
  {
    // ACCESSORS
    /**
     * @return string
     */
    function serializeXml();
    /**
     * @param string $xml_
     *
     * @return \Components\Serializable_Xml
     */
    function unserializeXml($xml_);
    //--------------------------------------------------------------------------
  }
?>
