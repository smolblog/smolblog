<?php

namespace Smolblog\Framework\Infrastructure;

use phpseclib3\Crypt\RSA;
use Smolblog\Framework\Objects\Keypair;

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

		return new Keypair(
			publicKey: $key->getPublicKey(),
			privateKey: $key->__toString(),
		);
	}
}
