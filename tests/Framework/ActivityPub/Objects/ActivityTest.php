<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\ActivityPubActivityTestKit;
use Smolblog\Test\TestCase;

final class ActivityTest extends TestCase {
	use ActivityPubActivityTestKit;
	const EXPECTED_TYPE = 'Activity';

	protected function setUp(): void {
		$actor = '//smol.blog/'.$this->randomId().'activitypub/actor';
		$this->subject = new Activity(
			id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
			actor: new Actor(
				id: $actor,
				type: ActorType::Person,
			),
			// actor: $actor,
			object: new Follow(
				id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
				object: '//smol.blog/'.$this->randomId().'activitypub/actor',
				actor: $actor,
			),
		);
	}
}
