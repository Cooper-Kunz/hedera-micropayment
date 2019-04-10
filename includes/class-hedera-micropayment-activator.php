<?php

global $hedera_micropayment_db_version;
$hedera_micropayment_db_version = '1.0.0';

function hedera_micropayment_create_db() {
  global $wpdb;
  global $hedera_micropayment_db_version;

  $charset_collate = $wpdb->get_charset_collate();
  $wp_table_user = $wpdb->prefix . 'users';
  $table_name = $wpdb->prefix . 'hedera_micropayment_anon_users';
  $table_name_2 = $wpdb->prefix . 'hedera_micropayment_records';
 
  $sql = "CREATE TABLE $table_name (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    free_content INTEGER NULL,
    nonce varchar(255),
    hash varchar(255),
    fk_wp_user_id bigint unsigned,
    FOREIGN KEY (fk_wp_user_id)
    REFERENCES $wp_table_user(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT  
  ) $charset_collate;";

  $sql2 = "CREATE TABLE $table_name_2 (
    id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    transaction_id varchar(255) NOT NULL,
    account varchar(2000),
    memo varchar(2000),
    content_id varchar(255),
    cost int,
    node_precheckcode int,
    fk_anon_users int NOT NULL,
    FOREIGN KEY (fk_anon_users)
    REFERENCES $table_name(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( array($sql, $sql2) );

  // version our plugin db
  add_option('hedera_micropayment_db_version', $hedera_micropayment_db_version);
}

class HederaMicropaymentActivator {

    public static function activate() {
      hedera_micropayment_create_db();
    }

}