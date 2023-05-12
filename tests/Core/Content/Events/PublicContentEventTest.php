<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Test\TestCase;

final class SamplePublicContentEvent extends PublicContentEvent {}

final class PublicContentEventTest extends TestCase {
	public function testItHasAnEmptyPayload() {
		$this->assertEquals([], (new SamplePublicContentEvent(
			contentId: $this->randomId(),
			userId: $this->randomId(),
			siteId: $this->randomId(),
		))->getPayload());
	}
}
