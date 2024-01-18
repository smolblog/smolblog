<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\ActivityPubActivityTestKit;
use Smolblog\Test\TestCase;

final class AcceptTest extends TestCase {
	use ActivityPubActivityTestKit;
	const EXPECTED_TYPE = 'Accept';

	protected function setUp(): void {
		$actor = '//smol.blog/'.$this->randomId().'activitypub/actor';
		$this->subject = new Accept(
			id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
			actor: $actor,
			object: new Follow(
				id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
				actor: '//smol.blog/'.$this->randomId().'activitypub/actor',
				object: $actor,
			),
		);
	}
}
