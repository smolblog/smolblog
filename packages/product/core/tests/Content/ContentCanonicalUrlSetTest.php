<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Test\TestCase;

final class ContentCanonicalUrlSetTest extends TestCase {
	public function testTheEventCanBeCreated() {
		$event = new ContentCanonicalUrlSet(
			url: new Url('https://smol.blog/test'),
			aggregateId: $this->randomId(),
			userId: $this->randomId(),
			entityId: $this->randomId(),
		);

		$this->assertInstanceOf(ContentCanonicalUrlSet::class, $event);
	}
}
