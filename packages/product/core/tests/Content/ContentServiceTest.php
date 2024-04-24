<?php

namespace Smolblog\Core\Content;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Test\Kits\ServiceTestKit;
use Smolblog\Test\TestCase;

#[CoversClass(ContentService::class)]
final class ContentServiceTest extends TestCase {
	use ServiceTestKit;

	protected function setUp(): void {
		$this->service = $this->setUpService(ContentService::class);
	}

	public function testItWorks() {
		$this->assertInstanceOf(ContentService::class, $this->service);
	}
}
