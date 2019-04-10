<?php

class HederaMicropaymentCryptoTest extends WP_UnitTestCase {
  
  public function test_generate_key_pair() {
    $h = new HederaMicropaymentCrypto('hedera_micropayment', '0.9.0');
    $keypair = $h->generate_key_pair();
    $secret = $keypair->getSecretKey();
    $public = $keypair->getPublicKey();

    $message = 'test message';
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

}
