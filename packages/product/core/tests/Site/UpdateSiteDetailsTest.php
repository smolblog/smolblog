<?php

namespace Smolblog\Core\Site\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Site\Events\SiteDetailsUpdated;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\EntityNotFound;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Test\SiteTestBase;

final class UpdateSiteDetailsTest extends SiteTestBase {
	public function testHappyPath() {
		$command = new UpdateSiteDetails(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			displayName: 'New Site',
			description: 'I shoulda done this like a million years ago.',
			pictureId: $this->randomId(),
		);

		$this->repo->method('hasSiteWithId')->willReturn(true);
		$this->sitePerms->method('canManageSettings')->willReturn(true);

		$this->expectEvent(new SiteDetailsUpdated(
			userId: $command->userId,
			aggregateId: $command->siteId,
			displayName: 'New Site',
			description: 'I shoulda done this like a million years ago.',
			pictureId: $command->pictureId
		));

		$this->app->execute($command);
	}
	public function testItWillNotIncludeNullValues() {
		$command = new UpdateSiteDetails(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			displayName: 'New Site',
		);

		$this->repo->method('hasSiteWithId')->willReturn(true);
		$this->sitePerms->method('canManageSettings')->willReturn(true);

		$this->expectEvent(new SiteDetailsUpdated(
			userId: $command->userId,
			aggregateId: $command->siteId,
			displayName: 'New Site',
		));

		$this->app->execute($command);
	}

	public function testItFailsIfPermissionsAreNotSet() {
		$command = new UpdateSiteDetails(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			displayName: 'New Site',
			description: 'I shoulda done this like a million years ago.',
			pictureId: $this->randomId(),
		);

		$this->repo->method('hasSiteWithId')->willReturn(true);
		$this->sitePerms->method('canManageSettings')->willReturn(false);

		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute($command);
	}

	public function testItFailsIfTheSiteDoesNotExist() {
		$command = new UpdateSiteDetails(
			siteId: $this->randomId(),
			userId: $this->randomId(),
			displayName: 'New Site',
			description: 'I shoulda done this like a million years ago.',
			pictureId: $this->randomId(),
		);

		$this->repo->method('hasSiteWithId')->willReturn(false);
		$this->sitePerms->method('canManageSettings')->willReturn(true);

		$this->expectException(EntityNotFound::class);

		$this->app->execute($command);
	}

	public function testTheCommandRequiresAtLeastOneUpdatedValue() {
		$this->expectException(InvalidValueProperties::class);

		new UpdateSiteDetails(
			siteId: $this->randomId(),
			userId: $this->randomId(),
		);
	}

	public function testTheEventRequiresAtLeastOneUpdatedValue() {
		$this->expectException(InvalidValueProperties::class);

		new SiteDetailsUpdated(
			userId: $this->randomId(),
			aggregateId: $this->randomId(),
		);
	}
}
