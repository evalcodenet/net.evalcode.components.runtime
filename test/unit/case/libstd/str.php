<?php


namespace Components;


  /**
   * Runtime_Test_Unit_Case_Libstd_Str
   *
   * @package net.evalcode.components.runtime
   * @subpackage test.unit.case.libstd
   *
   * @author evalcode.net
   */
  class Runtime_Test_Unit_Case_Libstd_Str implements Test_Unit_Case
  {
    // TESTS
    /**
     * @test
     * @profile(fork)
     */
    public function testDetectors()
    {
      assertTrue(\str\isNullOrEmpty(null));
      assertTrue(\str\isNullOrEmpty(''));

      assertFalse(\str\isNullOrEmpty('0.00'));
      assertFalse(\str\isNullOrEmpty('00000'));
      assertFalse(\str\isNullOrEmpty('0.001'));
      assertFalse(\str\isNullOrEmpty('f'));
      assertFalse(\str\isNullOrEmpty('foo'));

      assertTrue(\str\isNullOrZero(null));
      assertTrue(\str\isNullOrZero(''));
      assertTrue(\str\isNullOrZero('0.00'));
      assertTrue(\str\isNullOrZero('00000'));

      assertFalse(\str\isNullOrZero('0.001'));
      assertFalse(\str\isNullOrZero('f'));
      assertFalse(\str\isNullOrZero('foo'));

      assertTrue(\str\isZero('0.00'));
      assertTrue(\str\isZero('00000'));

      assertFalse(\str\isZero(null));
      assertFalse(\str\isZero(''));
      assertFalse(\str\isZero('0.001'));
      assertFalse(\str\isZero('f'));
      assertFalse(\str\isZero('foo'));
    }
    //--------------------------------------------------------------------------
  }
?>
