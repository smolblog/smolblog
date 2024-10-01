<?php

namespace Smolblog\Foundation\Exceptions;

use Smolblog\Test\TestCase;

final class ServiceNotRegisteredTest extends TestCase {
	public function testItCreatesADefaultMessage() {
		$actual = new ServiceNotRegistered(service: TestCase::class, registry: self::class);

		$this->assertStringContainsString(TestCase::class, $actual->getMessage());
		$this->assertStringContainsString(self::class, $actual->getMessage());
	}

	public function testItCanBeGivenAMessage() {
		$actual = new ServiceNotRegistered(
			service: TestCase::class,
			registry: self::class,
			message: 'This is a test message.',
		);

		$this->assertEquals('This is a test message.', $actual->getMessage());
	}
}
