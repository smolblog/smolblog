<?php

namespace Smolblog\Foundation\Value\Traits;

use PHPUnit\Framework\Attributes\CoversTrait;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;
use Smolblog\Test\TestCase;

readonly class ExampleEntity extends Value implements Entity {
	use EntityKit;
	public function __construct(Identifier $id, public string $name) {
		$this->id = $id;
	}
}

#[CoversTrait(EntityKit::class)]
final class EntityTest extends TestCase {
	public function testItWillCorrectlyRetrieveTheId() {
		$id = new RandomIdentifier();
		$entity = new ExampleEntity($id, 'test');

		$this->assertEquals($id, $entity->id);
		$this->assertEquals($id, $entity->getId());
	}
}
