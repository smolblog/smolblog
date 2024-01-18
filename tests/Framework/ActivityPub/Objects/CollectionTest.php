<?php

namespace Smolblog\Framework\ActivityPub\Objects;

use Smolblog\Test\SerializableTestKit;
use Smolblog\Test\TestCase;

final class CollectionTest extends TestCase {
	use SerializableTestKit;

	protected function setUp(): void {
		$this->subject = new Collection(
			id: '//smol.blog/'.$this->randomId().'./activitypub/followers',
			totalItems: 42,
			another: 'one',
		);
	}

	public function testTotalItemsIsOptional() {
		$actual = new Collection(id: '//smol.blog/something');

		$this->assertNull($actual->totalItems);
		$this->assertArrayNotHasKey('totalItems', $actual->toArray());
	}
}
