<?php

use Smolblog\Framework\Foundation\Value;
use Smolblog\Framework\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Foundation\Value\Fields\RandomIdentifier;
use Smolblog\Framework\Foundation\Value\Traits\Entity;
use Smolblog\Framework\Foundation\Value\Traits\EntityKit;

readonly class EntityTest extends Value implements Entity {
	use EntityKit;
	public function __construct(Identifier $id, public string $name) {
		$this->id = $id;
	}
}

describe('EntityKit::getId', function() {
	it('will correctly retrieve the ID', function() {
		$id = new RandomIdentifier();
		$entity = new EntityTest($id, 'test');

		expect($entity->id)->toMatchValue($id);
		expect($entity->getId())->toMatchValue($id);
	});
});
