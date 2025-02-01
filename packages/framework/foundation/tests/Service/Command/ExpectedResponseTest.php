<?php

namespace Smolblog\Foundation\Service\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Test\TestCase;

#[CoversClass(ExpectedResponse::class)]
final class ExpectedResponseTest extends TestCase {
	#[TestDox('ExpectedResponse can be instantiated.')]
	public function testItAll() {
		$this->assertInstanceOf(ExpectedResponse::class, new ExpectedResponse(type: self::class));
	}
}
