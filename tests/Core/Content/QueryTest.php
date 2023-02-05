<?php

namespace Smolblog\Core\Content;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\Queries\GenericContentById;
use Smolblog\Core\Content\Types\Status\StatusById;
use Smolblog\Framework\Objects\Identifier;

final class QueryTest extends TestCase {
	public function testGenericContentByIdCanBeInstantiated() {
		$this->assertInstanceOf(
			GenericContentById::class,
			new GenericContentById(id: Identifier::createRandom())
		);
	}

	public function testStatusByIdCanBeInstantiated() {
		$this->assertInstanceOf(
			StatusById::class,
			new StatusById(id: Identifier::createRandom())
		);
	}
}
