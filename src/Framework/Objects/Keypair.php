<?php

namespace Smolblog\Framework\Objects;

/**
 * Represents a public key and optionally its corresponding private key.
 */
class Keypair extends Value {
	/**
	 * Construct the object.
	 *
	 * @param string      $publicKey  PEM-formatted public key.
	 * @param string|null $privateKey PEM-formatted private key.
	 */
	public function __construct(
		public readonly string $publicKey,
		public readonly ?string $privateKey = null,
	) {
	}
}
