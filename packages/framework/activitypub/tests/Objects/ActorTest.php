<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\Kits\SerializableTestKit;
use Smolblog\Test\TestCase;

final class ActorTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void {
		$this->subject = new Actor(
			id: '//smol.blog/'.$this->randomId().'./activitypub/actor',
			type: ActorType::Organization,
			publicKeyPem: $this->randomId()->toString(),
			inbox: '//smol.blog/inbox',
			outbox: '//smol.blog/outbox',
		);
	}
}
