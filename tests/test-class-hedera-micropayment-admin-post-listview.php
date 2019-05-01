<?php

/**
 * Class HederaMicropaymentAdminPostListviewTest
 *
 * @package HederaMicropayment
 */
class HederaMicropaymentAdminPostListviewTest extends WP_UnitTestCase {

  public function test_manage_posts() {

    $h = new HederaMicropaymentAdminPostListview('hedera_micropayment', '0.9.0');
    $columns = $h->manage_posts( array() );
    $this->assertTrue(array_key_exists('micropayment', $columns));
  }
}