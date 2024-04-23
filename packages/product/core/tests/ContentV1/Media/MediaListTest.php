<?php

namespace Smolblog\Core\ContentV1\Media;

use Smolblog\Framework\Exceptions\InvalidMessageAttributesException;
use Smolblog\Test\TestCase;

final class MediaListTest extends TestCase {
	public function testItWillFailIfPageIsNotPositive() {
		$this->expectException(InvalidMessageAttributesException::class);

		new MediaList(siteId: $this->randomId(), page: 0);
	}

	public function testItWillFailIfPageSizeIsNotPositive() {
		$this->expectException(InvalidMessageAttributesException::class);

		new MediaList(siteId: $this->randomId(), pageSize: 0);
	}

	public function testItWillShortCircuitIfTypesIsEmpty() {
		$query = new MediaList(siteId: $this->randomId(), types: []);

		$this->assertTrue($query->isPropagationStopped());
		$this->assertEmpty($query->results());
		$this->assertEquals(0, $query->count);
	}
}
