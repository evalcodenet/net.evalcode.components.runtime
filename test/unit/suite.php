<?php


namespace Components;


  /**
   * Runtime_Test_Unit_Suite
   *
   * @package net.evalcode.components.runtime
   * @subpackage test.unit
   *
   * @author evalcode.net
   */
  class Runtime_Test_Unit_Suite implements Test_Unit_Suite
  {
    // OVERRIDES
    public function name()
    {
      return 'runtime/test/unit/suite';
    }

    public function cases()
    {
      return array(
        'Components\\Runtime_Test_Unit_Case_Log'
      );
    }
    //--------------------------------------------------------------------------
  }
?>
