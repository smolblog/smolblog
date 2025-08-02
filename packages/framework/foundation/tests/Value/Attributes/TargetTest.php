<?php

namespace Smolblog\Foundation\Value\Attributes;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Test\TestCase;

#[CoversClass(Target::class)]
final class TargetTest extends TestCase {
	public function testItCanBeInstantiatedAndStoreAClassString() {
		$actual = new Target(self::class);

		$this->assertInstanceOf(Target::class, $actual);
		$this->assertEquals(self::class, $actual->type);
	}
}
