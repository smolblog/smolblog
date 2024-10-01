<?php

namespace Smolblog\Foundation\Value\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use Smolblog\Test\TestCase;

#[CoversClass(ArrayType::class)]
final class ArrayTypeTest extends TestCase {
	public function testItWillInstantiateCorrectly() {
		$actual = new ArrayType(self::class);
		$this->assertEquals(self::class, $actual->type);
	}
}
