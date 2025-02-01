<?php

namespace Smolblog\Core\Site\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Core\Site\Events\UserSitePermissionsSet;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\SiteTestBase;

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
