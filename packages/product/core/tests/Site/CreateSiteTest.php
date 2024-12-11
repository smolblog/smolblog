<?php

namespace Smolblog\Core\Site\Commands;

require_once __DIR__ . '/_base.php';

use Smolblog\Core\Site\Entities\Site;
use Smolblog\Core\Site\Events\SiteCreated;
use Smolblog\Foundation\Exceptions\CommandNotAuthorized;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Keypair;
use Smolblog\Test\SiteTestBase;

final class CreateSiteTest extends SiteTestBase {
	public function testHappyPath() {
		$expected = new Site(
			id: $this->randomId(),
			key: 'test',
			displayName: 'Test Site',
			userId: $this->randomId(),
			keypair: new Keypair(publicKey: '--PUBLIC-KEY--'),
			description: 'This is a drill.',
		);

		$this->keygen->method('generate')->willReturn($expected->keypair);
		$this->globalPerms->method('canCreateSite')->willReturn(true);
		$this->repo->method('hasSiteWithID')->willReturn(false);
		$this->repo->method('hasSiteWithKey')->willReturn(false);

		$event = new SiteCreated(
			userId: $expected->userId,
			aggregateId: $expected->id,
			key: 'test',
			displayName: 'Test Site',
			keypair: $expected->keypair,
			description: $expected->description,
			siteUserId: $expected->userId,
		);

		$this->expectEvent($event);
		$this->assertObjectEquals($expected, $event->getSiteObject());

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
			displayName: 'Test Site'
		);

		$this->repo->method('hasSiteWithId')->willReturn(true, true, false);
		$this->globalPerms->method('canCreateSite')->willReturn(true);

		$this->mockEventBus->expects($this->once())->method('dispatch')->with($this->isInstanceOf(SiteCreated::class));

		$this->app->execute($command);
	}
}
