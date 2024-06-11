<?php

namespace Smolblog\Core\Site;

use Smolblog\Test\TestCase;

class GetSiteSettingsTest extends TestCase {
	public function testUserMustBeAdminToSeeSettings() {
		$query = new GetSiteSettings(siteId: $this->randomId(), userId: $this->randomId());
		$auth = new UserHasPermissionForSite(siteId: $query->siteId, userId: $query->userId, mustBeAdmin: true);

		$this->assertEquals($auth, $query->getAuthorizationQuery());
	}
}
