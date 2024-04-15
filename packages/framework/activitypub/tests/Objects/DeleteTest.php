<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\Kits\ActivityPubActivityTestKit;
use Smolblog\Test\TestCase;

final class DeleteTest extends TestCase {
	use ActivityPubActivityTestKit;
	const EXPECTED_TYPE = 'Delete';

	protected function setUp(): void {
		$actor = '//smol.blog/'.$this->randomId().'activitypub/actor';
		$this->subject = new Delete(
			id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
			actor: $actor,
			to: ['https://www.w3.org/ns/activitystreams#Public'],
			object: $actor,
		);
	}
}
