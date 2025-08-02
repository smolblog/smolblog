<?php

namespace Smolblog\Foundation\Value\Attributes;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Test\TestCase;

#[CoversClass(DisplayName::class)]
final class DisplayNameTest extends TestCase {
	public function testItCanBeInstantiatedAndStoreAString() {
		$actual = new DisplayName('Something Dot Com');

		$this->assertInstanceOf(DisplayName::class, $actual);
		$this->assertEquals('Something Dot Com', $actual->name);
	}
}
