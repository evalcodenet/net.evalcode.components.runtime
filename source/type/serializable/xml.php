<?php


namespace Components;


  /**
   * Serializable_Xml
   *
   * @package net.evalcode.components
   * @subpackage type.serializable
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
     * @return Serializable_Xml
     */
    function unserializeXml($xml_);
    //--------------------------------------------------------------------------
  }
?>
