<?php

namespace Smolblog\Core\Content\Queries;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class BaseContentByIdTest extends TestCase {
	public function testItIsAuthorizedByAContentVisibleToUserQuery() {
		$query = new class(
			siteId: Identifier::createRandom(),
			contentId: Identifier::createRandom(),
		) extends BaseContentById {};
		$auth = new ContentVisibleToUser(
			siteId: $query->siteId,
			contentId: $query->contentId,
			userId: null,
		);

		$this->assertEquals($auth, $query->getAuthorizationQuery());
	}
}
