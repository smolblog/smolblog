<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\ActivityPubActivityTestKit;
use Smolblog\Test\TestCase;

final class FollowTest extends TestCase {
	use ActivityPubActivityTestKit;
	const EXPECTED_TYPE = 'Follow';

	protected function setUp(): void {
		$this->subject = new Follow(
			id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
			actor: '//smol.blog/'.$this->randomId().'activitypub/actor',
			object: '//smol.blog/'.$this->randomId().'activitypub/actor',
		);
	}
}
