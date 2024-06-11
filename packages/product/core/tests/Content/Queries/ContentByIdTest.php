<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Test\TestCase;

final class ContentByIdTest extends TestCase {
	public function testItProvidesSiteAndUserIdsAsGiven() {
		$siteId = $this->randomId();
		$userId = $this->randomId();
		$query = new ContentById(siteId: $siteId, id: $this->randomId(), userId: $userId);

		$this->assertEquals($siteId, $query->getSiteId());
		$this->assertEquals($userId, $query->getUserId());
	}
}
