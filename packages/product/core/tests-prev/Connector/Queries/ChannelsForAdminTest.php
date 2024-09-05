<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Test\TestCase;

class ChannelsForAdminTest extends TestCase {
	public function testUserMustBeAdminToSeeChannels() {
		$query = new ChannelsForAdmin(siteId: $this->randomId(), userId: $this->randomId());
		$auth = new UserHasPermissionForSite(siteId: $query->siteId, userId: $query->userId, mustBeAdmin: true);

		$this->assertEquals($auth, $query->getAuthorizationQuery());
	}
}
