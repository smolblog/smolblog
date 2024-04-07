<?php

namespace Smolblog\Core\ContentV1\Events;

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

	public function testItCorrectlyGivesTheContentId() {
		$expected = $this->randomId();
		$this->assertEquals($expected, (new SamplePublicContentEvent(
			contentId: $expected,
			userId: $this->randomId(),
			siteId: $this->randomId(),
		))->getContentId());
	}
}
