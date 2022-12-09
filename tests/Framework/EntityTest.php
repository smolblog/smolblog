<?php

namespace Smolblog\Framework;

use PHPUnit\Framework\TestCase;

final readonly class EntityTestConstructor extends Entity {
	public function __construct(
		public Identifier $id,
		public string $name
	) {}
}

final readonly class EntityTestHidden extends Entity {
	const NAMESPACE = '23e83a58-c771-48f8-8b79-017b8d218af8';
	public function __construct(
		public string $name
	) {
		parent::__construct(id: Identifier::createFromName(
			namespace: self::NAMESPACE,
			name: $this->name
		));
	}
}

final class EntityTest extends TestCase {
	public function testASubclassCanBeCreated() {
		$test = new EntityTestConstructor(id: Identifier::createRandom(), name: 'Luke');

		$this->assertInstanceOf(Entity::class, $test);
	}

	public function testASubclassCanSetIdInItsConstructor() {
		$test = new EntityTestHidden(name: 'Luke');
		$alsoTest = new EntityTestHidden(name: 'Luke');

		$this->assertInstanceOf(Entity::class, $test);
		$this->assertEquals($alsoTest->id->toString(), $test->id->toString());
	}

	public function testTwoEntitiesWithTheSameClassAndIdHaveEqualStringRepresentation() {
		$ident = Identifier::createRandom();
		$test1 = new EntityTestConstructor(id: $ident, name: 'Arthur');
		$test2 = new EntityTestConstructor(id: $ident, name: 'Dent');

		$this->assertEquals(strval($test1), strval($test2));
	}

	public function testTwoEntitiesWithDifferentClassesHaveDifferentStringRepresentation() {
		$ident = Identifier::createFromName(
			namespace: EntityTestHidden::NAMESPACE,
			name: 'Arthur'
		);
		$test1 = new EntityTestConstructor(id: $ident, name: 'Arthur');
		$test2 = new EntityTestHidden(name: 'Arthur');

		$this->assertEquals($test1->id, $test2->id);
		$this->assertNotEquals(strval($test1), strval($test2));
	}

	public function testTwoEntitiesWithDifferentIdsHaveDifferentStringRepresentation() {
		$test1 = new EntityTestHidden(name: 'Arthur');
		$test2 = new EntityTestHidden(name: 'Dent');

		$this->assertNotEquals(strval($test1), strval($test2));
	}

	public function testWillSerializeIdAsString() {
		$test = new EntityTestHidden(name: 'Luke');
		$expected = '{"id": "2e44ffaf-a8b2-550f-bf4b-71a137009a6a", "name": "Luke"}';

		$this->assertJsonStringEqualsJsonString($expected, json_encode($test));
	}

	public function testWillUnserializeFromJsonWithIdAsString() {
		$json = '{"id": "2e44ffaf-a8b2-550f-bf4b-71a137009a6a", "name": "Luke"}';
		$result = EntityTestConstructor::jsonDeserialize($json);

		$this->assertEquals('2e44ffaf-a8b2-550f-bf4b-71a137009a6a', $result->id);
		$this->assertEquals('Luke', $result->name);
	}
}