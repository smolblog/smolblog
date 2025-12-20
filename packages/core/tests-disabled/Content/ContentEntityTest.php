<?php

namespace Smolblog\Core\Content\Entities;

require_once __DIR__ . '/_base.php';

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Test\TestCase;
use Smolblog\Test\TestDefaultContentType;

#[AllowMockObjectsWithoutExpectations]
final class ContentEntityTest extends TestCase {
	public function testItUsesTheTitleAndTypeFromTheBody() {
		$actual = new Content(
			body: new TestDefaultContentType(title: 'Hello', body: 'World'),
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);

		$this->assertEquals('Hello', $actual->title());
		$this->assertEquals('testdefault', $actual->type());
	}
}
