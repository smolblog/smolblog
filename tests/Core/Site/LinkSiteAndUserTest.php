<?php

namespace Smolblog\Core\Site;

use Smolblog\Test\TestCase;

class LinkSiteAndUserTest extends TestCase {
	public function testUserMustBeAdminToEditOthers() {
		$command = new LinkSiteAndUser(
			siteId: $this->randomId(),
			linkedUserId: $this->randomId(),
			commandUserId: $this->randomId(),
		);
		$auth = new UserHasPermissionForSite(
			siteId: $command->siteId,
			userId: $command->commandUserId,
			mustBeAdmin: true
		);

		$this->assertEquals($auth, $command->getAuthorizationQuery());
	}

	public function testUserMustBeAdminToSetAdmin() {
		$userId = $this->randomId();
		$command = new LinkSiteAndUser(
			siteId: $this->randomId(),
			linkedUserId: $userId,
			commandUserId: $userId,
			isAdmin: true,
		);
		$auth = new UserHasPermissionForSite(
			siteId: $command->siteId,
			userId: $command->commandUserId,
			mustBeAdmin: true
		);

		$this->assertEquals($auth, $command->getAuthorizationQuery());
	}

	public function testUserMustBeAuthorToSetOwnPerms() {
		$userId = $this->randomId();
		$command = new LinkSiteAndUser(
			siteId: $this->randomId(),
			linkedUserId: $userId,
			commandUserId: $userId,
		);
		$auth = new UserHasPermissionForSite(
			siteId: $command->siteId,
			userId: $command->commandUserId,
			mustBeAuthor: true
		);

		$this->assertEquals($auth, $command->getAuthorizationQuery());
	}
}
