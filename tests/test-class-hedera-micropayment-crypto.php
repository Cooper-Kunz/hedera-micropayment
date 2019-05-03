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
  
  public function test_generate_key_pair() {
    $h = new HederaMicropaymentCrypto('hedera_micropayment', '0.9.0');
    $keypair = $h->generate_key_pair();
    $secret = $keypair->getSecretKey();
    $public = $keypair->getPublicKey();

    // php halite's ed25519 keys in hex string
    $secret_hex = Hex::encode($secret->getRawKeyMaterial());
    $public_hex = Hex::encode($public->getRawKeyMaterial());

    // var_dump($secret_hex);
    // var_dump($public_hex);

    $secret_hex_decoded = Hex::decode($secret_hex);
    $public_hex_decoded = Hex::decode($public_hex);

    // var_dump($secret->getRawKeyMaterial());

    $secret_original = new SignatureSecretKey(new HiddenString($secret_hex_decoded));

    $this->assertSame($secret->getRawKeyMaterial(), $secret_original->getRawKeyMaterial());


    // node-forge's ed25519 keys in hex string, generated for testing only
    // $forge_secret_hex = ''
    // $forge_public_hex = ''

    // $forge_secret_hex_decoded = Hex::decode($forge_secret_hex);
    // $forge_public_hex_decoded = Hex::decode($forge_public_hex);

    // $forge_secret_original = new SignatureSecretKey(new HiddenString($forge_secret_hex_decoded));
    // $forge_public_original = new SignaturePublicKey(new HiddenString($forge_public_hex_decoded));
    // var_dump($forge_secret_original);

    // $message = '{}';
    // $signature_binary = Asymmetric::sign($message, $forge_secret_original, true);

    // var_dump($signature_binary);
    // var_dump(bin2hex($signature_binary));

    // var_dump($forge_secret_hex);
    // var_dump($forge_public_hex);

  

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
