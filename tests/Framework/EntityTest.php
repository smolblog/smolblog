<?php

namespace Smolblog\Framework;

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

	public function testTwoEntitiesWithTheSameClassAndIdHaveEqualStringRepresentation() {
		$test1 = new EntityTestConstructor(id: 42, name: 'Arthur');
		$test2 = new EntityTestConstructor(id: 42, name: 'Dent');

		$this->assertEquals(strval($test1), strval($test2));
	}

	public function testTwoEntitiesWithDifferentClassesHaveDifferentStringRepresentation() {
		$test1 = new EntityTestConstructor(id: 'Arthur', name: 'Arthur');
		$test2 = new EntityTestHidden(name: 'Arthur');

		$this->assertNotEquals(strval($test1), strval($test2));
	}

	public function testTwoEntitiesWithDifferentIdsHaveDifferentStringRepresentation() {
		$test1 = new EntityTestHidden(name: 'Arthur');
		$test2 = new EntityTestHidden(name: 'Dent');

		$this->assertNotEquals(strval($test1), strval($test2));
	}
}
