<?php

namespace Smolblog\Core\Content\Entities;

use Cavatappi\Test\TestCase;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Test\TestDefaultContentType;

#[AllowMockObjectsWithoutExpectations]
final class ContentEntityTest extends TestCase {
	public function testItUsesTheTitleAndTypeFromTheBody() {
		$actual = new Content(
			id: $this->randomId(),
			body: new TestDefaultContentType(title: 'Hello', body: 'World'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertEquals('Hello', $actual->title());
		$this->assertEquals('testdefault', $actual->type());
	}
}
