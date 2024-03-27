<?php
use Smolblog\Framework\Foundation\Values\Entity;
use Smolblog\Framework\Foundation\Values\Identifier;

readonly class EntityTest extends Entity {
	public function __construct(Identifier $id, public string $name) {
		parent::__construct($id);
	}
}

it('will serialize and deserialize correctly', function() {
	$entity = new EntityTest(Identifier::fromString('1d1413ca-33d8-4c2d-8029-ea41e38654cf'), 'test');

	$serialized = $entity->toArray();
	expect($serialized)->toMatchArray(['id' => '1d1413ca-33d8-4c2d-8029-ea41e38654cf', 'name' => 'test']);

	$deserialized = EntityTest::fromArray($serialized);
	expect($deserialized->id)->toMatchValue('1d1413ca-33d8-4c2d-8029-ea41e38654cf');
	expect($deserialized->name)->toBe('test');
});
