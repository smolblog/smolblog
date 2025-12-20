<?php

namespace Smolblog\Core\Content\Events;

use Cavatappi\Foundation\Factories\HttpMessageFactory;
use Cavatappi\Test\TestCase;

final class ContentCanonicalUrlSetTest extends TestCase {
	public function testTheEventCanBeCreated() {
		$event = new ContentCanonicalUrlSet(
			url: HttpMessageFactory::uri('https://smol.blog/test'),
			aggregateId: $this->randomId(),
			userId: $this->randomId(),
			entityId: $this->randomId(),
		);

		$this->assertInstanceOf(ContentCanonicalUrlSet::class, $event);
	}
}
