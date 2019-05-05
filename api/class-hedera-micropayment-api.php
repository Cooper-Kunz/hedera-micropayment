<?php

class HederaMicropaymentAPI {
  private $plugin_name;
  private $version;
  private $option_name = "hedera_micropayment";

  public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
  }

  public function HederaMicropayment_api_routes() {
    $this->rest_hash();
    $this->rest_hello();
  }
  
    /**
   * curl -X POST http://localhost:8080/\?rest_route\=/hedera-micropayment/v1/hash \
   * --data '{ "anonId": "111", "hash": "981d0d87a97106762e88a44820c60b96" }' \ 
   * --header "authorization: d1d1303584" \
   * --header "Content-Type: application/json"
   */
  public function rest_hash() {
    register_rest_route('hedera-micropayment/v1', '/hash/', array(
      'methods' => 'POST',
      'callback' => array($this, 'rest_hash_cb')
    ));
  }

  public function rest_hash_cb($request) {

    $msg_failed = array('msg' => 'failed');
    $msg_success = array('msg' => 'success');

    $post_headers = $request->get_headers();
    if (!$post_headers[NAME_AUTHORIZATION]) {
      return new WP_REST_Response($msg_failed, 403);
    }

    $nonce = $post_headers[NAME_AUTHORIZATION][0];

    $post_data = $request->get_json_params();
    $post_data[NAME_ANON_ID] = $post_data['anonId'];
    unset($post_data['anonId']);
    $anon_id = (int)$post_data[NAME_ANON_ID];
    $hash = $post_data['hash'];

    global $wpdb;
    $table_name = $wpdb->prefix . $this->option_name . NAME_ANON_USERS;
    $query = "SELECT * FROM $table_name WHERE id = $anon_id";
    $result = $wpdb->get_row($query);

    if (empty($result->hash)) {
      $data = array('hash' => $hash, NAME_NONCE => $nonce);
      $where = array('id' => $anon_id);
      $updated = $wpdb->update($table_name, $data, $where);
      if (!$updated) {
        return new WP_REST_Response($msg_failed, 200);
      }
    }
    return new WP_REST_Response($msg_success, 200);    
  }

  // API end point
  // either 
  // curl -X POST http://localhost:8080/\?rest_route\=/hedera-micropayment/v1/hello \
  // --data '{"transactionId":"somestuff","account":"0.0.1000", "memo": "138,1", "cost": 1000, "nodePrecheckcode": 0 }' \ 
  // --header "authorization: somesecretkey" 
  // --header "Content-Type: application/json"
  // or
  // curl -X POST http://localhost:8080/wp-json/hedera-micropayment/v1/hello \
  // --data '{"transactionId":"somestuff","account":"0.0.1000", "memo": "138,1", "cost": 1000, "nodePrecheckcode": 0 }' \ 
  // --header "authorization: somesecretkey" 
  // --header "Content-Type: application/json"
  public function rest_hello() {
    register_rest_route('hedera-micropayment/v1', '/hello/', array(
      'methods' => 'POST',
      'callback' => array($this, 'rest_hello_cb')
    ));
  }

  public function rest_hello_cb( $request ) {

    $msg = array('msg' => 'failed validation. invalid content type.');
    if (!$this->validate_content_type($request->get_headers()['content_type'])) {
      return json_encode($msg);
    }

    $message = $request->get_body();
    $authorization_header = $request->get_headers()['authorization'];
    $msg = array('msg' => 'failed validation. invalid authorization.');
    if (!$this->validate_authorization($message, $authorization_header)) {
      return json_encode($msg);
    }

    $result = $request->get_json_params();
    $record = $result;
    $record['transaction_id'] = $record['transactionId'];
    unset($record['transactionId']);
    $record['node_precheckcode'] = $record['nodePrecheckcode'];
    unset($record['nodePrecheckcode']);
    
    $memo = $result['memo'];
    if (!empty($memo)) {
      $pieces = explode(",", $memo);
      if (is_array($pieces)) {
        $record['fk_anon_users'] = (int)$pieces[0];
        $record['content_id'] = $pieces[1];
      }
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'hedera_micropayment_records';
    $wpdb->insert($table_name, $record);

    return json_encode(array('msg' => 'success'));  
  }  

  // Validate that our content-type is application/json
  private function validate_content_type($content_type) {
    $content_type_header = $content_type[0];
    $content_type_match = preg_match_all('/^application\/json/', $content_type_header);
    if (!$content_type_match) {
      return false;
    } 
    return true;
  }

  // Authorization $secret contains our signature
  private function validate_authorization($message, $authorization_header) {
    if (is_array($authorization_header)) {
      // extract the hex-encoded signature from our authorization header
      $authorization = $authorization_header[0];
      $pieces = explode(' ', $authorization);
      $signature_hex = $pieces[1];
      return $this->verify_message_with_signature($message, $signature_hex);
    }
    return false;
  }

  // $message is the message (string), $token is the hex-encoded signature
  private function verify_message_with_signature($message, $signature_hex) {
    // recover the signature in bytes, from hex (as sent by Payment Server)
    $signature = hex2bin($signature_hex);

    // retrieve public key, from database
    $crypto = new HederaMicropaymentCrypto($this->option_name, $this->version);
    $public_hex = $crypto->getPublicKey();
    $public = $crypto->decodePublic($public_hex);

    return $crypto->verify($message, $public, $signature, true);
  }

}