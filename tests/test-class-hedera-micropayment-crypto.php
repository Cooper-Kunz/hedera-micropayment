<?php

/**
 * Class HederaMicropaymentCryptoTest
 *
 * @package HederaMicropayment
 */

use ParagonIE\ConstantTime\Hex;
use ParagonIE\HiddenString\HiddenString;
use ParagonIE\Halite\Asymmetric\{
  Crypto as Asymmetric,
  SignatureSecretKey,
  SignaturePublicKey
};

class HederaMicropaymentCryptoTest extends WP_UnitTestCase {
 
  private $plugin_name = 'hedera_micropayment';
  private $version = '1.0.0';
  private $option_name = 'hedera_micropayment';
  
  public function test_generate_key_pair() {
    $h = new HederaMicropaymentCrypto($this->plugin_name, $this->version);
    $keypair = $h->generate_key_pair();
    $secret = $keypair->getSecretKey();
    $public = $keypair->getPublicKey();

    // php halite's ed25519 keys in hex string
    $secret_hex = Hex::encode($secret->getRawKeyMaterial());
    $public_hex = Hex::encode($public->getRawKeyMaterial());

    $secret_hex_decoded = Hex::decode($secret_hex);
    $public_hex_decoded = Hex::decode($public_hex);

    $secret_original = new SignatureSecretKey(new HiddenString($secret_hex_decoded));

    $this->assertSame($secret->getRawKeyMaterial(), $secret_original->getRawKeyMaterial());

    $message = 'test message';
    
    $signature_encoding = $h->sign($message, $secret, true);
    $this->assertTrue(strlen($signature_encoding) === 64);
    $signature_binary = bin2hex($signature_encoding);

    $signature = $h->sign($message, $secret);
    $this->assertTrue(strlen($signature) === 88);

    $verify = $h->verify($message, $public, $signature);
    $this->assertTrue($verify);

    // prove that we can decode a secret key in hex format and use it
    $secret_hex = $h->encodeSecret($secret);
    $secret_decoded = $h->decodeSecret($secret_hex);
    $signature = $h->sign($message, $secret_decoded);
    $this->assertTrue(strlen($signature) === 88);

    // prove that we can decode a public key in hex format and a signature in hex format
    // and using the hex decoded objects to verify our message
    $public_hex = $h->encodePublic($public);
    $signature_hex = $h->encodeSignature($signature);
    $public_decoded = $h->decodePublic($public_hex);
    $signature_decoded = $h->decodeSignature($signature_hex);
    $verify = $h->verify($message, $public_decoded, $signature_decoded);
    $this->assertTrue($verify);
  }

  public function test_set_and_get_public_key() {
    $h = new HederaMicropaymentCrypto('hedera_micropayment', '0.9.0');
    $keypair = $h->generate_key_pair();
    $public = $keypair->getPublicKey();
    $public_hex = $h->encodePublic($public);
    $h->setPublicKey($public_hex);
    $public_hex_retrieved = $h->getPublicKey();
    $this->assertSame($public_hex, $public_hex_retrieved);
  }

  public function test_node_forge_halite_compatibility() {
    // test data, which we generate from nodejs' node-forge
    $secret_hex = '28f9609a24b7b532b296a9d45e6bed220c31944b5e9e59691cf1f32406c76f34c3ecd99819731b431b1e5d7b85d132f9566db0b2e7ce155525c75c5671c17487';
    $public_hex = 'c3ecd99819731b431b1e5d7b85d132f9566db0b2e7ce155525c75c5671c17487';
    $message = '{}';
    $signature_hex = '2db21ebba17003558569717526f5e082ea9e4d5214ef53578b0f401be6a801171ba4882bb5f82ac0efb7aaf55919cc995d3eb7f56492aa9f443d3a6c94af920d';

    $h = new HederaMicropaymentCrypto($this->plugin_name, $this->version);
    $secret = $h->decodeSecret($secret_hex);
    $public = $h->decodePublic($public_hex);
    $signature = $h->sign($message, $secret, true);
    $signature_hex_equivalent = bin2hex($signature);

    // proves that our node-forge generated test data matches with halite signing
    $this->assertSame($signature_hex_equivalent, $signature_hex);

    $verified = $h->verify($message, $public, $signature, true);
    $this->assertTrue($verified);
  }

}
