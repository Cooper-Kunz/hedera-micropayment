<?php

class HederaTest extends WP_UnitTestCase {

  public function test_valid_account_id() {
    // valid accountID string
    $valid = Hedera::valid_account_id('0.0.3');
    $this->assertTrue($valid);

    // invalid accountID string
    $valid = Hedera::valid_account_id('0.1');
    $this->assertFalse($valid);

    // invalid accountID string
    $valid = Hedera::valid_account_id('1');
    $this->assertFalse($valid);

    // invalid accountID string
    $valid = Hedera::valid_account_id('0.0.0.3');
    $this->assertFalse($valid);

    // invalid accountID string
    $valid = Hedera::valid_account_id(1);
    $this->assertFalse($valid);

    // invalid accountID string
    $valid = Hedera::valid_account_id('hello');
    $this->assertFalse($valid);
  }

}