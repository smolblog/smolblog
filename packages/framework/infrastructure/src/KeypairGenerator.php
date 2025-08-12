<?php

namespace Smolblog\Infrastructure;

use phpseclib3\Crypt\RSA;

/**
 * Service to generate an RSA public/private keypair.
 */
class KeypairGenerator {
	/**
	 * Generate an RSA Keypair.
	 *
	 * @return Keypair
	 */
	public function generate(): Keypair {
		$key = RSA::createKey();

		// @codeCoverageIgnoreStart
		$pub = str_replace(["\r\n", "\r", "\n"], "\n", strval($key->getPublicKey()));
		$priv = str_replace(["\r\n", "\r", "\n"], "\n", $key->__toString());
		// @codeCoverageIgnoreEnd
		return new Keypair(publicKey: $pub, privateKey: $priv);
	}
}
