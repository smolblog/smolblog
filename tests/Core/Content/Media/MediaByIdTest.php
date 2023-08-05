<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Core\Content\Queries\ContentVisibleToUser;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\TestCase;

final class MediaByIdTest extends TestCase {
	public function testItChecksForAccessWhenAnonymous() {
		$query = new MediaById(
			siteId: Identifier::fromString('fe5bd3c1-d286-4a4f-bb4d-92d23cab4ba2'),
			contentId: Identifier::fromString('b86891ed-c4f1-4b85-903d-0cd5a188b84d'),
		);
		$expected = new ContentVisibleToUser(
			siteId: Identifier::fromString('fe5bd3c1-d286-4a4f-bb4d-92d23cab4ba2'),
			contentId: Identifier::fromString('b86891ed-c4f1-4b85-903d-0cd5a188b84d'),
			userId: null,
		);

		$this->assertEquals($expected, $query->getAuthorizationQuery());
	}

	public function testItChecksForAccessWhenAuthenticated() {
		$query = new MediaById(
			siteId: Identifier::fromString('fe5bd3c1-d286-4a4f-bb4d-92d23cab4ba2'),
			contentId: Identifier::fromString('b86891ed-c4f1-4b85-903d-0cd5a188b84d'),
			userId: Identifier::fromString('1a6b4cb1-e026-45c0-af8f-9a8c4484f1d9'),
		);
		$expected = new ContentVisibleToUser(
			siteId: Identifier::fromString('fe5bd3c1-d286-4a4f-bb4d-92d23cab4ba2'),
			contentId: Identifier::fromString('b86891ed-c4f1-4b85-903d-0cd5a188b84d'),
			userId: Identifier::fromString('1a6b4cb1-e026-45c0-af8f-9a8c4484f1d9'),
		);

		$this->assertEquals($expected, $query->getAuthorizationQuery());
	}
}
