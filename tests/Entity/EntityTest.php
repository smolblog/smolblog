<?php

namespace Smolblog\Core\Entity;

use PHPUnit\Framework\TestCase;

final class EntityTestImplementation extends Entity {
	public function __construct(
		public readonly string|int $id,
		public readonly string $name
	) {}
}

final class EntityTest extends TestCase {
	public function testASubclassCanBeCreated() {
		$test = new EntityTestImplementation(id: 5, name: 'Luke');

		$this->assertInstanceOf(Entity::class, $test);
	}
}
