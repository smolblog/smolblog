<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\Kits\SerializableTestKit;
use Smolblog\Test\TestCase;

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
