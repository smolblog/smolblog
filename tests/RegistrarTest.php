<?php

namespace Smolblog\Core;

use PHPUnit\Framework\TestCase;

final class RegistrarTestChildString {
	use Registrar;

	public static function register(string $object = null, string $withSlug = ''): void {
		static::addToRegistry(object: $object, slug: $withSlug);
	}

	public static function retrieve(string $slug = ''): ?string {
		return static::getFromRegistry(slug: $slug);
	}
}

final class RegistrarTestChildInt {
	use Registrar;

	public static function register(int $value = null, string $withSlug = ''): void {
		static::addToRegistry(object: $value, slug: $withSlug);
	}

	public static function retrieve(string $slug = ''): ?int {
		return static::getFromRegistry(slug: $slug);
	}
}

final class RegistrarTest extends TestCase {
	public function testSpecificRegistrarCanBeCreated() {
		$test = 'It\'s only a model.';

		RegistrarTestChildString::register(object: $test, withSlug: 'camelot');

		$this->assertEquals(RegistrarTestChildString::retrieve('camelot'), $test);
	}

	public function testMultipleRegistrarsAreIndependent() {
		$testString = 'It\'s only a model.';
		$testInt = 34;

		RegistrarTestChildString::register(object: $testString, withSlug: 'camelot');
		RegistrarTestChildInt::register(value: $testInt, withSlug: 'scene');

		$this->assertEquals(RegistrarTestChildString::retrieve('camelot'), $testString);
		$this->assertEquals(RegistrarTestChildInt::retrieve('scene'), $testInt);

		$this->assertNull(RegistrarTestChildString::retrieve('scene'));
		$this->assertNull(RegistrarTestChildInt::retrieve('camelot'));
	}
}
