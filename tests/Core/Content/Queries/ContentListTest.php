<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Test\TestCase;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Exceptions\InvalidMessageAttributesException;
use Smolblog\Framework\Objects\Identifier;

final class ContentListTest extends TestCase {
	public function testPageZeroWillThrowAnException() {
		$this->expectException(InvalidMessageAttributesException::class);

		new ContentList(siteId: $this->randomId(), page: 0);
	}

	public function testPageSizeZeroWillThrowAnException() {
		$this->expectException(InvalidMessageAttributesException::class);

		new ContentList(siteId: $this->randomId(), pageSize: 0);
	}

	public function testEmptyVisibilityStopsMessage() {
		$query = new ContentList(siteId: $this->randomId(), visibility: []);

		$this->assertTrue($query->isPropagationStopped());
		$this->assertEquals([], $query->results());
	}

	public function testEmptyTypesStopsMessage() {
		$query = new ContentList(siteId: $this->randomId(), types: []);

		$this->assertTrue($query->isPropagationStopped());
		$this->assertEquals([], $query->results());
	}

	public function testAnonymousQueryWithoutPublishedVisibilityStopsMessage() {
		$query = new ContentList(siteId: $this->randomId(), visibility: [ContentVisibility::Protected]);

		$this->assertTrue($query->isPropagationStopped());
		$this->assertEquals([], $query->results());
	}
}
