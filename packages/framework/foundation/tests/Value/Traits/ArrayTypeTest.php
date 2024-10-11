<?php

namespace Smolblog\Foundation\Value\Traits;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Smolblog\Test\TestCase;

#[CoversClass(ArrayType::class)]
final class ArrayTypeTest extends TestCase {
	public function testItWillInstantiateCorrectly() {
		$actual = new ArrayType(self::class);
		$this->assertEquals(self::class, $actual->type);
		$this->assertFalse($actual->isBuiltIn());
	}

	public static function builtInTypes(): array {
		return [
			'string' => [ArrayType::TYPE_STRING],
			'int' => [ArrayType::TYPE_INTEGER],
			'bool' => [ArrayType::TYPE_BOOLEAN],
			'float' => [ArrayType::TYPE_FLOAT],
		];
	}

	#[DataProvider('builtInTypes')]
	#[TestDox('It will mark a $_dataName as a built-in type.')]
	public function testItWillMarkScalarTypesCorrectly(string $typeString) {
		$actual = new ArrayType($typeString);
		$this->assertTrue($actual->isBuiltIn());
	}
}
