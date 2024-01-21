<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\Kits\SerializableTestKit;
use Smolblog\Test\TestCase;

final readonly class TestObject extends ActivityPubBase {
	public function type(): string { return 'test'; }
}

final class ActivityPubBaseTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void {
		$this->subject = new TestObject(
			id: '//smol.blog/activitypub/outbox/' . $this->randomId(),
			randomProperty: 'hullo',
		);
	}

	public function testItDelegatesTheType() {
		$this->assertEquals('test', $this->subject->type());
	}

	public function testItOnlyRequiresAnId() {
		$this->assertInstanceOf(
			ActivityPubBase::class,
			new TestObject(id: '//smol.blog/athing'),
		);
	}

	public function testAdditionalPropertiesAreAccessable() {
		$this->assertEquals('hullo', $this->subject->randomProperty);
	}
}
