<?php

/**
 * Class HederaMicropaymentPublicTest
 *
 * @package HederaMicropayment
 */

class HederaMicropaymentPublicTest extends WP_UnitTestCase {

  public function test_get_random_node() {
    // use reflection to retrieve the submit_rand_node method, so as to test it
    // this is necessary because submit_rand_node is a private method
    $get_random_node = get_method('HederaMicropaymentPublic', 'get_random_node');
    $hederaPublic = new HederaMicropaymentPublic('hedera_micropayment', '0.9.0');
    $key = $get_random_node->invokeArgs($hederaPublic, array());
 
    // this is for logging purpost in cli
    // $b = 'some other value';
    // $this->assertEquals('some value', $b, '$key is not equal to "some value", instead it is instead: ' . $key);

    $this->assertInternalType('string', $key);
    $this->assertTrue(Hedera::valid_account_id($key));
  }

}