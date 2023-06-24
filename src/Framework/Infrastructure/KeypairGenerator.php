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
			publicKey: str_replace(["\r\n", "\r", "\n"], "\n", $key->getPublicKey()),
			privateKey: str_replace(["\r\n", "\r", "\n"], "\n", $key->__toString()),
		);
	}
}
