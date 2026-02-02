<?php

namespace Smolblog\Core\Site\Commands;

use Cavatappi\Foundation\Exceptions\CommandNotAuthorized;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Smolblog\Core\Site\Entities\Site;
use Smolblog\Core\Site\Events\SiteCreated;
use Smolblog\Core\Test\SiteTestBase;

#[AllowMockObjectsWithoutExpectations]
final class CreateSiteTest extends SiteTestBase {
	public function testHappyPath() {
		$expected = new Site(
			id: $this->randomId(),
			key: 'test',
			displayName: 'Test Site',
			userId: $this->randomId(),
			description: 'This is a drill.',
		);

		$this->globalPerms->method('canCreateSite')->willReturn(true);
		$this->repo->method('hasSiteWithID')->willReturn(false);
		$this->repo->method('hasSiteWithKey')->willReturn(false);

		$event = new SiteCreated(
			userId: $expected->userId,
			aggregateId: $expected->id,
			key: 'test',
			displayName: 'Test Site',
			description: $expected->description,
			siteUserId: $expected->userId,
		);

		$this->expectEvent($event);
		$this->assertValueObjectEquals($expected, $event->getSiteObject());

		$this->app->execute(new CreateSite(
			userId: $expected->userId,
			key: 'test',
			displayName: 'Test Site',
			description: $expected->description,
			siteId: $expected->id,
		));
	}

	public function testItFailsIfTheGivenSiteIdExists() {
		$this->repo->method('hasSiteWithKey')->willReturn(false);
		$this->globalPerms->method('canCreateSite')->willReturn(true);

		$this->repo->method('hasSiteWithID')->willReturn(true);
		$this->expectException(InvalidValueProperties::class);

		$this->app->execute(new CreateSite(
			userId: $this->randomId(),
			key: 'test',
			displayName: 'Test Site',
			siteId: $this->randomId(),
		));
	}

	public function testItFailsIfTheGivenSiteKeyExists() {
		$this->repo->method('hasSiteWithID')->willReturn(false);
		$this->globalPerms->method('canCreateSite')->willReturn(true);

		$this->repo->method('hasSiteWithKey')->willReturn(true);
		$this->expectException(InvalidValueProperties::class);

		$this->app->execute(new CreateSite(
			userId: $this->randomId(),
			key: 'test',
			displayName: 'Test Site',
		));
	}

	public function testItFailsIfTheUserCannotCreateSite() {
		$this->repo->method('hasSiteWithID')->willReturn(false);
		$this->repo->method('hasSiteWithKey')->willReturn(false);

		$this->globalPerms->method('canCreateSite')->willReturn(false);
		$this->expectException(CommandNotAuthorized::class);

		$this->app->execute(new CreateSite(
			userId: $this->randomId(),
			key: 'test',
			displayName: 'Test Site',
		));
	}

	public function testItGeneratesANewIdThatDoesNotExist() {
		$command = new CreateSite(
			userId: $this->randomId(),
			key: 'test',
			displayName: 'Test Site',
		);

		$this->repo->method('hasSiteWithId')->willReturn(true, true, false);
		$this->globalPerms->method('canCreateSite')->willReturn(true);

		$this->expectEventOfType(SiteCreated::class);

		$this->app->execute($command);
	}
}
