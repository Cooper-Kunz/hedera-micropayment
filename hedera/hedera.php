<?php

class Hedera {

  static function valid_account_id($account_id_string) {
    $account_id_array = explode('.', $account_id_string);
    if (!is_array($account_id_array) || count($account_id_array) !== 3) {
      return false;
    }
    foreach($account_id_array as $el) {
      if (!is_numeric($el)) {
        return false;
      }
    }
    return true;
  }

}