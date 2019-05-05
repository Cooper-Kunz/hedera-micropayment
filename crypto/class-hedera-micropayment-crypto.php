<?php

// use ParagonIE\Halite\KeyFactory;
use ParagonIE\ConstantTime\Hex;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Asymmetric\{
  Crypto as Asymmetric,
  SignatureSecretKey,
  SignaturePublicKey
};
use ParagonIE\Halite\{
  KeyFactory,
  Halite
};

class HederaMicropaymentCrypto {
  private $plugin_name;
  private $version;
  private $option_name = 'hedera_micropayment';

  public function __construct($plugin_name, $version) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * returns signing keypair for authentication
   * $secret = $keypair->getSecretKey();
   * $public = $keypair->getPublicKey();
   */
  public function generate_key_pair() {
    return KeyFactory::generateSignatureKeyPair();
  }

  public function sign($message, $secret, $encoding = Halite::ENCODE_BASE64URLSAFE) {
    return Asymmetric::sign($message, $secret, $encoding);
  }

  public function verify($message, $public, $signature, $encoding = Halite::ENCODE_BASE64URLSAFE) {
    return Asymmetric::verify($message, $public, $signature ,$encoding);
  }

  public function encodePublic($public) {
    return Hex::encode($public->getRawKeyMaterial());
  }

  public function decodePublic($public_hex) {
    $public = Hex::decode($public_hex);
    return new SignaturePublicKey(new HiddenString($public));
  }

  public function encodeSecret($secret) {
    return Hex::encode($secret->getRawKeyMaterial());
  }

  public function decodeSecret($secret_hex) {
    $secret = Hex::decode($secret_hex);
    return new SignatureSecretKey(new HiddenString($secret));
  }

  public function encodeSignature($signature) {
    return Hex::encode($signature);
  }

  public function decodeSignature($signature) {
    return Hex::decode($signature);
  }

  public function decodeMessage($message) {
    return new HiddenString($message);
  }

  /**
   * @return string - hex-encoded public key from our database
   */
  public function getPublicKey() {
    return get_option($this->option_name . '_payment_server_pub', false);
  }

  /**
   * accepts hex-encoded public key and saves it into our database
   */
  public function setPublicKey($public_hex) {
    update_option($this->option_name . '_payment_server_pub', $public_hex, false);
  }
}