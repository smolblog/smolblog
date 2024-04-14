<?php

namespace Smolblog\Core\ContentV1\Queries;

use Smolblog\Test\TestCase;

final class ContentByPermalinkTest extends TestCase {
	public function testItProvidesSiteAndUserIdsAsGiven() {
		$siteId = $this->randomId();
		$userId = $this->randomId();
		$query = new ContentByPermalink(siteId: $siteId, permalink: '/bob', userId: $userId);

		$this->assertEquals($siteId, $query->getSiteId());
		$this->assertEquals($userId, $query->getUserId());
	}
}
