<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\Kits\SerializableTestKit;
use Smolblog\Test\TestCase;

final class ActivityPubObjectTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void {
		$this->subject = new ActivityPubObject(
			id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
			randomProperty: 'hullo',
		);
	}

	public function testItIsAnObjectType() {
		$this->assertEquals('Object', $this->subject->type());
	}

	public function testItOnlyRequiresAnId() {
		$this->assertInstanceOf(
			ActivityPubObject::class,
			new ActivityPubObject(id: '//smol.blog/athing'),
		);
	}
}
