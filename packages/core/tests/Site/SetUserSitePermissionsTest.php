<?php

namespace Smolblog\Core\Site\Commands;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\EntityNotFound;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Core\Site\Events\UserSitePermissionsSet;
use Smolblog\Core\Test\SiteTestBase;

#[AllowMockObjectsWithoutExpectations]
final class SetUserSitePermissionsTest extends SiteTestBase {
	public function testHappyPath() {
		$command = new SetUserSitePermissions(
			siteId: $this->randomId(),
			linkedUserId: $this->randomId(),
			userId: $this->randomId(),
			level: SitePermissionLevel::Admin,
		);

		$this->repo->method('hasSiteWithId')->willReturn(true);
		$this->sitePerms->method('canManagePermissions')->willReturn(true);

		$this->expectEvent(new UserSitePermissionsSet(
			userId: $command->userId,
			aggregateId: $command->siteId,
			entityId: $command->linkedUserId,
			level: SitePermissionLevel::Admin,
		));

		$this->app->execute($command);
	}

	public function testItFailsIfPermissionsAreNotSet() {
		$command = new SetUserSitePermissions(
			siteId: $this->randomId(),
			linkedUserId: $this->randomId(),
			userId: $this->randomId(),
			level: SitePermissionLevel::Admin,
		);

		$this->repo->method('hasSiteWithId')->willReturn(true);
		$this->sitePerms->method('canManagePermissions')->willReturn(false);

		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheSiteDoesNotExist() {
		$command = new SetUserSitePermissions(
			siteId: $this->randomId(),
			linkedUserId: $this->randomId(),
			userId: $this->randomId(),
			level: SitePermissionLevel::Admin,
		);

		$this->repo->method('hasSiteWithId')->willReturn(false);
		$this->sitePerms->method('canManagePermissions')->willReturn(true);

		$this->expectException(EntityNotFound::class);

		$this->app->execute($command);
	}
}
