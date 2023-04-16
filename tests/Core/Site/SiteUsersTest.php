<?php

namespace Smolblog\Core\Site;

use Smolblog\Test\TestCase;

class SiteUsersTest extends TestCase {
	public function testUserMustBeAuthorToSeeUsers() {
		$query = new SiteUsers(siteId: $this->randomId(), userId: $this->randomId());
		$auth = new UserHasPermissionForSite(siteId: $query->siteId, userId: $query->userId, mustBeAuthor: true);

		$this->assertEquals($auth, $query->getAuthorizationQuery());
	}
}
