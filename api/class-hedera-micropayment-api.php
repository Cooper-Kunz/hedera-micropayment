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

    $msg = array('msg' => 'failed validation');
    if (!$this->validate_content_type($request->get_headers()['content_type'])) {
      return json_encode($msg);
    }
    if (!$this->validate_authorization($request->get_headers()['authorization'])) {
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

    var_dump($fk_anon_id);
    var_dump($record);
    global $wpdb;
    $table_name = $wpdb->prefix . 'hedera_micropayment_records';
    $wpdb->insert($table_name, $record);

    return json_encode(array('msg' => 'success'));  
  }  

  // Validate that our content-type is application/json
  private function validate_content_type($content_type) {
    var_dump($content_type[0]);
    if ($content_type[0] !== 'application/json') {
      return false;
    } 
    return true;
  }

  // TODO: validate our authorization secret properly. If it does not validate, then, we must exit
  private function validate_authorization($secret) {
    var_dump($secret[0]);
    if ($secret === '') {
      return false;
    }
    return true;
  }

}