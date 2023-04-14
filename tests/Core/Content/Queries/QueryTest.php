<?php

namespace Smolblog\Core\Content\Queries;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Content\Types\Status\StatusById;
use Smolblog\Framework\Objects\Identifier;

final class QueryTest extends TestCase {
	public function testGenericContentByIdCanBeInstantiated() {
		$this->assertInstanceOf(
			GenericContentById::class,
			new GenericContentById(siteId: Identifier::createRandom(), contentId: Identifier::createRandom())
		);
	}

	public function testStatusByIdCanBeInstantiated() {
		$this->assertInstanceOf(
			StatusById::class,
			new StatusById(siteId: Identifier::createRandom(), contentId: Identifier::createRandom())
		);
	}
}
