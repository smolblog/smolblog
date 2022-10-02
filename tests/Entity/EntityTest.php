<?php

namespace Smolblog\Core\Entity;

use PHPUnit\Framework\TestCase;

final class EntityTestConstructor extends Entity {
	public function __construct(
		public readonly string|int $id,
		public readonly string $name
	) {}
}

final class EntityTestHidden extends Entity {
	public function __construct(
		public readonly string $name
	) {
		parent::__construct(id: $this->name);
	}
}

final class EntityTest extends TestCase {
	public function testASubclassCanBeCreated() {
		$test = new EntityTestConstructor(id: 5, name: 'Luke');

		$this->assertInstanceOf(Entity::class, $test);
	}

	public function testASubclassCanSetIdInItsConstructor() {
		$test = new EntityTestHidden(name: 'Luke');

		$this->assertInstanceOf(Entity::class, $test);
		$this->assertEquals('Luke', $test->id);
	}
}
