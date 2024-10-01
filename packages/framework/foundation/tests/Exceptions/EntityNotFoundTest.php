<?php

namespace Smolblog\Foundation\Exceptions;

use Smolblog\Test\TestCase;

final class EntityNotFoundTest extends TestCase {
	public function testItCreatesADefaultMessage() {
		$expectedId = $this->randomId();
		$actual = new EntityNotFound(entityId: $expectedId, entityName: self::class);

		$this->assertStringContainsString($expectedId, $actual->getMessage());
		$this->assertStringContainsString(self::class, $actual->getMessage());
	}

	public function testItCanBeGivenAMessage() {
		$actual = new EntityNotFound(
			entityId: $this->randomId(),
			entityName: self::class,
			message: 'This is a test message.',
		);

		$this->assertEquals('This is a test message.', $actual->getMessage());
	}
}
