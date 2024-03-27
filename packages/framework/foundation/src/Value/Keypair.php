<?php

namespace Smolblog\Framework\Foundation\Value;

use Smolblog\Framework\Foundation\Value;
use Smolblog\Framework\Foundation\Value\Traits\SerializableValue;
use Smolblog\Framework\Foundation\Value\Traits\SerializableValueKit;

/**
 * Represents a public key and optionally its corresponding private key.
 */
readonly class Keypair extends Value implements SerializableValue {
	use SerializableValueKit;

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
