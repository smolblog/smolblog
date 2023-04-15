<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class BaseContentByIdTest extends TestCase {
	public function testItIsAuthorizedByAContentVisibleToUserQuery() {
		$query = new class(
			siteId: $this->randomId(),
			contentId: $this->randomId(),
		) extends BaseContentById {};
		$auth = new ContentVisibleToUser(
			siteId: $query->siteId,
			contentId: $query->contentId,
			userId: null,
		);

		$this->assertEquals($auth, $query->getAuthorizationQuery());
	}
}
