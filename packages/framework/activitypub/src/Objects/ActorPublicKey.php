<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Foundation\Value\Traits\SerializableValueKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;

/**
 * A public encryption key attached to an actor.
 */
readonly class ActorPublicKey implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Construct the object.
	 *
	 * @param string $id           ID of the key.
	 * @param string $owner        Actor that owns the key.
	 * @param string $publicKeyPem Actual PEM-formatted string of the key.
	 */
	public function __construct(
		public string $id,
		public string $owner,
		public string $publicKeyPem,
	) {
	}
}
