<?php

namespace Smolblog\Core\Site;

use Smolblog\Test\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class SiteUserLinkTest extends TestCase {
	public function testAnIdIsKnowableFromSiteAndUser() {
		$siteId = $this->randomId();
		$userId = $this->randomId();

		$this->assertEquals(
			SiteUserLink::buildId(siteId: $siteId, userId: $userId),
			(new SiteUserLink(
				siteId: $siteId,
				userId: $userId,
				isAdmin: false,
				isAuthor: true))->id
		);
	}
}
