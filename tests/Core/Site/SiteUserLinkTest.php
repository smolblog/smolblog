<?php

namespace Smolblog\Core\Site;

use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Objects\Identifier;

final class SiteUserLinkTest extends TestCase {
	public function testAnIdIsKnowableFromSiteAndUser() {
		$siteId = Identifier::createRandom();
		$userId = Identifier::createRandom();

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
