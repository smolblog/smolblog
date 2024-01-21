<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\Kits\ActivityPubActivityTestKit;
use Smolblog\Test\TestCase;

final class UndoTest extends TestCase {
	use ActivityPubActivityTestKit;
	const EXPECTED_TYPE = 'Undo';

	protected function setUp(): void {
		$actor = '//smol.blog/'.$this->randomId().'activitypub/actor';
		$this->subject = new Undo(
			id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
			actor: $actor,
			object: new Follow(
				id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
				actor: $actor,
				object: '//smol.blog/'.$this->randomId().'activitypub/actor',
			),
		);
	}
}
