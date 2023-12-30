<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use JsonSerializable;
use Smolblog\Framework\Objects\ArraySerializable;
use Smolblog\Framework\Objects\SerializableKit;
use Smolblog\Test\SerializableTestKit;
use Smolblog\Test\TestCase;

/**
 * A public encryption key attached to an actor.
 */
readonly class ActorPublicKey implements ArraySerializable, JsonSerializable {
	use SerializableKit;

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

final class ActorPublicKeyTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void {
		$actor = '//smol.blog/'.$this->randomId().'/activitypub/actor';
		$this->subject = new ActorPublicKey(
			id: "$actor#publicKey",
			owner: $actor,
			publicKeyPem: $this->randomId()->toString(),
		);
	}
}
